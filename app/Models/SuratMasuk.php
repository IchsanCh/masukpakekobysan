<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratMasuk extends Model
{
    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tanggal_diterima',
        'isi_ringkasan',
        'pengirim',
        'sifat_surat',
        'file_surat',
        'referensi_retensi_id',
        'status_disposisi',
        'status_arsip',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_diterima' => 'date',
    ];

    /**
     * Batasi surat masuk sesuai hak lihat $user:
     * agendaris & pimpinan melihat semua; peran lain hanya surat yang punya
     * disposisi yang menyentuh dirinya (lihat Disposisi::scopeTerlihatOleh).
     * Rantai disposisi dari surat yang terlihat ditampilkan utuh (transparan).
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->canViewAllSurat()) {
            return $query;
        }

        return $query->whereHas('disposisi', fn (Builder $d) => $d->terlihatOleh($user));
    }

    public function isVisibleTo(User $user): bool
    {
        return static::query()->whereKey($this->getKey())->visibleTo($user)->exists();
    }

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

    /**
     * Recompute status_disposisi based on the state of the whole disposisi tree.
     * - 'baru'     : belum ada disposisi sama sekali
     * - 'selesai'  : semua disposisi (di semua cabang) sudah selesai
     * - 'diproses' : sudah ada disposisi tapi masih ada yang berjalan
     */
    public function refreshStatusDisposisi(): void
    {
        $all = $this->disposisi()->get();

        if ($all->isEmpty()) {
            $status = 'baru';
        } elseif ($all->every(fn (Disposisi $d) => $d->status === 'selesai')) {
            $status = 'selesai';
        } else {
            $status = 'diproses';
        }

        if ($status !== $this->status_disposisi) {
            $this->update(['status_disposisi' => $status]);
        }
    }

    public function waNotifications(): HasMany
    {
        return $this->hasMany(WaNotification::class, 'surat_masuk_id');
    }
}