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
        $this->markTestSkipped('API Routes sind noch nicht implementiert');

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
        $this->markTestSkipped('API Routes sind noch nicht implementiert');
    }

    #[Test]
    public function api_returns_event_categories()
    {
        $this->markTestSkipped('API Routes sind noch nicht implementiert');
    }

    #[Test]
    public function api_requires_authentication_for_protected_routes()
    {
        $this->markTestSkipped('API Routes sind noch nicht implementiert');
    }

    #[Test]
    public function authenticated_user_can_access_their_bookings_via_api()
    {
        $this->markTestSkipped('API Routes sind noch nicht implementiert');
    }

    #[Test]
    public function api_can_search_events()
    {
        $this->markTestSkipped('API Routes sind noch nicht implementiert');
    }

    #[Test]
    public function api_supports_pagination()
    {
        $this->markTestSkipped('API Routes sind noch nicht implementiert');
    }
}



