<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function booking_confirmation_email_is_queued()
    {
        Queue::fake();

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

        Queue::assertPushed(\App\Jobs\SendBookingConfirmationEmail::class);
    }

    #[Test]
    public function event_reminder_emails_are_queued()
    {
        Queue::fake();

        $event = Event::factory()->create([
            'start_date' => now()->addDay(),
        ]);

        Booking::factory()->count(5)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        // Trigger reminder job
        \Artisan::call('events:send-reminders');

        Queue::assertPushed(\App\Jobs\SendEventReminderEmail::class);
    }

    #[Test]
    public function cancelled_event_notifications_are_queued()
    {
        Queue::fake();

        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create(['organization_id' => $result['organization']->id]);

        Booking::factory()->count(3)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        // Set current organization
        session(['current_organization_id' => $result['organization']->id]);

        $this->actingAs($organizer)->post(route('organizer.events.cancel', $event), [
            'cancellation_reason' => 'Unexpected circumstances',
        ]);

        Queue::assertPushed(\App\Jobs\SendEventCancellationEmail::class);
    }
}



