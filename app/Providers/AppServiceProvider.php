<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\SchoolConfig;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('verify', static function (Request $request): Limit {
            return Limit::perMinute(30)->by($request->ip());
        });

        View::composer('*', static function ($view): void {
            $view->with('schoolConfig', SchoolConfig::get());
        });
    }
}
