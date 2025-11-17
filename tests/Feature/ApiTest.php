<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function api_returns_events_list()
    {
        Event::factory()->count(5)->create(['is_published' => true]);

        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'start_date', 'venue_city']
                ]
            ]);
    }

    #[Test]
    public function api_returns_single_event()
    {
        $event = Event::factory()->create(['is_published' => true]);

        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'description', 'start_date']
            ]);
    }

    #[Test]
    public function api_returns_event_categories()
    {
        \App\Models\EventCategory::factory()->count(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug']
                ]
            ]);
    }

    #[Test]
    public function api_requires_authentication_for_protected_routes()
    {
        $response = $this->postJson('/api/bookings', []);

        $response->assertStatus(401);
    }

    #[Test]
    public function authenticated_user_can_access_their_bookings_via_api()
    {
        $user = createUser();
        \App\Models\Booking::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->getJson('/api/bookings');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function api_can_search_events()
    {
        Event::factory()->create([
            'title' => 'Laravel Conference',
            'is_published' => true,
        ]);
        Event::factory()->create([
            'title' => 'PHP Summit',
            'is_published' => true,
        ]);

        $response = $this->getJson('/api/events?search=Laravel');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Laravel Conference']);
    }

    #[Test]
    public function api_supports_pagination()
    {
        Event::factory()->count(25)->create(['is_published' => true]);

        $response = $this->getJson('/api/events?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'total', 'per_page']
            ]);
    }
}



