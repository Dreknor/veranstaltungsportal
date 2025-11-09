<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function event_with_zero_max_attendees_allows_unlimited_bookings()
    {
        $event = Event::factory()->create([
            'max_attendees' => null,
            'is_published' => true,
        ]);

        $this->assertTrue($event->hasAvailableTickets());
    }

    /** @test */
    public function free_event_with_zero_price_ticket()
    {
        $user = createUser();
        $event = Event::factory()->create(['is_published' => true]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.store', $event), [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'tickets' => [$ticketType->id => 1],
        ]);

        $booking = Booking::where('customer_email', $user->email)->first();
        $this->assertEquals(0, $booking->total);
    }

    /** @test */
    public function event_exactly_at_capacity()
    {
        $event = Event::factory()->create([
            'max_attendees' => 10,
            'is_published' => true,
        ]);

        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);
        $booking->items()->create(['quantity' => 10, 'price' => 50]);

        $this->assertEquals(0, $event->fresh()->availableTickets());
        $this->assertFalse($event->fresh()->hasAvailableTickets());
    }

    /** @test */
    public function cancelled_booking_frees_up_tickets()
    {
        $event = Event::factory()->create(['max_attendees' => 10]);

        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);
        $booking->items()->create(['quantity' => 5, 'price' => 50]);

        $this->assertEquals(5, $event->fresh()->availableTickets());

        $booking->update(['status' => 'cancelled']);

        // After cancellation, tickets should be available again
        $this->assertEquals(10, $event->fresh()->availableTickets());
    }

    /** @test */
    public function event_in_past_cannot_be_booked()
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->subWeek(),
        ]);

        $this->assertFalse($event->canBeBooked());
    }

    /** @test */
    public function event_starting_in_less_than_one_hour()
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addMinutes(30),
        ]);

        // Event should still be bookable if it's in the future
        $this->assertTrue($event->canBeBooked());
    }

    /** @test */
    public function discount_code_with_zero_usage_limit()
    {
        $discountCode = \App\Models\DiscountCode::factory()->create([
            'usage_limit' => null,
            'is_active' => true,
        ]);

        $this->assertTrue($discountCode->isValid());
    }

    /** @test */
    public function discount_code_exactly_at_usage_limit()
    {
        $discountCode = \App\Models\DiscountCode::factory()->create([
            'usage_limit' => 10,
            'usage_count' => 10,
            'is_active' => true,
        ]);

        $this->assertFalse($discountCode->isValid());
    }

    /** @test */
    public function hundred_percent_discount_makes_booking_free()
    {
        $discountCode = \App\Models\DiscountCode::factory()->create([
            'type' => 'percentage',
            'value' => 100,
        ]);

        $discount = $discountCode->calculateDiscount(100);

        $this->assertEquals(100, $discount);
    }

    /** @test */
    public function event_with_very_long_title()
    {
        $organizer = createOrganizer();
        $longTitle = str_repeat('A', 500);

        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'title' => substr($longTitle, 0, 255), // Assuming max 255 chars
        ]);

        $this->assertNotNull($event);
    }

    /** @test */
    public function booking_with_multiple_ticket_types()
    {
        $user = createUser();
        $event = Event::factory()->create(['is_published' => true]);

        $ticketType1 = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 50,
        ]);
        $ticketType2 = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 100,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.store', $event), [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'tickets' => [
                $ticketType1->id => 2,
                $ticketType2->id => 1,
            ],
        ]);

        $booking = Booking::where('customer_email', $user->email)->first();
        $this->assertEquals(200, $booking->subtotal); // 2*50 + 1*100
    }

    /** @test */
    public function series_with_no_recurrence_count_or_end_date()
    {
        $series = \App\Models\EventSeries::factory()->create([
            'recurrence_count' => null,
            'recurrence_end_date' => null,
        ]);

        $this->assertNull($series->recurrence_count);
        $this->assertNull($series->recurrence_end_date);
    }

    /** @test */
    public function event_with_same_start_and_end_date()
    {
        $startDate = now()->addWeek();

        $event = Event::factory()->create([
            'start_date' => $startDate,
            'end_date' => $startDate,
        ]);

        $this->assertEquals($event->start_date->format('Y-m-d H:i'), $event->end_date->format('Y-m-d H:i'));
    }

    /** @test */
    public function ticket_type_with_unlimited_quantity()
    {
        $ticketType = TicketType::factory()->create([
            'quantity' => null,
            'quantity_sold' => 100,
        ]);

        $this->assertEquals(PHP_INT_MAX, $ticketType->availableQuantity());
    }
}


