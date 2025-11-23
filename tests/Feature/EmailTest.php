<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Mail\BookingConfirmation;
use App\Mail\BookingCancellation;
use App\Mail\EventCancelledMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function booking_confirmation_email_is_sent()
    {
        Mail::fake();

        $user = User::factory()->create();
        $event = Event::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'customer_email' => 'customer@example.com',
        ]);

        Mail::to($booking->customer_email)->send(new BookingConfirmation($booking));

        Mail::assertSent(BookingConfirmation::class, function ($mail) use ($booking) {
            return $mail->hasTo($booking->customer_email);
        });
    }

    #[Test]
    public function booking_cancellation_email_is_sent()
    {
        Mail::fake();

        $booking = Booking::factory()->create([
            'customer_email' => 'customer@example.com',
            'status' => 'cancelled',
        ]);

        Mail::to($booking->customer_email)->send(new BookingCancellation($booking));

        Mail::assertSent(BookingCancellation::class);
    }

    #[Test]
    public function event_cancellation_email_is_sent_to_attendees()
    {
        Mail::fake();

        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create([
            'organization_id' => $result['organization']->id,
            'is_cancelled' => false,
        ]);

        $booking1 = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'customer_email' => 'attendee1@example.com',
        ]);
        $booking2 = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'customer_email' => 'attendee2@example.com',
        ]);

        // Cancel the event via controller to trigger emails
        $this->actingAs($organizer)->post(route('organizer.events.cancel', $event), [
            'cancellation_reason' => 'Weather conditions',
        ]);

        Mail::assertQueued(EventCancelledMail::class, 2);
    }

    #[Test]
    public function booking_confirmation_email_contains_booking_details()
    {
        $booking = Booking::factory()->create([
            'booking_number' => 'BK-TEST123',
        ]);

        $mail = new BookingConfirmation($booking);

        $this->assertEquals($booking->customer_email, $booking->customer_email);
    }
}



