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

        // Activate the Observers
        Inventory::observe(AuditObserver::class);
        Printer::observe(AuditObserver::class);
        RequestItem::observe(AuditObserver::class);
    }
}
