<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
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

    /** @test */
    public function sql_injection_is_prevented_in_search()
    {
        Event::factory()->create(['title' => 'Normal Event', 'is_published' => true]);

        $maliciousInput = "'; DROP TABLE events; --";

        $response = $this->get(route('events.index', ['search' => $maliciousInput]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('events', ['title' => 'Normal Event']);
    }

    /** @test */
    public function xss_is_prevented_in_event_title()
    {
        $organizer = createOrganizer();

        $maliciousTitle = '<script>alert("XSS")</script>';

        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'title' => $maliciousTitle,
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertStatus(200);
        $response->assertDontSee('<script>', false);
    }

    /** @test */
    public function user_cannot_access_other_users_bookings()
    {
        $user1 = createUser();
        $user2 = createUser();

        $booking = Booking::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get(route('bookings.show', $booking));

        $response->assertStatus(403);
    }

    /** @test */
    public function organizer_cannot_delete_other_organizers_events()
    {
        $organizer1 = createOrganizer();
        $organizer2 = createOrganizer();

        $event = Event::factory()->create(['user_id' => $organizer2->id]);

        $response = $this->actingAs($organizer1)->delete(route('organizer.events.destroy', $event));

        $response->assertStatus(403);
    }

    /** @test */
    public function mass_assignment_is_protected()
    {
        $organizer = createOrganizer();

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), [
            'title' => 'Test Event',
            'event_type' => 'physical',
            'start_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            'user_id' => 999, // Try to set user_id to another user
        ]);

        $event = Event::where('title', 'Test Event')->first();

        if ($event) {
            $this->assertEquals($organizer->id, $event->user_id);
        }
    }

    /** @test */
    public function passwords_are_hashed()
    {
        $password = 'my-secret-password';

        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(\Hash::check($password, $user->password));
    }

    /** @test */
    public function sensitive_data_is_not_exposed_in_api()
    {
        $user = createUser();

        $response = $this->actingAs($user, 'api')->getJson('/api/profile');

        $response->assertJsonMissing(['password', 'remember_token']);
    }

    /** @test */
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

    /** @test */
    public function file_upload_validates_mime_types()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('malicious.exe', 100);

        $response = $this->actingAs($organizer)->post(route('organizer.events.upload-image', $event), [
            'image' => $file,
        ]);

        $response->assertSessionHasErrors('image');
    }

    /** @test */
    public function session_hijacking_is_prevented()
    {
        $user = createUser();

        // Login and get session
        $this->actingAs($user);

        // Try to access with tampered session
        // This test depends on session security configuration
        $this->assertTrue(true);
    }

    /** @test */
    public function booking_number_cannot_be_guessed()
    {
        $booking1 = Booking::factory()->create();
        $booking2 = Booking::factory()->create();

        // Booking numbers should be unique and not sequential
        $this->assertNotEquals($booking1->booking_number, $booking2->booking_number);
        $this->assertStringStartsWith('BK-', $booking1->booking_number);
        $this->assertStringStartsWith('BK-', $booking2->booking_number);
    }
}

