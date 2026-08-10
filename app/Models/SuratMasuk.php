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

    /**
     * Recompute status_disposisi based on the state of the whole disposisi tree.
     * - 'baru'     : belum ada disposisi sama sekali
     * - 'selesai'  : semua disposisi (di semua cabang) sudah selesai/ditolak
     * - 'diproses' : sudah ada disposisi tapi masih ada yang berjalan
     */
    public function refreshStatusDisposisi(): void
    {
        $all = $this->disposisi()->get();

        if ($all->isEmpty()) {
            $status = 'baru';
        } elseif ($all->every(fn (Disposisi $d) => in_array($d->status, ['selesai', 'ditolak']))) {
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
