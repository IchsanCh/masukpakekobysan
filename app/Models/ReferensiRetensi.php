<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferensiRetensi extends Model
{
    protected $fillable = [
        'kode_klasifikasi',
        'nama_kegiatan',
        'masa_aktif',
        'masa_inaktif',
        'nasib_akhir_default',
        'default_batas_waktu_hari',
    ];

    public function suratMasuk(): HasMany
    {
        return $this->hasMany(SuratMasuk::class, 'referensi_retensi_id');
    }

    public function suratKeluar(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'referensi_retensi_id');
    }
}
