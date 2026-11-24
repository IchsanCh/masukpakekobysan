<?php

namespace App\Services;

use App\Models\ArsipKeputusanLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArsipKeputusanService
{
    /**
     * Tetapkan arsip permanen: status surat diubah dan keputusannya dicatat ke
     * log (siapa & kapan) dalam satu transaksi, jadi gak mungkin ada status
     * berubah tanpa jejak atau sebaliknya.
     */
    public function tetapkanPermanen(Model $surat, string $jenis, int $userId): ArsipKeputusanLog
    {
        return DB::transaction(function () use ($surat, $jenis, $userId) {
            $log = $this->catat($surat, $jenis, ArsipKeputusanLog::PERMANEN, $userId);

            $surat->update(['status_arsip' => 'permanen']);

            return $log;
        });
    }

    /**
     * Eksekusi pemusnahan beneran: catat ke log (snapshot data, karena baris
     * surat aslinya bakal hilang), hapus record DB-nya permanen, lalu hapus file
     * fisik. Dipanggil dari approve tunggal maupun bulk.
     *
     * File fisik dihapus SETELAH transaksi sukses: kalau DB gagal, file gak
     * ikut hilang dan log gak tersisa tanpa surat yang benar-benar terhapus.
     */
    public function musnahkan(Model $surat, string $jenis, int $userId): ArsipKeputusanLog
    {
        $file = $surat->file_surat;

        $log = DB::transaction(function () use ($surat, $jenis, $userId) {
            $log = $this->catat($surat, $jenis, ArsipKeputusanLog::MUSNAH, $userId);

            $surat->delete();

            return $log;
        });

        if ($file) {
            Storage::disk('public')->delete($file);
        }

        return $log;
    }

    private function catat(Model $surat, string $jenis, string $keputusan, int $userId): ArsipKeputusanLog
    {
        return ArsipKeputusanLog::create([
            'jenis' => $jenis,
            'keputusan' => $keputusan,
            'nomor_surat' => $surat->nomor_surat,
            'tanggal_surat' => $surat->tanggal_surat,
            'isi_ringkasan' => $surat->isi_ringkasan,
            'kode_klasifikasi' => $surat->referensiRetensi->kode_klasifikasi ?? null,
            'diputuskan_oleh' => $userId,
        ]);
    }
}