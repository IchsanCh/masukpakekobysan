<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disposisi extends Model
{
    protected $fillable = [
        'surat_masuk_id',
        'parent_id',
        'dari_user_id',
        'tipe_tujuan',
        'unit_id',
        'kepada_user_id',
        'instruksi',
        'batas_waktu',
        'status',
        'alasan_ditolak',
        'dibaca_at',
        'diselesaikan_at',
    ];

    protected $casts = [
        'batas_waktu' => 'date',
        'dibaca_at' => 'datetime',
        'diselesaikan_at' => 'datetime',
    ];

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Disposisi::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'parent_id');
    }

    public function dariUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dari_user_id');
    }

    public function kepadaUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kepada_user_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'disposisi_id');
    }

    public function waNotifications(): HasMany
    {
        return $this->hasMany(WaNotification::class, 'disposisi_id');
    }
}
