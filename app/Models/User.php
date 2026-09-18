<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method bool canViewAllSurat()
 * @method bool canDisposisi()
 * @method bool hasRole(string $role)
 * @method bool hasAnyRole(array $roles)
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
        'unit_id',
        'peran',
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

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->peran === $role;
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->peran, $roles, true);
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
        return $this->hasAnyRole(['pimpinan', 'sekretariat', 'kabid', 'agendaris']);
    }

    /**
     * True kalau user ini agendaris — satu-satunya peran yang boleh membuat/meneruskan
     * disposisi "atas nama" pimpinan. Ini kemampuan OPSIONAL: agendaris tetap bisa
     * bertindak atas nama dirinya sendiri kalau mau, ini cuma pilihan tambahan.
     * Kabid/sekretariat/pimpinan tidak pernah dapat kemampuan ini.
     */
    public function canMewakiliPimpinan(): bool
    {
        return $this->hasRole('agendaris');
    }

    /**
     * Daftar pimpinan yang satu unit dengan user ini. Dipakai agendaris untuk
     * memilih "atas nama siapa" saat membuat/meneruskan disposisi.
     */
    public function pimpinanSeunit(): \Illuminate\Support\Collection
    {
        return self::where('unit_id', $this->unit_id)
            ->where('peran', 'pimpinan')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
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