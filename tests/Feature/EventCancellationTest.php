<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCancellationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function organizer_can_cancel_event()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'is_cancelled' => false,
        ]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.cancel', $event), [
            'cancellation_reason' => 'Unexpected circumstances',
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'is_cancelled' => true,
            'cancellation_reason' => 'Unexpected circumstances',
        ]);
        $this->assertNotNull($event->fresh()->cancelled_at);
    }

    /** @test */
    public function cancelling_event_notifies_all_attendees()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        // Create multiple bookings
        $bookings = Booking::factory()->count(3)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.cancel', $event), [
            'cancellation_reason' => 'Weather conditions',
        ]);

        // All bookings should be cancelled
        foreach ($bookings as $booking) {
            $this->assertEquals('cancelled', $booking->fresh()->status);
        }
    }

    /** @test */
    public function cancelled_event_cannot_accept_new_bookings()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'is_cancelled' => true,
        ]);

        $response = $this->actingAs($user)->get(route('bookings.create', $event));

        $response->assertStatus(403);
    }

    /** @test */
    public function cancelled_event_shows_cancellation_message()
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'is_cancelled' => true,
            'cancellation_reason' => 'Due to weather',
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertSee('Due to weather');
    }

    /** @test */
    public function organizer_can_provide_refund_for_cancelled_event()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.cancel', $event), [
            'cancellation_reason' => 'Refund all attendees',
            'refund_attendees' => true,
        ]);

        $response->assertStatus(302);
    }
}

