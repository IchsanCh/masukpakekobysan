<?php

namespace App\Http\Controllers;

use App\Console\Commands\CekMasaRetensiArsip;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArsipController extends Controller
{
    public function index(Request $request): View
    {
        $jenis = $request->input('jenis') === 'keluar' ? 'keluar' : 'masuk';

        $query = $jenis === 'keluar'
            ? SuratKeluar::with('referensiRetensi')
            : SuratMasuk::with('referensiRetensi');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('isi_ringkasan', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status_arsip')) {
            $query->where('status_arsip', $status);
        }

        $suratList = $query->orderByDesc('tanggal_surat')->paginate(15)->withQueryString();

        $perluDitinjauMasuk = SuratMasuk::where('status_arsip', 'perlu_ditinjau')->count();
        $perluDitinjauKeluar = SuratKeluar::where('status_arsip', 'perlu_ditinjau')->count();

        return view('arsip.index', compact('suratList', 'jenis', 'perluDitinjauMasuk', 'perluDitinjauKeluar'));
    }

    public function tetapkanMusnah(Request $request, string $jenis, int $id): RedirectResponse
    {
        $surat = $this->findSurat($jenis, $id);

        abort_unless($surat->status_arsip === 'perlu_ditinjau', 422, 'Surat ini bukan status perlu ditinjau.');

        CekMasaRetensiArsip::musnahkan(
            $surat,
            $jenis === 'keluar' ? 'surat keluar' : 'surat masuk',
            'manual hasil tinjauan oleh ' . $request->user()->name,
        );

        return redirect()->route('arsip.index', ['jenis' => $jenis])
            ->with('success', 'Arsip dimusnahkan.');
    }

    public function tetapkanPermanen(Request $request, string $jenis, int $id): RedirectResponse
    {
        $surat = $this->findSurat($jenis, $id);

        abort_unless($surat->status_arsip === 'perlu_ditinjau', 422, 'Surat ini bukan status perlu ditinjau.');

        $surat->update(['status_arsip' => 'permanen', 'nasib_akhir' => 'permanen']);

        return redirect()->route('arsip.index', ['jenis' => $jenis])
            ->with('success', 'Arsip ditetapkan permanen.');
    }

    private function findSurat(string $jenis, int $id): SuratMasuk|SuratKeluar
    {
        return $jenis === 'keluar' ? SuratKeluar::findOrFail($id) : SuratMasuk::findOrFail($id);
    }
}