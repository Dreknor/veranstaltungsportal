<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function booking_confirmation_email_is_sent()
    {
        Mail::fake();

        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);
        $ticketType = \App\Models\TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 50,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.store', $event), [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '1234567890',
            'billing_address' => '123 Main St',
            'billing_postal_code' => '12345',
            'billing_city' => 'Test City',
            'billing_country' => 'Deutschland',
            'tickets' => [
                [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        Mail::assertSent(\App\Mail\BookingConfirmation::class);
    }

    #[Test]
    public function event_reminder_can_be_dispatched()
    {
        // This test verifies that the job can be dispatched manually
        $event = Event::factory()->create([
            'start_date' => now()->addDay(),
        ]);

        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        Mail::fake();

        // Dispatch the job manually
        \App\Jobs\SendEventReminderEmail::dispatch($booking);

        // Since the job implements ShouldQueue, the mail is queued not sent
        Mail::assertQueued(\App\Mail\EventReminderMail::class);
    }

    #[Test]
    public function cancelled_event_notifications_can_be_dispatched()
    {
        // This test verifies that the cancellation job can be dispatched
        $event = Event::factory()->create();
        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        Mail::fake();

        // Dispatch the job manually
        \App\Jobs\SendEventCancellationEmail::dispatch($booking);

        // Since the job implements ShouldQueue, the mail is queued not sent
        Mail::assertQueued(\App\Mail\EventCancelledMail::class);
    }
}



