<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\SuratMasuk;
use App\Models\Unit;
use App\Models\User;
use App\Services\WhatsAppNotifier;
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
            $query = Disposisi::where(function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('tipe_tujuan', 'unit')->where('unit_id', $user->unit_id);
                })->orWhere(function ($q2) use ($user) {
                    $q2->where('tipe_tujuan', 'personal')->where('kepada_user_id', $user->id);
                });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $disposisis = $query->with(['suratMasuk', 'dariUser', 'diinputOleh', 'unit', 'kepadaUser'])
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
            ->with([
                'dariUser', 'diinputOleh', 'unit', 'kepadaUser', 'childrenRecursive',
                'tindakLanjut', 'tindakLanjut.user', 'tindakLanjut.lampiran',
            ])
            ->orderBy('created_at')
            ->get();

        $units = Unit::where('is_active', true)->orderBy('nama_unit')->get();
        $usersForPersonal = User::where('is_active', true)
            ->where('id', '!=', $user->id)
            ->with('unit')
            ->orderBy('name')
            ->get();
        $pimpinanUntukRoot = $user->canMewakiliPimpinan() ? $user->pimpinanSeunit() : collect();
        $canCreateRoot = $user->canDisposisi();

        return view('disposisi.show', compact('suratMasuk', 'tree', 'units', 'usersForPersonal', 'canCreateRoot', 'pimpinanUntukRoot'));
    }

    /**
     * Buat disposisi awal (root) dari surat masuk.
     */
    public function store(Request $request, SuratMasuk $suratMasuk, WhatsAppNotifier $notifier): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->canDisposisi(), 403, 'Hanya pimpinan, kabid, sekretariat, atau agendaris (atas nama pimpinan) yang bisa membuat disposisi.');

        $validated = $this->validateDisposisi($request);
        [$dariUserId, $diinputOlehId] = $this->resolveActor($request, $user);

        $disposisi = Disposisi::create([
            ...$validated,
            'surat_masuk_id' => $suratMasuk->id,
            'parent_id' => null,
            'dari_user_id' => $dariUserId,
            'diinput_oleh_id' => $diinputOlehId,
        ]);

        $suratMasuk->refreshStatusDisposisi();

        $notifier->notifyDisposisi($disposisi);

        return redirect()->route('disposisi.show', $suratMasuk)
            ->with('success', 'Disposisi berhasil dibuat.');
    }

    /**
     * Teruskan (forward) disposisi yang sudah diterima ke unit/orang lain.
     */
    public function forward(Request $request, Disposisi $disposisi, WhatsAppNotifier $notifier): RedirectResponse
    {
        $user = $request->user();

        if ($disposisi->canBeActedBy($user)) {
            abort_unless($user->canDisposisi(), 403, 'Anda hanya bisa menerima/memproses disposisi ini, tidak berwenang meneruskannya ke pihak lain.');
            $bertindakSebagai = $user;
        } else {
            $bertindakSebagai = $disposisi->pimpinanDiwakiliOleh($user);
            abort_unless($bertindakSebagai !== null, 403);
        }

        abort_if($disposisi->isTerminal(), 422, 'Disposisi ini sudah selesai/ditolak, tidak bisa diteruskan lagi.');

        $validated = $this->validateDisposisi($request);

        $subDisposisi = Disposisi::create([
            ...$validated,
            'surat_masuk_id' => $disposisi->surat_masuk_id,
            'parent_id' => $disposisi->id,
            'dari_user_id' => $bertindakSebagai->id,
            'diinput_oleh_id' => $bertindakSebagai->id === $user->id ? null : $user->id,
        ]);

        // Kalau masih 'menunggu', anggap otomatis diproses begitu diteruskan.
        if ($disposisi->status === 'menunggu') {
            $disposisi->update(['status' => 'diproses', 'dibaca_at' => $disposisi->dibaca_at ?? now()]);
        }

        $disposisi->suratMasuk->refreshStatusDisposisi();

        $notifier->notifyDisposisi($subDisposisi);

        return redirect()->route('disposisi.show', $disposisi->surat_masuk_id)
            ->with('success', 'Disposisi diteruskan.');
    }

    /**
     * Ubah status disposisi (diterima / diproses / selesai / ditolak).
     */
    public function updateStatus(Request $request, Disposisi $disposisi, WhatsAppNotifier $notifier): RedirectResponse
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

        if ($validated['status'] === 'ditolak') {
            $notifier->notifyDisposisiDitolak($disposisi, $user);
        }

        if ($validated['status'] === 'selesai') {
            $notifier->notifyDisposisiSelesai($disposisi, $user, 'Ditandai selesai langsung tanpa catatan tindak lanjut.');
        }

        return redirect()->route('disposisi.show', $disposisi->surat_masuk_id)
            ->with('success', 'Status disposisi diperbarui.');
    }

    /**
     * Tentukan dari_user_id & diinput_oleh_id untuk disposisi ROOT (dari surat masuk).
     * Kalau $user bisa mewakili pimpinan (agendaris) DAN memilih "sebagai" = 'pimpinan',
     * validasi pilihan pimpinannya dan pastikan satu unit dengan dia. Selain itu
     * (termasuk agendaris yang memilih bertindak sendiri), bertindak sebagai dirinya sendiri.
     *
     * @return array{0: int, 1: int|null} [dari_user_id, diinput_oleh_id]
     */
    private function resolveActor(Request $request, User $user): array
    {
        $sebagai = $request->input('sebagai', 'diri_sendiri');

        if (! $user->canMewakiliPimpinan() || $sebagai !== 'pimpinan') {
            return [$user->id, null];
        }

        $validated = $request->validate([
            'atas_nama_pimpinan_id' => 'required|exists:users,id',
        ]);

        $pimpinanId = (int) $validated['atas_nama_pimpinan_id'];
        $valid = $user->pimpinanSeunit()->pluck('id')->contains($pimpinanId);

        abort_unless($valid, 403, 'Pimpinan yang dipilih tidak berada di unit yang sama dengan Anda.');

        return [$pimpinanId, $user->id];
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

        return Disposisi::where('surat_masuk_id', $suratMasuk->id)
            ->where(function ($q) use ($user) {
                $q->where('dari_user_id', $user->id)
                  ->orWhere('kepada_user_id', $user->id)
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('tipe_tujuan', 'unit')->where('unit_id', $user->unit_id);
                  });
            })->exists();
    }
}