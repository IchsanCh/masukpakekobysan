<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuration extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'description',
    ];

    /**
     * Get a config value by key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $config = Cache::rememberForever("config.{$key}", function () use ($key) {
            return static::where('key', $key)->first();
        });

        return $config?->value ?? $default;
    }

    /**
     * Set a config value by key.
     */
    public static function setValue(string $key, mixed $value): static
    {
        $config = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );

        Cache::forget("config.{$key}");

        return $config;
    }

    /**
     * Get all configs by group.
     */
    public static function getGroup(string $group): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('group', $group)->get();
    }

    /**
     * Get Fonnte token.
     */
    public static function fonnteToken(): ?string
    {
        return static::getValue('fonnte_token');
    }

    /**
     * Get WA template by tipe.
     */
    public static function waTemplate(string $tipe): ?string
    {
        return static::getValue("wa_template_{$tipe}");
    }
}