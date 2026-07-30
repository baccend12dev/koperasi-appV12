<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
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
        // Paksa skema HTTPS jika diakses melalui Cloudflare atau environment bukan local
        if (config('app.env') !== 'local' || request()->header('x-forwarded-proto') === 'https') {
             URL::forceScheme('https');
        }
    }
}
