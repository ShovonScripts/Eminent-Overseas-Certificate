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

        Config::set('school', array_merge(Config::get('school', []), $settings));

        return $next($request);
    }
}
