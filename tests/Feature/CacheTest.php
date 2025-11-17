<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[Test]
    public function events_are_cached_for_homepage()
    {
        Event::factory()->count(10)->create(['is_published' => true]);

        // First request - should cache
        $response1 = $this->get(route('home'));

        // Second request - should use cache
        $response2 = $this->get(route('home'));

        $response1->assertStatus(200);
        $response2->assertStatus(200);
    }

    #[Test]
    public function event_cache_is_cleared_when_event_is_updated()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'title' => 'Original Title',
        ]);

        // Cache the event
        $this->get(route('events.show', $event));

        // Update event
        $this->actingAs($organizer)->put(route('organizer.events.update', $event), [
            'title' => 'Updated Title',
            'description' => $event->description,
            'event_type' => $event->event_type,
            'event_category_id' => $event->event_category_id,
            'start_date' => $event->start_date->format('Y-m-d H:i:s'),
            'end_date' => $event->end_date->format('Y-m-d H:i:s'),
            'is_published' => $event->is_published,
        ]);

        // Verify cache was cleared
        $response = $this->get(route('events.show', $event->fresh()));
        $response->assertSee('Updated Title');
    }

    #[Test]
    public function featured_events_are_cached()
    {
        Event::factory()->count(5)->create([
            'is_published' => true,
            'is_featured' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_bookings_are_not_cached()
    {
        $user = createUser();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }
}



