<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\EventWaitlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaitlistTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_join_waitlist_for_sold_out_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'max_attendees' => 10,
        ]);

        $ticketType = \App\Models\TicketType::factory()->create(['event_id' => $event->id]);

        // Fill all available tickets
        $booking = \App\Models\Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);
        $booking->items()->create(['quantity' => 10, 'price' => 50, 'ticket_type_id' => $ticketType->id]);

        $response = $this->actingAs($user)->post(route('waitlist.join', $event), [
            'name' => $user->name,
            'email' => $user->email,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('event_waitlist', [
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function user_cannot_join_waitlist_for_available_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'max_attendees' => 100,
        ]);

        $response = $this->actingAs($user)->post(route('waitlist.join', $event), [
            'name' => $user->name,
            'email' => $user->email,
            'quantity' => 1,
        ]);

        $response->assertStatus(302); // Should redirect back with error
        $response->assertSessionHasErrors();
    }

    #[Test]
    public function user_can_leave_waitlist()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $waitlist = EventWaitlist::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('waitlist.leave', $event));

        $this->assertDatabaseMissing('event_waitlist', [
            'id' => $waitlist->id,
        ]);
    }

    #[Test]
    public function organizer_can_view_waitlist_for_their_event()
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create(['organization_id' => $result['organization']->id]);

        EventWaitlist::factory()->count(5)->create(['event_id' => $event->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.waitlist.index', $event));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_cannot_join_waitlist_twice()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['max_attendees' => 1]);
        $ticketType = \App\Models\TicketType::factory()->create(['event_id' => $event->id]);

        // Fill event
        $booking = \App\Models\Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);
        $booking->items()->create(['quantity' => 1, 'price' => 50, 'ticket_type_id' => $ticketType->id]);

        // Join waitlist first time
        EventWaitlist::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'quantity' => 1,
        ]);

        // Try to join again
        $response = $this->actingAs($user)->post(route('waitlist.join', $event), [
            'name' => $user->name,
            'email' => $user->email,
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors();
    }
}


