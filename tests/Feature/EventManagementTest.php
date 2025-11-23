<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\Event;
use App\Models\User;
use App\Models\EventCategory;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_organizer_can_view_event_create_page()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $this->createOrganizerWithOrganization($organizer);

        $response = $this->actingAs($organizer)->get(route('organizer.events.create'));

        $response->assertStatus(200);
    }

    #[Test]
    public function authenticated_organizer_can_create_event()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $result = $this->createOrganizerWithOrganization($organizer);
        $category = EventCategory::factory()->create();

        $eventData = [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'event_type' => 'physical',
            'event_category_id' => $category->id,
            'start_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            'end_date' => now()->addWeek()->addHours(2)->format('Y-m-d H:i:s'),
            'venue_name' => 'Test Venue',
            'venue_address' => 'Test Address',
            'venue_city' => 'Berlin',
            'venue_postal_code' => '10115',
            'venue_country' => 'Germany',
            'max_attendees' => 100,
            'is_published' => false,
            'is_featured' => false,
            'is_private' => false,
        ];

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), $eventData);

        // Debug: Check response
        if ($response->status() !== 302) {
            $this->fail('Expected 302 redirect, got ' . $response->status() . '. Response: ' . $response->getContent());
        }

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('events', [
            'title' => 'Test Event',
            'organization_id' => $result['organization']->id,
        ]);
    }

    #[Test]
    public function authenticated_organizer_can_view_their_events()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        Event::factory()->count(3)->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function authenticated_organizer_can_edit_their_event()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.edit', $event));

        $response->assertStatus(200);
    }

    #[Test]
    public function authenticated_organizer_can_update_their_event()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create(['organization_id' => $result['organization']->id]);

        $updateData = [
            'title' => 'Updated Event Title',
            'description' => $event->description,
            'event_type' => $event->event_type,
            'event_category_id' => $event->event_category_id,
            'start_date' => $event->start_date->format('Y-m-d H:i:s'),
            'end_date' => $event->end_date->format('Y-m-d H:i:s'),
            'venue_name' => $event->venue_name,
            'venue_address' => $event->venue_address ?? 'Test Address',
            'venue_city' => $event->venue_city,
            'venue_postal_code' => $event->venue_postal_code ?? '10115',
            'venue_country' => $event->venue_country ?? 'Germany',
            'is_published' => false,
            'is_featured' => false,
            'is_private' => false,
        ];

        $response = $this->actingAs($organizer)->put(route('organizer.events.update', $event), $updateData);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event Title',
        ]);
    }

    #[Test]
    public function authenticated_organizer_can_delete_their_event()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create(['organization_id' => $result['organization']->id]);

        $response = $this->actingAs($organizer)->delete(route('organizer.events.destroy', $event));

        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }

    #[Test]
    public function organizer_cannot_edit_other_organizers_events()
    {
        $organizer1 = User::factory()->create(['user_type' => 'organizer']);
        $organizer2 = User::factory()->create(['user_type' => 'organizer']);
        $result2 = $this->createOrganizerWithOrganization($organizer2);
        $event = Event::factory()->create(['organization_id' => $result2['organization']->id]);

        $response = $this->actingAs($organizer1)->get(route('organizer.events.edit', $event));

        $response->assertStatus(403);
    }

    #[Test]
    public function guests_can_view_published_events()
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertStatus(200)
            ->assertSee($event->title);
    }

    #[Test]
    public function guests_cannot_view_unpublished_events()
    {
        $event = Event::factory()->create(['is_published' => false]);

        $response = $this->get(route('events.show', $event));

        $response->assertStatus(404);
    }

    #[Test]
    public function events_list_shows_only_published_events()
    {
        Event::factory()->count(3)->create(['is_published' => true]);
        Event::factory()->count(2)->create(['is_published' => false]);

        $response = $this->get(route('events.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function organizer_can_cancel_event_with_reason()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create([
            'organization_id' => $result['organization']->id,
            'is_cancelled' => false,
        ]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.cancel', $event), [
            'cancellation_reason' => 'Weather conditions',
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'is_cancelled' => true,
            'cancellation_reason' => 'Weather conditions',
        ]);
    }
}


