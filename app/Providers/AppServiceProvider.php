<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\UserRegistered;
use App\Listeners\SendWelcomeEmail;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    protected $listen = [
        UserRegistered::class => [
            SendWelcomeEmail::class,
        ],
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Event::listen(
        //     UserRegistered::class,
        //     SendWelcomeEmail::class,
        // );
    }
}
