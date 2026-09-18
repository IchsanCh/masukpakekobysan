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
        'diinput_oleh_id',
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
        'batas_waktu' => 'datetime',
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
        return $this->children()->with([
            'childrenRecursive', 'dariUser', 'diinputOleh', 'unit', 'kepadaUser',
            'tindakLanjut', 'tindakLanjut.user', 'tindakLanjut.lampiran',
        ]);
    }

    public function dariUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dari_user_id');
    }

    /**
     * Agendaris yang benar-benar menginput disposisi ini kalau ini hasil
     * "atas nama pimpinan" (null kalau bukan hasil proxy).
     */
    public function diinputOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diinput_oleh_id');
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

        return $this->unit_id !== null && $user->unit_id === $this->unit_id;
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, ['selesai', 'ditolak']);
    }

    /**
     * Kalau $user adalah agendaris dan disposisi ini ditujukan (personal) ke
     * salah satu pimpinan seunit dia, atau ditujukan ke unit tempat salah satu
     * pimpinan seunit dia berada, kembalikan pimpinan yang bisa diwakili itu.
     * Null kalau $user tidak berhak bertindak sebagai proxy di sini.
     */
    public function pimpinanDiwakiliOleh(User $user): ?User
    {
        if (! $user->canMewakiliPimpinan()) {
            return null;
        }

        $pimpinanSeunit = $user->pimpinanSeunit();

        if ($this->tipe_tujuan === 'personal') {
            return $pimpinanSeunit->firstWhere('id', $this->kepada_user_id);
        }

        // pimpinanSeunit() sudah pasti semuanya di unit yang sama dengan $user,
        // jadi tinggal cek disposisi ini memang ditujukan ke unit itu.
        return $this->unit_id === $user->unit_id ? $pimpinanSeunit->first() : null;
    }
}