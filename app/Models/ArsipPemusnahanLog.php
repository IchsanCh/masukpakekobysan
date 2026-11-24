<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArsipPemusnahanLog extends Model
{
    protected $fillable = [
        'jenis',
        'surat_id',
        'nomor_surat',
        'tanggal_surat',
        'isi_ringkasan',
        'kode_klasifikasi',
        'disetujui_oleh',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}