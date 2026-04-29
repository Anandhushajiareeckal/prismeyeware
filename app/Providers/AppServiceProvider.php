<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
     *
     * Forces https:// for all asset() / route() / url() helpers when
     * the application is running behind an HTTPS reverse proxy (production).
     * This prevents Mixed Content errors that block QZ Tray's certificate
     * fetch and request-signing endpoint.
     */
    public function boot(): void
    {
        // Force https:// for all asset() / route() / url() helpers.
        // Covers two scenarios:
        //   1. APP_ENV=production  → always force https
        //   2. Behind an nginx/Apache HTTPS proxy that sets
        //      X-Forwarded-Proto: https (even when APP_ENV=local)
        if (
            config('app.env') === 'production'
            || request()->server('HTTP_X_FORWARDED_PROTO') === 'https'
        ) {
            URL::forceScheme('https');
        }
    }
}
