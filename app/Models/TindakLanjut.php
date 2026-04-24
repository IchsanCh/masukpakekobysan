<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TindakLanjut extends Model
{
    protected $fillable = [
        'disposisi_id',
        'user_id',
        'keterangan',
        'status',
    ];

    public function disposisi(): BelongsTo
    {
        return $this->belongsTo(Disposisi::class, 'disposisi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(TindakLanjutLampiran::class, 'tindak_lanjut_id');
    }

    public function waNotifications(): HasMany
    {
        return $this->hasMany(WaNotification::class, 'tindak_lanjut_id');
    }
}
