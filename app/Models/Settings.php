<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Settings extends Model
{
    use HasFactory;

    private const CACHE_KEY = 'settings.all';

    protected $fillable = ['key', 'value', 'description'];

    /**
     * @return array<string, string>
     */
    public static function values(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, static fn (): array => self::query()
            ->pluck('value', 'key')
            ->all());
    }

    public static function get(string $key, $default = null)
    {
        return self::values()[$key] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        self::flushCache();
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(static function (): void {
            self::flushCache();
        });

        static::deleted(static function (): void {
            self::flushCache();
        });
    }
}
