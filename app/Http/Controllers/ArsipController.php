<?php

namespace App\Http\Controllers;

use App\Models\ArsipKeputusanLog;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Services\ArsipKeputusanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ArsipController extends Controller
{
    public function index(Request $request): View
    {
        $jenis = $request->input('jenis') === 'keluar' ? 'keluar' : 'masuk';
        $tab = in_array($request->input('tab'), ['tinjau', 'approval', 'semua', 'riwayat'], true)
            ? $request->input('tab')
            : 'tinjau';

        $suratList = $tab === 'riwayat'
            ? $this->queryRiwayat($request)
            : $this->querySurat($request, $jenis, $tab);

        $perluDitinjauMasuk = SuratMasuk::where('status_arsip', 'perlu_ditinjau')->count();
        $perluDitinjauKeluar = SuratKeluar::where('status_arsip', 'perlu_ditinjau')->count();
        $menungguMasuk = SuratMasuk::where('status_arsip', 'menunggu_musnah')->count();
        $menungguKeluar = SuratKeluar::where('status_arsip', 'menunggu_musnah')->count();

        return view('arsip.index', compact(
            'suratList', 'jenis', 'tab',
            'perluDitinjauMasuk', 'perluDitinjauKeluar',
            'menungguMasuk', 'menungguKeluar',
        ));
    }

    private function querySurat(Request $request, string $jenis, string $tab)
    {
        $query = $jenis === 'keluar'
            ? SuratKeluar::with('referensiRetensi')
            : SuratMasuk::with('referensiRetensi');

        if ($tab === 'tinjau') {
            $query->where('status_arsip', 'perlu_ditinjau');
        } elseif ($tab === 'approval') {
            $query->where('status_arsip', 'menunggu_musnah');
        } elseif ($status = $request->input('status_arsip')) {
            $query->where('status_arsip', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('isi_ringkasan', 'like', "%{$search}%");
            });
        }

        if ($dari = $request->input('tanggal_dari')) {
            $query->whereDate('tanggal_surat', '>=', $dari);
        }

        if ($sampai = $request->input('tanggal_sampai')) {
            $query->whereDate('tanggal_surat', '<=', $sampai);
        }

        return $query->orderByDesc('tanggal_surat')->paginate(15)->withQueryString();
    }

    private function queryRiwayat(Request $request)
    {
        return $this->riwayatQuery($request)->paginate(15)->withQueryString();
    }

    /**
     * Query riwayat keputusan (musnah + permanen) — dipakai tampilan tab Riwayat
     * DAN export CSV/PDF, supaya hasil export selalu sama dengan yang difilter di layar.
     * Surat yang sudah dimusnahkan dicari di sini (barisnya sudah tidak ada di tabel surat).
     */
    private function riwayatQuery(Request $request): Builder
    {
        $query = ArsipKeputusanLog::with('pemutus');

        if (in_array($jenis = $request->input('jenis_log'), ['masuk', 'keluar'], true)) {
            $query->where('jenis', $jenis);
        }

        if (in_array($keputusan = $request->input('keputusan_log'), [ArsipKeputusanLog::MUSNAH, ArsipKeputusanLog::PERMANEN], true)) {
            $query->where('keputusan', $keputusan);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('isi_ringkasan', 'like', "%{$search}%");
            });
        }

        if ($dari = $request->input('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $dari);
        }

        if ($sampai = $request->input('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $sampai);
        }

        return $query->orderByDesc('created_at');
    }

    /**
     * Dari tab "Perlu Ditinjau": agendaris mutusin nasib akhirnya PERMANEN.
     * Ini gak butuh approval terpisah — langsung final, tapi siapa yang
     * menetapkan & kapan dicatat di log keputusan.
     */
    public function tetapkanPermanen(Request $request, string $jenis, int $id, ArsipKeputusanService $service): RedirectResponse
    {
        $surat = $this->findSurat($jenis, $id);

        abort_unless($surat->status_arsip === 'perlu_ditinjau', 422, 'Surat ini bukan status perlu ditinjau.');

        $service->tetapkanPermanen($surat, $jenis, $request->user()->id);

        return redirect()->route('arsip.index', ['jenis' => $jenis, 'tab' => 'tinjau'])
            ->with('success', 'Arsip ditetapkan permanen.');
    }

    /**
     * Dari tab "Perlu Ditinjau": agendaris mutusin nasib akhirnya MUSNAH — tapi ini
     * CUMA nandain (masuk antrian menunggu_musnah), belum beneran dieksekusi.
     * Seragam sama jalur scheduler, harus lewat approve terpisah.
     */
    public function tetapkanMusnah(Request $request, string $jenis, int $id): RedirectResponse
    {
        $surat = $this->findSurat($jenis, $id);

        abort_unless($surat->status_arsip === 'perlu_ditinjau', 422, 'Surat ini bukan status perlu ditinjau.');

        $surat->update(['status_arsip' => 'menunggu_musnah']);

        return redirect()->route('arsip.index', ['jenis' => $jenis, 'tab' => 'tinjau'])
            ->with('success', 'Surat ditandai untuk dimusnahkan, menunggu persetujuan.');
    }

    /**
     * Approve + eksekusi pemusnahan SATU surat dari tab "Menunggu Persetujuan".
     */
    public function approveMusnah(Request $request, string $jenis, int $id, ArsipKeputusanService $service): RedirectResponse
    {
        $surat = $this->findSurat($jenis, $id);

        abort_unless($surat->status_arsip === 'menunggu_musnah', 422, 'Surat ini bukan status menunggu persetujuan.');

        $service->musnahkan($surat, $jenis, $request->user()->id);

        return redirect()->route('arsip.index', ['jenis' => $jenis, 'tab' => 'approval'])
            ->with('success', 'Arsip disetujui dan dimusnahkan.');
    }

    /**
     * Approve + eksekusi pemusnahan BANYAK surat sekaligus dari tab "Menunggu Persetujuan".
     * Payload: items[] = ["masuk:12", "keluar:7", ...] (jenis:id per baris yang dicentang).
     */
    public function bulkApprove(Request $request, ArsipKeputusanService $service): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'string',
        ]);

        $berhasil = 0;

        foreach ($validated['items'] as $item) {
            [$jenis, $id] = array_pad(explode(':', $item, 2), 2, null);

            if (! in_array($jenis, ['masuk', 'keluar'], true) || ! $id) {
                continue;
            }

            $surat = $jenis === 'keluar' ? SuratKeluar::find($id) : SuratMasuk::find($id);

            if (! $surat || $surat->status_arsip !== 'menunggu_musnah') {
                continue;
            }

            $service->musnahkan($surat, $jenis, $request->user()->id);
            $berhasil++;
        }

        return redirect()->route('arsip.index', ['tab' => 'approval'])
            ->with('success', "{$berhasil} arsip berhasil disetujui dan dimusnahkan.");
    }

    public function exportCsv(Request $request): Response
    {
        $tab = $request->input('tab', 'riwayat');
        $rows = $tab === 'riwayat'
            ? $this->rowsRiwayatUntukExport($request)
            : $this->rowsSuratUntukExport($request);

        $filename = 'arsip-' . $tab . '-' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_keys($rows[0] ?? ['Kosong' => '']));
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportPdf(Request $request): \Illuminate\Http\Response
    {
        $tab = $request->input('tab', 'riwayat');
        $rows = $tab === 'riwayat'
            ? $this->rowsRiwayatUntukExport($request)
            : $this->rowsSuratUntukExport($request);

        $pdf = Pdf::loadView('arsip.pdf-export', [
            'rows' => $rows,
            'tab' => $tab,
            'tanggalDari' => $request->input('tanggal_dari'),
            'tanggalSampai' => $request->input('tanggal_sampai'),
            'keputusan' => in_array($request->input('keputusan_log'), [ArsipKeputusanLog::MUSNAH, ArsipKeputusanLog::PERMANEN], true)
                ? $request->input('keputusan_log')
                : null,
            'dicetakOleh' => $request->user()->name,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('arsip-' . $tab . '-' . now()->format('Ymd_His') . '.pdf');
    }

    private function rowsSuratUntukExport(Request $request): array
    {
        $jenis = $request->input('jenis') === 'keluar' ? 'keluar' : 'masuk';
        $tabDalam = in_array($request->input('tab'), ['tinjau', 'approval'], true) ? $request->input('tab') : null;

        $query = $jenis === 'keluar'
            ? SuratKeluar::with('referensiRetensi')
            : SuratMasuk::with('referensiRetensi');

        if ($tabDalam === 'tinjau') {
            $query->where('status_arsip', 'perlu_ditinjau');
        } elseif ($tabDalam === 'approval') {
            $query->where('status_arsip', 'menunggu_musnah');
        } elseif ($status = $request->input('status_arsip')) {
            $query->where('status_arsip', $status);
        }

        if ($dari = $request->input('tanggal_dari')) {
            $query->whereDate('tanggal_surat', '>=', $dari);
        }

        if ($sampai = $request->input('tanggal_sampai')) {
            $query->whereDate('tanggal_surat', '<=', $sampai);
        }

        return $query->orderByDesc('tanggal_surat')->get()->map(fn ($s) => [
            'Nomor Surat' => $s->nomor_surat,
            'Tanggal' => $s->tanggal_surat->format('d-m-Y'),
            'Isi Ringkasan' => $s->isi_ringkasan,
            'Kode Klasifikasi' => $s->referensiRetensi->kode_klasifikasi ?? '-',
            'Status Arsip' => $s->status_arsip,
        ])->toArray();
    }

    private function rowsRiwayatUntukExport(Request $request): array
    {
        return $this->riwayatQuery($request)->get()->map(fn ($l) => [
            'Keputusan' => $l->keputusan === ArsipKeputusanLog::PERMANEN ? 'Ditetapkan Permanen' : 'Dimusnahkan',
            'Jenis' => $l->jenis,
            'Nomor Surat' => $l->nomor_surat,
            'Tanggal Surat' => $l->tanggal_surat->format('d-m-Y'),
            'Kode Klasifikasi' => $l->kode_klasifikasi ?? '-',
            'Waktu Keputusan' => $l->created_at->format('d-m-Y H:i'),
            'Diputuskan Oleh' => $l->pemutus->name ?? '-',
        ])->toArray();
    }

    private function findSurat(string $jenis, int $id): SuratMasuk|SuratKeluar
    {
        abort_unless(in_array($jenis, ['masuk', 'keluar'], true), 404);

        return $jenis === 'keluar' ? SuratKeluar::findOrFail($id) : SuratMasuk::findOrFail($id);
    }
}