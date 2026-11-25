<?php

namespace App\Http\Controllers;

use App\Models\ReferensiRetensi;
use App\Models\SuratMasuk;
use App\Services\WhatsAppNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SuratMasukController extends Controller
{
    /**
     * Batas ukuran file surat, dalam KB (Laravel validation 'max' pakai KB).
     * 10 MB = 10240 KB.
     */
    private const MAX_FILE_KB = 10240;

    public function index(Request $request): View
    {
        $user = $request->user();

        // Agendaris & pimpinan: semua surat. Kabid/sekretariat/staf: hanya surat yang
        // didisposisikan kepadanya (atau ke bidangnya) — lihat SuratMasuk::scopeVisibleTo.
        $query = SuratMasuk::visibleTo($user)->with(['referensiRetensi', 'disposisi']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%")
                  ->orWhere('isi_ringkasan', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status_disposisi', $status);
        }

        $suratMasuks = $query->orderByDesc('tanggal_diterima')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $canManage = $user->canManageSuratMasuk();
        $canViewAll = $user->canViewAllSurat();

        return view('surat-masuk.index', compact('suratMasuks', 'canManage', 'canViewAll', 'user'));
    }

    public function create(): View
    {
        $retensis = ReferensiRetensi::orderBy('kode_klasifikasi')->get();

        return view('surat-masuk.create', compact('retensis'));
    }

    public function store(Request $request, WhatsAppNotifier $notifier): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        if ($request->hasFile('file_surat')) {
            $validated['file_surat'] = $request->file('file_surat')->store('surat-masuk', 'public');
        }

        $validated['dibuat_oleh'] = $request->user()->id;
        $validated['status_disposisi'] = 'baru';
        $validated['status_arsip'] = 'aktif';

        $suratMasuk = SuratMasuk::create($validated);

        $notifier->notifySuratMasukBaru($suratMasuk);

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil ditambahkan.');
    }

    public function edit(SuratMasuk $suratMasuk): View
    {
        $retensis = ReferensiRetensi::orderBy('kode_klasifikasi')->get();

        return view('surat-masuk.edit', compact('suratMasuk', 'retensis'));
    }

    public function update(Request $request, SuratMasuk $suratMasuk): RedirectResponse
    {
        $validated = $this->validateRequest($request, $suratMasuk->id);

        if ($request->hasFile('file_surat')) {
            if ($suratMasuk->file_surat) {
                Storage::disk('public')->delete($suratMasuk->file_surat);
            }
            $validated['file_surat'] = $request->file('file_surat')->store('surat-masuk', 'public');
        }

        $suratMasuk->update($validated);

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil diperbarui.');
    }

    public function destroy(SuratMasuk $suratMasuk): RedirectResponse
    {
        if ($suratMasuk->disposisi()->count() > 0) {
            return back()->with('error', 'Surat tidak bisa dihapus karena sudah memiliki disposisi.');
        }

        if ($suratMasuk->file_surat) {
            Storage::disk('public')->delete($suratMasuk->file_surat);
        }

        $suratMasuk->delete();

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil dihapus.');
    }

    private function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100',
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'required|date',
            'isi_ringkasan' => 'required|string',
            'pengirim' => 'required|string|max:255',
            'sifat_surat' => 'required|in:biasa,segera,sangat_segera,rahasia',
            'referensi_retensi_id' => 'required|exists:referensi_retensis,id',
            'file_surat' => ($ignoreId ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png|max:' . self::MAX_FILE_KB,
        ], [
            'referensi_retensi_id.required' => 'Referensi retensi wajib dipilih.',
            'file_surat.max' => 'Ukuran file maksimal ' . (self::MAX_FILE_KB / 1024) . ' MB.',
            'file_surat.mimes' => 'File harus berformat PDF, JPG, atau PNG.',
        ]);

        return $validated;
    }
}