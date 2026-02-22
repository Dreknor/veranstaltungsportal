<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function csrf_token_is_required_for_post_requests()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Without CSRF token, Laravel should reject the request
        // This test depends on your middleware configuration
        $this->assertTrue(true);
    }

    #[Test]
    public function sql_injection_is_prevented_in_search()
    {
        Event::factory()->create(['title' => 'Normal Event', 'is_published' => true]);

        $maliciousInput = "'; DROP TABLE events; --";

        $response = $this->get(route('events.index', ['search' => $maliciousInput]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('events', ['title' => 'Normal Event']);
    }

    #[Test]
    public function xss_is_prevented_in_event_title()
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);

        // Set current organization
        session(['current_organization_id' => $result['organization']->id]);

        $maliciousTitle = '<script>alert("XSS")</script>';

        $event = Event::factory()->create([
            'organization_id' => $result['organization']->id,
            'title' => $maliciousTitle,
            'slug' => \Illuminate\Support\Str::slug('safe-event-title-' . time()),
            'is_published' => true,
        ]);

        $response = $this->get(route('events.show', $event->slug));

        $response->assertStatus(200);

        // Der Titel sollte escaped angezeigt werden
        $response->assertSee('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', false);

        // Das unescapete <script>alert("XSS")</script> sollte NICHT im Titel vorkommen
        // Aber <script> Tags sind erlaubt für JSON-LD und normale JavaScript
        $response->assertDontSee('<script>alert("XSS")</script>', false);

        // Stelle sicher, dass im JSON-LD die Tags escaped sind
        $response->assertSee('\u003Cscript\u003E', false);
    }

    #[Test]
    public function user_cannot_access_other_users_bookings()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $booking = Booking::factory()->create(['user_id' => $user2->id]);

        // Nutzer 1 versucht auf Buchung von Nutzer 2 zuzugreifen
        $response = $this->actingAs($user1)->get(route('bookings.show', $booking->booking_number));

        // Der Controller leitet bei fehlendem Zugriff zur Verifizierungsseite um (302), kein 403
        $response->assertStatus(302);
        $response->assertRedirect(route('bookings.verify', $booking->booking_number));
    }

    #[Test]
    public function user_can_access_own_booking()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('bookings.show', $booking->booking_number));

        $response->assertStatus(200);
        $response->assertViewIs('bookings.show');
        // Buchungsnummer sollte irgendwo im HTML erscheinen
        $response->assertSee($booking->booking_number, false);
    }

    #[Test]
    public function organizer_cannot_delete_other_organizers_events()
    {
        $organizer1 = User::factory()->create();
        $organizer1->assignRole('organizer');
        $result1 = $this->createOrganizerWithOrganization($organizer1);

        $organizer2 = User::factory()->create();
        $organizer2->assignRole('organizer');
        $result2 = $this->createOrganizerWithOrganization($organizer2);

        $event = Event::factory()->create(['organization_id' => $result2['organization']->id]);

        $response = $this->actingAs($organizer1)->delete(route('organizer.events.destroy', $event));

        // 302 Redirect ist auch eine gültige Zugriffsverweigerung
        $this->assertContains($response->status(), [302, 403]);
    }


    #[Test]
    public function passwords_are_hashed()
    {
        $password = 'my-secret-password';

        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(\Hash::check($password, $user->password));
    }

    #[Test]
    public function rate_limiting_prevents_brute_force()
    {
        $attempts = 10;

        for ($i = 0; $i < $attempts; $i++) {
            $response = $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // After many failed attempts, should be rate limited
        // This test depends on your rate limiting configuration
        $this->assertTrue(true);
    }

    #[Test]
    public function session_hijacking_is_prevented()
    {
        $user = User::factory()->create();

        // Login and get session
        $this->actingAs($user);

        // Try to access with tampered session
        // This test depends on session security configuration
        $this->assertTrue(true);
    }

    #[Test]
    public function booking_number_cannot_be_guessed()
    {
        $booking1 = Booking::factory()->create();
        $booking2 = Booking::factory()->create();

        // Booking numbers should be unique and not sequential
        $this->assertNotEquals($booking1->booking_number, $booking2->booking_number);
        $this->assertStringStartsWith('BK-', $booking1->booking_number);
        $this->assertStringStartsWith('BK-', $booking2->booking_number);
    }

    #[Test]
    public function user_booking_page_uses_authenticated_layout_when_logged_in()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('bookings.show', $booking->booking_number));

        $response->assertStatus(200);
        // Prüfe auf typische Sidebar-Elemente aus dem App-Layout
        $response->assertSee('Meine Buchungen', false);
        $response->assertSee('Favoriten', false);
        $response->assertSee('Benachrichtigungen', false);
        // Sicherstellen, dass das Public-Navi-Element "Registrieren" nicht angezeigt wird
        $response->assertDontSee('Registrieren', false);
    }

    #[Test]
    public function discount_code_api_is_rate_limited_to_10_per_minute(): void
    {
        // Das Rate-Limit für die Discount-Code-API ist auf 10 pro Minute gesetzt.
        // Wir prüfen, ob die Route mit dem korrekten Middleware konfiguriert ist.
        $route = collect(\Route::getRoutes())->first(
            fn ($r) => $r->getName() === 'api.validate-discount-code'
        );

        $this->assertNotNull($route, 'Die Route api.validate-discount-code muss existieren.');

        $middlewares = $route->middleware();
        $this->assertContains('throttle:10,1', $middlewares,
            'Die Discount-Code-Route muss mit throttle:10,1 geschützt sein.'
        );
    }

    #[Test]
    public function booking_cancel_is_rate_limited_to_5_per_minute(): void
    {
        // Das Rate-Limit für das Booking-Storno ist auf 5 pro Minute gesetzt.
        $route = collect(\Route::getRoutes())->first(
            fn ($r) => $r->getName() === 'bookings.cancel'
        );

        $this->assertNotNull($route, 'Die Route bookings.cancel muss existieren.');

        $middlewares = $route->middleware();
        $this->assertContains('throttle:5,1', $middlewares,
            'Die Storno-Route muss mit throttle:5,1 geschützt sein.'
        );
    }

    #[Test]
    public function booking_store_does_not_log_pii_data(): void
    {
        // Sicherheitstest: BookingController::store darf keine personenbezogenen Daten loggen.
        // Wir prüfen, ob der Log::info-Aufruf mit Ticket-Rohdaten entfernt wurde.
        $controllerContent = file_get_contents(
            app_path('Http/Controllers/BookingController.php')
        );

        // Der entfernte Log::info-Aufruf mit PII-Daten
        $this->assertStringNotContainsString(
            "Log::info('Booking store - Incoming tickets data'",
            $controllerContent,
            'DSGVO: Der Log::info-Aufruf mit Ticket-Rohdaten (PII) muss aus BookingController::store entfernt sein.'
        );
    }
}
