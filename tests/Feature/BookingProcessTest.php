<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Booking;
use App\Models\TicketType;
use App\Models\DiscountCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingProcessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_can_view_booking_page_for_published_event()
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);

        $response = $this->get(route('bookings.create', $event));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_create_booking_for_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
            'max_attendees' => 100,
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 50,
            'quantity' => 100,
        ]);

        $bookingData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '123456789',
            'tickets' => [
                $ticketType->id => 2,
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $this->assertDatabaseHas('bookings', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'customer_email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function booking_calculates_total_correctly()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 50,
        ]);

        $bookingData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'tickets' => [
                $ticketType->id => 2,
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $booking = Booking::where('customer_email', 'john@example.com')->first();

        $this->assertNotNull($booking);
        $this->assertEquals(100, $booking->subtotal);
    }

    /** @test */
    public function booking_applies_discount_code()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 100,
        ]);
        $discountCode = DiscountCode::factory()->create([
            'event_id' => $event->id,
            'code' => 'SAVE20',
            'type' => 'percentage',
            'value' => 20,
            'is_active' => true,
        ]);

        $bookingData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'tickets' => [
                $ticketType->id => 1,
            ],
            'discount_code' => 'SAVE20',
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $booking = Booking::where('customer_email', 'john@example.com')->first();

        $this->assertNotNull($booking);
        $this->assertEquals(20, $booking->discount);
        $this->assertEquals(80, $booking->total);
    }

    /** @test */
    public function user_can_view_their_bookings()
    {
        $user = User::factory()->create();
        Booking::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_cancel_their_booking()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('bookings.cancel', $booking));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);
    }

    /** @test */
    public function user_cannot_cancel_other_users_booking()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user2->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user1)->post(route('bookings.cancel', $booking));

        $response->assertStatus(403);
    }

    /** @test */
    public function booking_reduces_available_tickets()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
            'max_attendees' => 100,
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 50,
            'quantity' => 10,
            'quantity_sold' => 0,
        ]);

        $bookingData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'tickets' => [
                $ticketType->id => 3,
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $ticketType->refresh();
        $this->assertEquals(3, $ticketType->quantity_sold);
        $this->assertEquals(7, $ticketType->availableQuantity());
    }

    /** @test */
    public function cannot_book_more_tickets_than_available()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 50,
            'quantity' => 5,
            'quantity_sold' => 0,
        ]);

        $bookingData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'tickets' => [
                $ticketType->id => 10,
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function cannot_book_cancelled_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'is_cancelled' => true,
            'start_date' => now()->addWeek(),
        ]);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);

        $bookingData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'tickets' => [
                $ticketType->id => 1,
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $response->assertStatus(403);
    }
}

