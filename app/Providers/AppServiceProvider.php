<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Support\Facades\Event;
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

        // Guest group members have no app, email or device, so never notify them.
        Event::listen(NotificationSending::class, function (NotificationSending $event) {
            if ($event->notifiable instanceof User && $event->notifiable->is_guest) {
                return false;
            }
        });
    }
}
