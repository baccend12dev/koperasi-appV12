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

        // Register Dynamic Gate Permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->role && in_array(strtolower($user->role->name), ['super admin', 'superadmin'])) {
                return true;
            }
        });

        \Illuminate\Support\Facades\Gate::define('permission', function ($user, string $permissionName) {
            return $user->hasPermission($permissionName);
        });

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
                foreach (\App\Models\Permission::all() as $permission) {
                    \Illuminate\Support\Facades\Gate::define($permission->name, function ($user) use ($permission) {
                        return $user->hasPermission($permission->name);
                    });
                }
            }
        } catch (\Exception $e) {
            // Catch during initial migration setup
        }
    }
}
