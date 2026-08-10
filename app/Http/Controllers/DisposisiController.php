<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\SuratMasuk;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisposisiController extends Controller
{
    /**
     * Status berikutnya yang valid dari status sekarang.
     */
    private const TRANSITIONS = [
        'menunggu' => ['diterima', 'ditolak'],
        'diterima' => ['diproses', 'ditolak'],
        'diproses' => ['selesai', 'ditolak'],
        'selesai' => [],
        'ditolak' => [],
    ];

    /**
     * "Disposisi Saya" — daftar disposisi yang ditujukan ke user (lewat unit atau personal).
     */
    public function inbox(Request $request): View
    {
        $user = $request->user();
        $view = $request->input('view', 'diterima');

        if ($view === 'dikirim') {
            $query = Disposisi::where('dari_user_id', $user->id);
        } else {
            $unitIds = $user->units()->pluck('units.id');
            $query = Disposisi::where(function ($q) use ($user, $unitIds) {
                $q->where(function ($q2) use ($unitIds) {
                    $q2->where('tipe_tujuan', 'unit')->whereIn('unit_id', $unitIds);
                })->orWhere(function ($q2) use ($user) {
                    $q2->where('tipe_tujuan', 'personal')->where('kepada_user_id', $user->id);
                });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $disposisis = $query->with(['suratMasuk', 'dariUser', 'unit', 'kepadaUser'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('disposisi.inbox', compact('disposisis', 'view'));
    }

    /**
     * Halaman rantai disposisi untuk satu surat masuk.
     */
    public function show(SuratMasuk $suratMasuk): View
    {
        $user = auth()->user();

        abort_unless($this->canViewSurat($suratMasuk, $user), 403);

        $suratMasuk->load('referensiRetensi');

        $tree = Disposisi::where('surat_masuk_id', $suratMasuk->id)
            ->whereNull('parent_id')
            ->with(['dariUser', 'unit', 'kepadaUser', 'childrenRecursive'])
            ->orderBy('created_at')
            ->get();

        $units = Unit::where('is_active', true)->orderBy('nama_unit')->get();
        $usersForPersonal = User::where('is_active', true)->orderBy('name')->get();
        $canCreateRoot = $user->canDisposisi();

        return view('disposisi.show', compact('suratMasuk', 'tree', 'units', 'usersForPersonal', 'canCreateRoot'));
    }

    /**
     * Buat disposisi awal (root) dari surat masuk.
     */
    public function store(Request $request, SuratMasuk $suratMasuk): RedirectResponse
    {
        abort_unless($request->user()->canDisposisi(), 403, 'Hanya pimpinan, kabid, atau sekretariat yang bisa membuat disposisi.');

        $validated = $this->validateDisposisi($request);

        Disposisi::create([
            ...$validated,
            'surat_masuk_id' => $suratMasuk->id,
            'parent_id' => null,
            'dari_user_id' => $request->user()->id,
        ]);

        $suratMasuk->refreshStatusDisposisi();

        return redirect()->route('disposisi.show', $suratMasuk)
            ->with('success', 'Disposisi berhasil dibuat.');
    }

    /**
     * Teruskan (forward) disposisi yang sudah diterima ke unit/orang lain.
     */
    public function forward(Request $request, Disposisi $disposisi): RedirectResponse
    {
        $user = $request->user();

        abort_unless($disposisi->canBeActedBy($user), 403);
        abort_if($disposisi->isTerminal(), 422, 'Disposisi ini sudah selesai/ditolak, tidak bisa diteruskan lagi.');

        $validated = $this->validateDisposisi($request);

        Disposisi::create([
            ...$validated,
            'surat_masuk_id' => $disposisi->surat_masuk_id,
            'parent_id' => $disposisi->id,
            'dari_user_id' => $user->id,
        ]);

        // Kalau masih 'menunggu', anggap otomatis diproses begitu diteruskan.
        if ($disposisi->status === 'menunggu') {
            $disposisi->update(['status' => 'diproses', 'dibaca_at' => $disposisi->dibaca_at ?? now()]);
        }

        $disposisi->suratMasuk->refreshStatusDisposisi();

        return redirect()->route('disposisi.show', $disposisi->surat_masuk_id)
            ->with('success', 'Disposisi diteruskan.');
    }

    /**
     * Ubah status disposisi (diterima / diproses / selesai / ditolak).
     */
    public function updateStatus(Request $request, Disposisi $disposisi): RedirectResponse
    {
        $user = $request->user();

        abort_unless($disposisi->canBeActedBy($user), 403);

        $validated = $request->validate([
            'status' => 'required|in:diterima,diproses,selesai,ditolak',
            'alasan_ditolak' => 'required_if:status,ditolak|nullable|string',
        ]);

        $allowed = self::TRANSITIONS[$disposisi->status] ?? [];
        abort_unless(in_array($validated['status'], $allowed), 422, 'Transisi status tidak valid.');

        $update = ['status' => $validated['status']];

        if ($validated['status'] === 'diterima') {
            $update['dibaca_at'] = $disposisi->dibaca_at ?? now();
        }

        if ($validated['status'] === 'selesai') {
            $update['diselesaikan_at'] = now();
        }

        if ($validated['status'] === 'ditolak') {
            $update['alasan_ditolak'] = $validated['alasan_ditolak'];
        }

        $disposisi->update($update);
        $disposisi->suratMasuk->refreshStatusDisposisi();

        return redirect()->route('disposisi.show', $disposisi->surat_masuk_id)
            ->with('success', 'Status disposisi diperbarui.');
    }

    private function validateDisposisi(Request $request): array
    {
        $validated = $request->validate([
            'tipe_tujuan' => 'required|in:unit,personal',
            'unit_id' => 'required_if:tipe_tujuan,unit|nullable|exists:units,id',
            'kepada_user_id' => 'required_if:tipe_tujuan,personal|nullable|exists:users,id',
            'instruksi' => 'required|string',
            'batas_waktu' => 'nullable|date',
        ]);

        // Field yang tidak relevan dengan tipe_tujuan yang dipilih tetap dikosongkan.
        if ($validated['tipe_tujuan'] === 'unit') {
            $validated['kepada_user_id'] = null;
        } else {
            $validated['unit_id'] = null;
        }

        return $validated;
    }

    /**
     * Boleh lihat halaman rantai disposisi surat ini kalau: bisa lihat semua surat (agendaris/pimpinan),
     * atau dia bagian dari rantai disposisi surat ini (pengirim/penerima/anggota unit tujuan).
     */
    private function canViewSurat(SuratMasuk $suratMasuk, User $user): bool
    {
        if ($user->canViewAllSurat()) {
            return true;
        }

        $unitIds = $user->units()->pluck('units.id');

        return Disposisi::where('surat_masuk_id', $suratMasuk->id)
            ->where(function ($q) use ($user, $unitIds) {
                $q->where('dari_user_id', $user->id)
                  ->orWhere('kepada_user_id', $user->id)
                  ->orWhere(function ($q2) use ($unitIds) {
                      $q2->where('tipe_tujuan', 'unit')->whereIn('unit_id', $unitIds);
                  });
            })->exists();
    }
}
