<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Authentication\Providers\AuthenticationServiceProvider;
use App\Modules\DocumentMetadata\Providers\DocumentMetadataServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register module service providers
        $this->app->register(AuthenticationServiceProvider::class);
        $this->app->register(DocumentMetadataServiceProvider::class);
    }

    public function boot(): void
    {
        //
    }
}