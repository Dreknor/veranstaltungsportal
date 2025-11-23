<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function guest_can_view_booking_page_for_published_event()
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);

        $response = $this->get(route('bookings.create', $event));

        $response->assertStatus(200);
    }

    #[Test]
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
            'billing_address' => 'Test Street 123',
            'billing_postal_code' => '12345',
            'billing_city' => 'Berlin',
            'billing_country' => 'Germany',
            'tickets' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $this->assertDatabaseHas('bookings', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'customer_email' => 'john@example.com',
        ]);
    }

    #[Test]
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
            'billing_address' => 'Test Street 123',
            'billing_postal_code' => '12345',
            'billing_city' => 'Berlin',
            'billing_country' => 'Germany',
            'tickets' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $booking = Booking::where('customer_email', 'john@example.com')->first();

        $this->assertNotNull($booking);
        $this->assertEquals(100, $booking->subtotal);
    }

    #[Test]
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
            'billing_address' => 'Test Street 456',
            'billing_postal_code' => '54321',
            'billing_city' => 'Munich',
            'billing_country' => 'Germany',
            'tickets' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 1,
                ],
            ],
            'discount_code' => 'SAVE20',
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $booking = Booking::where('customer_email', 'john@example.com')->first();

        $this->assertNotNull($booking);
        $this->assertEquals(20, $booking->discount);
        $this->assertEquals(80, $booking->total);
    }

    #[Test]
    public function user_can_view_their_bookings()
    {
        $user = User::factory()->create();
        Booking::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_cancel_their_booking()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('bookings.cancel', $booking->booking_number));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);
    }

    #[Test]
    public function user_cannot_cancel_other_users_booking()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user2->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user1)->post(route('bookings.cancel', $booking->booking_number));

        $response->assertStatus(403);
    }

    #[Test]
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
            'billing_address' => 'Test Street 789',
            'billing_postal_code' => '11111',
            'billing_city' => 'Hamburg',
            'billing_country' => 'Germany',
            'tickets' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 3,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $ticketType->refresh();
        $this->assertEquals(3, $ticketType->quantity_sold);
        $this->assertEquals(7, $ticketType->availableQuantity());
    }

    #[Test]
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
            'billing_address' => 'Test Street 999',
            'billing_postal_code' => '99999',
            'billing_city' => 'Frankfurt',
            'billing_country' => 'Germany',
            'tickets' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 10,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
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
            'billing_address' => 'Test Street 101',
            'billing_postal_code' => '10101',
            'billing_city' => 'Dresden',
            'billing_country' => 'Germany',
            'tickets' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}



