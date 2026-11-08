<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class LoadSettings
{
    public function handle(Request $request, Closure $next)
    {
        $settings = Settings::pluck('value', 'key')->toArray();
        $school = Config::get('school', []);
        $images = $school['images'] ?? [];

        Config::set('school', array_replace($school, [
            'name' => $settings['school_name'] ?? $school['name'],
            'address' => $settings['school_address'] ?? $school['address'],
            'phone' => $settings['school_phone'] ?? $school['phone'],
            'email' => $settings['school_email'] ?? $school['email'],
            'verify_base_url' => $settings['verify_base_url'] ?? $school['verify_base_url'],
            'number_prefix' => $settings['number_prefix'] ?? $school['number_prefix'],
            'images' => [
                'logo' => $settings['logo_path'] ?? $images['logo'],
                'background' => $settings['background_path'] ?? $images['background'],
                'seal' => $settings['seal_path'] ?? $images['seal'],
            ],
        ]));

        return $next($request);
    }
}
