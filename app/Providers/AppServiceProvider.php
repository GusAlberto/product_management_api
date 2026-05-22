<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Scramble::configure()
            ->expose(
                ui: '/docs/api',
                document: '/docs/api.json',
            );

        RateLimiter::for('products-api', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->user()?->id ?? $request->ip());
        });
    }
}
