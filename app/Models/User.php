<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method bool canViewAllSurat()
 * @method bool canDisposisi()
 * @method bool hasRole(string $role)
 * @method bool hasAnyRole(array $roles)
 * @method \App\Models\Unit|null primaryUnit()
 * @method array roles()
 */

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'no_wa',
        'jabatan_struktural',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'unit_user')
                    ->withPivot('peran', 'is_primary')
                    ->withTimestamps();
    }

    /**
     * Get the user's primary unit.
     */
    public function primaryUnit(): ?Unit
    {
        return $this->units()->wherePivot('is_primary', true)->first();
    }

    /**
     * Get all roles across all units.
     */
    public function roles(): array
    {
        return $this->units->pluck('pivot.peran')->unique()->values()->toArray();
    }

    /**
     * Check if user has a specific role in any unit.
     */
    public function hasRole(string $role): bool
    {
        return $this->units()->wherePivot('peran', $role)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->units()->wherePivotIn('peran', $roles)->exists();
    }

    /**
     * Check if user is agendaris or pimpinan (can see all surat).
     */
    public function canViewAllSurat(): bool
    {
        return $this->hasAnyRole(['agendaris', 'pimpinan']);
    }

    /**
     * Check if user can create disposisi.
     */
    public function canDisposisi(): bool
    {
        return $this->hasAnyRole(['pimpinan', 'sekretariat', 'kabid']);
    }

    public function suratMasukDibuat(): HasMany
    {
        return $this->hasMany(SuratMasuk::class, 'dibuat_oleh');
    }

    public function suratKeluarDibuat(): HasMany
    {
        return $this->hasMany(SuratKeluar::class, 'dibuat_oleh');
    }

    public function disposisiDikirim(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'dari_user_id');
    }

    public function disposisiDiterima(): HasMany
    {
        return $this->hasMany(Disposisi::class, 'kepada_user_id');
    }

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjut::class, 'user_id');
    }

    public function waNotifications(): HasMany
    {
        return $this->hasMany(WaNotification::class, 'user_id');
    }
}
