<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
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
     */
    public function boot(): void
    {
        if (request()->getHost() !== '127.0.0.1' && request()->getHost() !== 'localhost') {
            URL::forceScheme('https');
        }
        Schema::defaultStringLength(191);
        Vite::prefetch(concurrency: 3);
    }
}
