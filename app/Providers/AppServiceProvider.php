<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\SystemLog;
use App\Observers\BookingObserver;
use App\Observers\BookingObserverForBadges;
use App\Observers\EventFeedObserver;
use App\Observers\EventObserver;
use App\Observers\SystemLogObserver;
use App\Policies\BookingPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        // Policy-Registrierung für Booking (inkl. Gast-Support über nullable User)
        Gate::policy(Booking::class, BookingPolicy::class);

        Event::observe(EventObserver::class);
        Event::observe(EventFeedObserver::class);
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

        // Rate-Limiter für ATOM-Feed (60 Req/min + 500 Req/h pro IP)
        RateLimiter::for('atom-feed', function (Request $request) {
            return [
                Limit::perMinute(60)->by($request->ip()),
                Limit::perHour(500)->by($request->ip()),
            ];
        });
    }
}
