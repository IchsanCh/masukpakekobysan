<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaNotification extends Model
{
    protected $fillable = [
        'user_id',
        'no_wa_tujuan',
        'surat_masuk_id',
        'disposisi_id',
        'tindak_lanjut_id',
        'tipe',
        'pesan',
        'status_kirim',
        'terkirim_at',
    ];

    protected $casts = [
        'terkirim_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    public function disposisi(): BelongsTo
    {
        return $this->belongsTo(Disposisi::class, 'disposisi_id');
    }

    public function tindakLanjut(): BelongsTo
    {
        return $this->belongsTo(TindakLanjut::class, 'tindak_lanjut_id');
    }
}
