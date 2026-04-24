<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'nama_unit',
        'singkatan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'unit_user')
                    ->withPivot('peran', 'is_primary')
                    ->withTimestamps();
    }

    public function disposisi(): HasMany
    {
        return $this->hasMany(Disposisi::class);
    }
}
