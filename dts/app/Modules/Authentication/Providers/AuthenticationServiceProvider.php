<?php

namespace App\Modules\Authentication\Providers;

use Illuminate\Support\ServiceProvider;

class AuthenticationServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register module services
    }

    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        
        // Load views (if any module-specific views)
        // $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'authentication');
        
        // Load migrations (if any module-specific migrations)
        // $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
    }
}