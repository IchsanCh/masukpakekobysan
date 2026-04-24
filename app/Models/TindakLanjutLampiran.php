<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjutLampiran extends Model
{
    protected $fillable = [
        'tindak_lanjut_id',
        'nama_file',
        'file_path',
        'mime_type',
        'ukuran_bytes',
    ];

    public function tindakLanjut(): BelongsTo
    {
        return $this->belongsTo(TindakLanjut::class, 'tindak_lanjut_id');
    }
}
