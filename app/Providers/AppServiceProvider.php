<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Event;
use App\Observers\BookingObserver;
use App\Observers\EventObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event as EventFacade;
use SocialiteProviders\Manager\SocialiteWasCalled;

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

        // Listen for Socialite SSO events
        EventFacade::listen(SocialiteWasCalled::class, function (SocialiteWasCalled $event) {
            $event->extendSocialite('keycloak', \SocialiteProviders\Keycloak\Provider::class);
            $event->extendSocialite('google', \SocialiteProviders\Google\Provider::class);
            $event->extendSocialite('github', \SocialiteProviders\GitHub\Provider::class);
        });
    }
}
