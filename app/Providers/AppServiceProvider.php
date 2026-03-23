<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Models\Inventory;
use App\Models\Printer;
use App\Models\RequestItem;
use App\Observers\AuditObserver;

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
        // ADD THIS MISSING UMBRELLA GATE:
        \Illuminate\Support\Facades\Gate::define('manage-assets', function (\App\Models\User $user) {
            return $user->is_superuser ||
                $user->permissions->contains('name', 'manage-system') ||
                $user->permissions->contains('name', 'manage-printers');
        });

        // Your existing gates...
        \Illuminate\Support\Facades\Gate::define('manage-printers', function (\App\Models\User $user) {
            return $user->is_superuser || $user->permissions->contains('name', 'manage-printers');
        });

        \Illuminate\Support\Facades\Gate::define('manage-requests', function (\App\Models\User $user) {
            return $user->is_superuser || $user->permissions->contains('name', 'manage-requests');
        });

        \Illuminate\Support\Facades\Gate::define('manage-inventory', function (\App\Models\User $user) {
            return $user->is_superuser || $user->permissions->contains('name', 'manage-inventory');
        });

        \Illuminate\Support\Facades\Gate::define('manage-users', function (\App\Models\User $user) {
            return $user->is_superuser || $user->permissions->contains('name', 'manage-users');
        });

        \Illuminate\Support\Facades\Gate::define('manage-system', function (\App\Models\User $user) {
            return $user->is_superuser || $user->permissions->contains('name', 'manage-system');
        });

        // ADD THIS WITH YOUR OTHER GATES:
        \Illuminate\Support\Facades\Gate::define('submit-on-behalf', function (\App\Models\User $user) {
            return $user->is_superuser || $user->permissions->contains('name', 'submit-on-behalf');
        });
    }
}
