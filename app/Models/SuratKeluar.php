<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratKeluar extends Model
{
    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'isi_ringkasan',
        'kepada',
        'pengelola',
        'lampiran',
        'file_surat',
        'referensi_retensi_id',
        'retensi_tahun',
        'status_arsip',
        'nasib_akhir',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    public function referensiRetensi(): BelongsTo
    {
        return $this->belongsTo(ReferensiRetensi::class, 'referensi_retensi_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
