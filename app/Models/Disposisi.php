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

    /**
     * Sama seperti children(), tapi eager-load seluruh keturunannya secara rekursif
     * (dipakai buat render pohon disposisi penuh dalam 1 query bertingkat).
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with(['childrenRecursive', 'dariUser', 'unit', 'kepadaUser']);
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

    /**
     * Siapapun di unit tujuan (semua peran) bisa menerima/memproses/meneruskan
     * disposisi yang tipe_tujuan-nya 'unit'. Kalau tipe_tujuan 'personal',
     * hanya orang yang dituju yang boleh bertindak.
     */
    public function canBeActedBy(User $user): bool
    {
        if ($this->tipe_tujuan === 'personal') {
            return $this->kepada_user_id === $user->id;
        }

        return $this->unit_id !== null && $user->units()->where('units.id', $this->unit_id)->exists();
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, ['selesai', 'ditolak']);
    }
}
