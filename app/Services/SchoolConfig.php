<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Settings;

class SchoolConfig
{
    public static function get(): array
    {
        $config = config('school');

        $config['images']['logo'] = self::resolveImage('logo_path', $config['images']['logo']);
        $config['images']['background'] = self::resolveImage('background_path', $config['images']['background']);
        $config['images']['seal'] = self::resolveImage('seal_path', $config['images']['seal']);

        return $config;
    }

    private static function resolveImage(string $settingsKey, string $fallback): string
    {
        $dbPath = Settings::get($settingsKey);

        return $dbPath ?: $fallback;
    }
}
