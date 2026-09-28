<?php

namespace App\Services;

use App\Models\ArsipPemusnahanLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ArsipPemusnahanService
{
    /**
     * Eksekusi pemusnahan beneran: catat ke arsip_pemusnahan_logs (snapshot data,
     * karena baris surat aslinya bakal hilang), hapus file fisik, lalu hapus
     * record DB-nya permanen. Dipanggil dari approve tunggal maupun bulk.
     */
    public function musnahkan(Model $surat, string $jenis, int $userId, ?string $keterangan = null): ArsipPemusnahanLog
    {
        $log = ArsipPemusnahanLog::create([
            'jenis' => $jenis,
            'surat_id' => $surat->id,
            'nomor_surat' => $surat->nomor_surat,
            'tanggal_surat' => $surat->tanggal_surat,
            'isi_ringkasan' => $surat->isi_ringkasan,
            'kode_klasifikasi' => $surat->referensiRetensi->kode_klasifikasi ?? null,
            'disetujui_oleh' => $userId,
            'dimusnahkan_at' => now(),
            'keterangan' => $keterangan,
        ]);

        if ($surat->file_surat) {
            Storage::disk('public')->delete($surat->file_surat);
        }

        $surat->delete();

        return $log;
    }
}