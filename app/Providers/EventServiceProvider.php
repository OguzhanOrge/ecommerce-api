<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\UserLoggedIn;
use App\Listeners\LogUserLogin;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserLoggedIn::class => [
            LogUserLogin::class,
        ],
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
