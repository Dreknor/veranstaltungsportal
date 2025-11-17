<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\SystemLog;
use App\Observers\BookingObserver;
use App\Observers\BookingObserverForBadges;
use App\Observers\EventObserver;
use App\Observers\SystemLogObserver;
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
        SystemLog::observe(SystemLogObserver::class);
        Booking::observe(BookingObserver::class);
        Booking::observe(BookingObserverForBadges::class);
        Booking::observe(\App\Observers\BookingInvoiceObserver::class);
        \App\Models\PlatformFee::observe(\App\Observers\PlatformFeeInvoiceObserver::class);
        \App\Models\Organization::observe(\App\Observers\OrganizationObserver::class);

        // Listen for Socialite SSO events
        EventFacade::listen(SocialiteWasCalled::class, function (SocialiteWasCalled $event) {
            $event->extendSocialite('keycloak', \SocialiteProviders\Keycloak\Provider::class);
            $event->extendSocialite('google', \SocialiteProviders\Google\Provider::class);
            $event->extendSocialite('github', \SocialiteProviders\GitHub\Provider::class);
        });
    }
}
