<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Settings;

class SchoolConfig
{
    public static function get(): array
    {
        $config = config('school');
        $settings = Settings::values();

        $config['images']['logo'] = $settings['logo_path'] ?? ($config['images']['logo'] ?? null);
        $config['images']['background'] = $settings['background_path'] ?? ($config['images']['background'] ?? null);
        $config['images']['seal'] = $settings['seal_path'] ?? ($config['images']['seal'] ?? null);
        $config['images']['divider'] = $settings['divider_path'] ?? ($config['images']['divider'] ?? public_path('images/certificate/divider.png'));

        return $config;
    }
}
