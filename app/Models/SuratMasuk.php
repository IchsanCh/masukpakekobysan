<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratMasuk extends Model
{
    protected $fillable = [
        'nomor_agenda',
        'nomor_surat',
        'tanggal_surat',
        'tanggal_diterima',
        'isi_ringkasan',
        'pengirim',
        'sifat_surat',
        'file_surat',
        'referensi_retensi_id',
        'retensi_tahun',
        'status_disposisi',
        'status_arsip',
        'nasib_akhir',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_diterima' => 'date',
    ];

    public function referensiRetensi(): BelongsTo
    {
        return $this->belongsTo(ReferensiRetensi::class, 'referensi_retensi_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function disposisi(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'surat_masuk_id');
    }

    public function waNotifications(): HasMany
    {
        return $this->hasMany(WaNotification::class, 'surat_masuk_id');
    }
}
