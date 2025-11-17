<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organizer_can_view_check_in_page()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.check-in', $event));

        $response->assertStatus(200);
    }

    #[Test]
    public function organizer_can_check_in_attendee_with_qr_code()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);
        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'booking_number' => 'BK-TEST123',
        ]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.check-in.process', $event), [
            'booking_number' => 'BK-TEST123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'checked_in_at' => now(),
        ]);
    }

    #[Test]
    public function organizer_can_check_in_attendee_manually()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);
        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.check-in.manual', $event), [
            'booking_id' => $booking->id,
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
        ]);
    }

    #[Test]
    public function cannot_check_in_cancelled_booking()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);
        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'cancelled',
            'booking_number' => 'BK-CANCELLED',
        ]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.check-in.process', $event), [
            'booking_number' => 'BK-CANCELLED',
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function cannot_check_in_invalid_booking_number()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.check-in.process', $event), [
            'booking_number' => 'BK-INVALID',
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function organizer_can_view_check_in_statistics()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        Booking::factory()->count(5)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'checked_in_at' => now(),
        ]);

        Booking::factory()->count(3)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'checked_in_at' => null,
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.check-in.stats', $event));

        $response->assertStatus(200);
    }

    #[Test]
    public function organizer_cannot_check_in_for_other_organizers_event()
    {
        $organizer1 = createOrganizer();
        $organizer2 = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer2->id]);

        $response = $this->actingAs($organizer1)->get(route('organizer.events.check-in', $event));

        $response->assertStatus(403);
    }

    #[Test]
    public function booking_can_be_checked_in_only_once()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);
        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'booking_number' => 'BK-ONCE',
            'checked_in_at' => now(),
        ]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.check-in.process', $event), [
            'booking_number' => 'BK-ONCE',
        ]);

        $response->assertStatus(422);
    }
}



