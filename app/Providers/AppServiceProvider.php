<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

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
        // Only superusers can manage users
        Gate::define('manage-users', function (User $user) {
            return $user->is_superuser;
        });

        // Managing Printer Locations and Printers
        Gate::define('manage-assets', function ($user) {
            return $user->is_superuser || $user->hasPermission('manage-assets');
        });

        // Managing Inventory Stock and Thresholds
        Gate::define('manage-inventory', function ($user) {
            return $user->is_superuser || $user->hasPermission('manage-inventory');
        });
    }
}
