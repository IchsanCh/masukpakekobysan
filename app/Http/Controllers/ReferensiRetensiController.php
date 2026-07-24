<?php

namespace App\Http\Controllers;

use App\Models\ReferensiRetensi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferensiRetensiController extends Controller
{
    public function index(Request $request): View
    {
        $query = ReferensiRetensi::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_klasifikasi', 'like', "%{$search}%")
                  ->orWhere('nama_kegiatan', 'like', "%{$search}%");
            });
        }

        $retensis = $query->orderBy('kode_klasifikasi')->paginate(20)->withQueryString();

        $editRetensi = null;
        if ($editId = $request->input('edit')) {
            $editRetensi = ReferensiRetensi::find($editId);
        }

        return view('retensi.index', compact('retensis', 'editRetensi'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_klasifikasi'         => 'required|string|max:50|unique:referensi_retensis,kode_klasifikasi',
            'nama_kegiatan'            => 'required|string|max:255',
            'masa_aktif'               => 'required|integer|min:0',
            'masa_inaktif'             => 'required|integer|min:0',
            'nasib_akhir_default'      => 'required|in:musnah,permanen,dinilai_kembali',
            'keterangan_nasib_akhir'   => 'nullable|string|max:255',
            'default_batas_waktu_hari' => 'nullable|integer|min:1',
        ]);

        ReferensiRetensi::create($validated);

        return redirect()->route('retensi.index')
            ->with('success', 'Referensi retensi berhasil ditambahkan.');
    }

    public function update(Request $request, ReferensiRetensi $retensi): RedirectResponse
    {
        $validated = $request->validate([
            'kode_klasifikasi'         => 'required|string|max:50|unique:referensi_retensis,kode_klasifikasi,' . $retensi->id,
            'nama_kegiatan'            => 'required|string|max:255',
            'masa_aktif'               => 'required|integer|min:0',
            'masa_inaktif'             => 'required|integer|min:0',
            'nasib_akhir_default'      => 'required|in:musnah,permanen,dinilai_kembali',
            'keterangan_nasib_akhir'   => 'nullable|string|max:255',
            'default_batas_waktu_hari' => 'nullable|integer|min:1',
        ]);

        $retensi->update($validated);

        return redirect()->route('retensi.index')
            ->with('success', 'Referensi retensi berhasil diperbarui.');
    }

    public function destroy(ReferensiRetensi $retensi): RedirectResponse
    {
        if ($retensi->suratMasuk()->count() > 0 || $retensi->suratKeluar()->count() > 0) {
            return back()->with('error', 'Retensi tidak bisa dihapus karena masih digunakan oleh surat.');
        }

        $retensi->delete();

        return redirect()->route('retensi.index')
            ->with('success', 'Referensi retensi berhasil dihapus.');
    }
}
