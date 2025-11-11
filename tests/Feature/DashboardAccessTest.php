<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function guests_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function participant_sees_their_bookings_on_dashboard()
    {
        $user = User::factory()->create(['user_type' => 'participant']);
        $bookings = Booking::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function organizer_sees_their_events_on_dashboard()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $events = Event::factory()->count(3)->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function organizer_can_access_organizer_dashboard()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);

        $response = $this->actingAs($organizer)->get(route('organizer.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function participant_cannot_access_organizer_dashboard()
    {
        $participant = User::factory()->create(['user_type' => 'participant']);

        $response = $this->actingAs($participant)->get(route('organizer.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function dashboard_displays_upcoming_events()
    {
        $user = User::factory()->create();

        Event::factory()->count(3)->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }
}

