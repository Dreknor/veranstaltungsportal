<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Event;
use App\Observers\BookingObserver;
use App\Observers\EventObserver;
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
        Event::observe(EventObserver::class);
        Booking::observe(BookingObserver::class);
    }
}
