<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Jejak keputusan akhir arsip: 'musnah' (surat dihapus permanen setelah approval)
 * atau 'permanen' (surat tetap ada, ditetapkan kekal). Berupa snapshot, bukan
 * relasi — identitas surat di sini cukup jenis + nomor_surat.
 * created_at = waktu keputusan dieksekusi.
 */
class ArsipKeputusanLog extends Model
{
    public const MUSNAH = 'musnah';

    public const PERMANEN = 'permanen';

    protected $fillable = [
        'jenis',
        'keputusan',
        'nomor_surat',
        'tanggal_surat',
        'isi_ringkasan',
        'kode_klasifikasi',
        'diputuskan_oleh',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    public function pemutus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }
}