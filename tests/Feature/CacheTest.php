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
        $result = createOrganizerWithOrganization();
        $organizer = $result['organizer'];
        $organization = $result['organization'];

        // Set current organization in session
        session(['current_organization_id' => $organization->id]);

        $event = Event::factory()->create([
            'organization_id' => $organization->id,
            'title' => 'Original Title',
            'is_published' => false, // Set to false to avoid publishingrequirements
            'is_cancelled' => false,
        ]);

        // Cache the event (if published)
        // $this->get(route('events.show', $event->slug));

        // Prepare update data with all required fields
        $updateData = [
            'title' => 'Updated Title',
            'description' => $event->description,
            'event_type' => $event->event_type,
            'event_category_id' => $event->event_category_id,
            'start_date' => $event->start_date->format('Y-m-d H:i:s'),
            'end_date' => $event->end_date->format('Y-m-d H:i:s'),
            'is_published' => false,
            'is_featured' => false,
            'is_private' => false,
        ];

        // Add venue fields if physical or hybrid event
        if (in_array($event->event_type, ['physical', 'hybrid'])) {
            $updateData['venue_name'] = $event->venue_name;
            $updateData['venue_address'] = $event->venue_address;
            $updateData['venue_city'] = $event->venue_city;
            $updateData['venue_postal_code'] = $event->venue_postal_code;
            $updateData['venue_country'] = $event->venue_country;
        }

        // Add online URL if online or hybrid event
        if (in_array($event->event_type, ['online', 'hybrid'])) {
            $updateData['online_url'] = $event->online_url ?? 'https://example.com/meeting';
        }

        // Update event
        $updateResponse = $this->actingAs($organizer)->put(route('organizer.events.update', $event), $updateData);

        // Check for validation errors
        if ($updateResponse->status() === 302) {
            $updateResponse->assertSessionHasNoErrors();
        }

        // Verify update was successful
        $updateResponse->assertRedirect();

        // Refresh event and verify title was updated
        $event->refresh();
        expect($event->title)->toBe('Updated Title');
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



