<?php

namespace App\Http\Controllers;

use App\Models\ReferensiRetensi;
use App\Models\SuratKeluar;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SuratKeluarController extends Controller
{
    /**
     * Batas ukuran file surat, dalam KB (Laravel validation 'max' pakai KB).
     */
    private const MAX_FILE_KB = 10240;

    public function index(Request $request): View
    {
        $user = $request->user();
        $query = SuratKeluar::with(['referensiRetensi', 'pembuat']);

        // Agendaris & pimpinan lihat semua surat keluar; selain itu cuma lihat punya sendiri
        // (tiap unit/orang bisa nyatet surat keluarnya masing-masing).
        if (! $user->canViewAllSurat()) {
            $query->where('dibuat_oleh', $user->id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('kepada', 'like', "%{$search}%")
                  ->orWhere('isi_ringkasan', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status_arsip')) {
            $query->where('status_arsip', $status);
        }

        $suratKeluars = $query->orderByDesc('tanggal_surat')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('surat-keluar.index', compact('suratKeluars'));
    }

    /**
     * Endpoint kecil buat form create: kasih saran nomor surat terbaru
     * begitu user ganti Tanggal Surat ke tahun lain.
     */
    public function nextNomor(Request $request): \Illuminate\Http\JsonResponse
    {
        $tahun = (int) $request->query('tahun', now()->year);

        return response()->json([
            'suggestion' => $this->buildSuggestedNomor($tahun),
        ]);
    }

    public function create(): View
    {
        $retensis = ReferensiRetensi::orderBy('kode_klasifikasi')->get();
        $suggestedNomor = $this->buildSuggestedNomor(now()->year);

        return view('surat-keluar.create', compact('retensis', 'suggestedNomor'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        // nomor_urut selalu dihitung ulang di sini berdasarkan tanggal_surat yang FINAL
        // disubmit (bukan dari saran yang tampil di form), biar tetap benar walau
        // usernya sempat ganti-ganti tanggal atau nomor_surat teksnya diedit manual.
        $tahun = \Illuminate\Support\Carbon::parse($validated['tanggal_surat'])->year;
        $validated['nomor_urut'] = $this->nextNomorUrut($tahun);

        if ($request->hasFile('file_surat')) {
            $validated['file_surat'] = $request->file('file_surat')->store('surat-keluar', 'public');
        }

        $validated['dibuat_oleh'] = $request->user()->id;

        SuratKeluar::create($validated);

        return redirect()->route('surat-keluar.index')
            ->with('success', 'Surat keluar berhasil ditambahkan.');
    }

    public function edit(Request $request, SuratKeluar $suratKeluar): View
    {
        $this->authorizeAccess($request->user(), $suratKeluar);

        $retensis = ReferensiRetensi::orderBy('kode_klasifikasi')->get();

        return view('surat-keluar.edit', compact('suratKeluar', 'retensis'));
    }

    public function update(Request $request, SuratKeluar $suratKeluar): RedirectResponse
    {
        $this->authorizeAccess($request->user(), $suratKeluar);

        $validated = $this->validateRequest($request, $suratKeluar->id);

        if ($request->hasFile('file_surat')) {
            if ($suratKeluar->file_surat) {
                Storage::disk('public')->delete($suratKeluar->file_surat);
            }
            $validated['file_surat'] = $request->file('file_surat')->store('surat-keluar', 'public');
        }

        $suratKeluar->update($validated);

        return redirect()->route('surat-keluar.index')
            ->with('success', 'Surat keluar berhasil diperbarui.');
    }

    public function destroy(Request $request, SuratKeluar $suratKeluar): RedirectResponse
    {
        $this->authorizeAccess($request->user(), $suratKeluar);

        if ($suratKeluar->file_surat) {
            Storage::disk('public')->delete($suratKeluar->file_surat);
        }

        $suratKeluar->delete();

        return redirect()->route('surat-keluar.index')
            ->with('success', 'Surat keluar berhasil dihapus.');
    }

    /**
     * Agendaris/pimpinan boleh kelola semua surat keluar; selain itu cuma yang dia buat sendiri.
     */
    private function authorizeAccess(User $user, SuratKeluar $suratKeluar): void
    {
        abort_unless($user->canViewAllSurat() || $suratKeluar->dibuat_oleh === $user->id, 403);
    }

    /**
     * Nomor urut berikutnya untuk tahun tertentu (reset ke 1 tiap ganti tahun),
     * dihitung dari nilai nomor_urut tertinggi yang sudah dipakai di tahun itu.
     */
    private function nextNomorUrut(int $tahun): int
    {
        return (SuratKeluar::whereYear('tanggal_surat', $tahun)->max('nomor_urut') ?? 0) + 1;
    }

    /**
     * Teks saran nomor surat (4 digit + tahun) yang di-pre-fill di form tambah.
     * Ini cuma saran tampilan — nomor_urut yang beneran kesimpen dihitung ulang
     * saat submit (lihat store()), dan teks nomor_surat tetap bebas diedit user.
     */
    private function buildSuggestedNomor(int $tahun): string
    {
        return sprintf('%04d/%d', $this->nextNomorUrut($tahun), $tahun);
    }

    private function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nomor_surat' => 'required|string|max:255|unique:surat_keluars,nomor_surat,' . $ignoreId,
            'tanggal_surat' => 'required|date',
            'isi_ringkasan' => 'required|string',
            'kepada' => 'required|string|max:255',
            'pengelola' => 'nullable|string|max:255',
            'lampiran' => 'nullable|string|max:255',
            'referensi_retensi_id' => 'nullable|exists:referensi_retensis,id',
            'retensi_tahun' => 'required|integer|min:0',
            'status_arsip' => 'required|in:aktif,inaktif,perlu_ditinjau,musnah,permanen',
            'nasib_akhir' => 'nullable|in:musnah,permanen,dinilai_kembali',
            'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:' . self::MAX_FILE_KB,
        ], [
            'file_surat.max' => 'Ukuran file maksimal ' . (self::MAX_FILE_KB / 1024) . ' MB.',
            'file_surat.mimes' => 'File harus berformat PDF, JPG, atau PNG.',
        ]);
    }
}