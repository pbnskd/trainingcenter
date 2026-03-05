<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate; // <--- IMPORTANT: Add this import

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
        Paginator::useBootstrap();

        // ---------------------------------------------------------------------
        // SUPER ADMIN BYPASS (MOVED HERE TO ENSURE IT RUNS)
        // ---------------------------------------------------------------------
        Gate::before(function ($user, $ability) {
            if ($user->hasRole(config('rbac.super_admin'))) {
                return true; 
            }
        });
    }
}