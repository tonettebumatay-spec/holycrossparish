<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS (already there)
        URL::forceScheme('https');

        // Define the 'api' rate limiter used by throttle:api middleware
        RateLimiter::for('api', function ($job) {
            return Limit::perMinute(60)->by($job->user()?->id ?: $job->ip());
        });
    }
}