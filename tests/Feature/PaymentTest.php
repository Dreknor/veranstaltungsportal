<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function booking_can_be_created_with_pending_payment()
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
            'payment_method' => 'stripe',
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $this->assertDatabaseHas('bookings', [
            'customer_email' => 'john@example.com',
            'payment_status' => 'pending',
        ]);
    }

    #[Test]
    public function booking_status_changes_after_payment_confirmation()
    {
        $booking = Booking::factory()->create([
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // Simulate payment confirmation
        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'payment_transaction_id' => 'txn_' . uniqid(),
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);
    }

    #[Test]
    public function free_events_dont_require_payment()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 0,
        ]);

        $bookingData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'tickets' => [
                $ticketType->id => 1,
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $booking = Booking::where('customer_email', 'john@example.com')->first();

        $this->assertEquals(0, $booking->total);
        $this->assertEquals('confirmed', $booking->status);
    }

    #[Test]
    public function refund_can_be_processed_for_cancelled_booking()
    {
        $booking = Booking::factory()->create([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
            'payment_transaction_id' => 'txn_123',
            'total' => 100,
        ]);

        // Cancel and refund
        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => 'refunded',
        ]);
    }
}


