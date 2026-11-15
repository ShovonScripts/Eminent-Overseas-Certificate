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

        if (! empty($settings['school_name'])) {
            $config['name'] = $settings['school_name'];
        }
        if (! empty($settings['school_address'])) {
            $config['address'] = $settings['school_address'];
        }
        if (! empty($settings['school_phone'])) {
            $config['phone'] = $settings['school_phone'];
        }
        if (! empty($settings['school_email'])) {
            $config['email'] = $settings['school_email'];
        }
        if (! empty($settings['school_website'])) {
            $config['website'] = $settings['school_website'];
        }
        if (! empty($settings['verify_base_url'])) {
            $config['verify_base_url'] = $settings['verify_base_url'];
        }
        if (! empty($settings['number_prefix'])) {
            $config['number_prefix'] = $settings['number_prefix'];
        }

        $config['images']['logo'] = $settings['logo_path'] ?? ($config['images']['logo'] ?? null);
        $config['images']['background'] = $settings['background_path'] ?? ($config['images']['background'] ?? null);
        $config['images']['seal'] = $settings['seal_path'] ?? ($config['images']['seal'] ?? null);
        $config['images']['divider'] = $settings['divider_path'] ?? ($config['images']['divider'] ?? public_path('images/certificate/divider.png'));

        return $config;
    }
}
