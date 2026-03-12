<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmation;
use App\Mail\BookingPendingApproval;
use App\Mail\BookingRejected;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Organization;
use App\Models\TicketType;
use App\Models\User;
use App\Notifications\BookingApprovalRequiredNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FreeTicketApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function createFreeEvent(array $eventAttributes = []): array
    {
        $result = $this->createOrganizerWithOrganization();

        $event = Event::factory()->create(array_merge([
            'organization_id' => $result['organization']->id,
            'is_published' => true,
            'start_date' => now()->addWeek(),
            'max_attendees' => 100,
            'free_ticket_auto_confirm' => true,
        ], $eventAttributes));

        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 0,
            'quantity' => 100,
            'is_available' => true,
        ]);

        return [
            'user' => $result['organizer'],
            'organization' => $result['organization'],
            'event' => $event,
            'ticketType' => $ticketType,
        ];
    }

    protected function bookingData(int $ticketTypeId, array $extra = []): array
    {
        return array_merge([
            'customer_name' => 'Max Mustermann',
            'customer_email' => 'max@example.com',
            'customer_phone' => '',
            'billing_address' => 'Musterstraße 1',
            'billing_postal_code' => '12345',
            'billing_city' => 'Musterstadt',
            'billing_country' => 'Germany',
            'tickets' => [['ticket_type_id' => $ticketTypeId, 'quantity' => 1]],
            'payment_method' => 'invoice',
            'privacy_accepted' => '1',
        ], $extra);
    }

    /** @test */
    public function free_booking_is_auto_confirmed_when_auto_confirm_is_enabled(): void
    {
        Mail::fake();
        $data = $this->createFreeEvent(['free_ticket_auto_confirm' => true]);

        $response = $this->post(
            route('bookings.store', $data['event']),
            $this->bookingData($data['ticketType']->id)
        );

        $response->assertRedirect();
        $booking = Booking::where('event_id', $data['event']->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertNotNull($booking->confirmed_at);

        Mail::assertSent(BookingConfirmation::class);
        Mail::assertNotSent(BookingPendingApproval::class);
    }

    /** @test */
    public function free_booking_gets_pending_approval_status_when_auto_confirm_disabled(): void
    {
        Mail::fake();
        $data = $this->createFreeEvent(['free_ticket_auto_confirm' => false]);

        $this->post(
            route('bookings.store', $data['event']),
            $this->bookingData($data['ticketType']->id)
        );

        $booking = Booking::where('event_id', $data['event']->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals('pending_approval', $booking->status);
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertNull($booking->confirmed_at);

        Mail::assertSent(BookingPendingApproval::class);
        Mail::assertNotSent(BookingConfirmation::class);
    }

    /** @test */
    public function organizer_receives_approval_required_notification_for_pending_bookings(): void
    {
        Notification::fake();
        Mail::fake();
        $data = $this->createFreeEvent(['free_ticket_auto_confirm' => false]);

        $this->post(
            route('bookings.store', $data['event']),
            $this->bookingData($data['ticketType']->id)
        );

        Notification::assertSentTo($data['user'], BookingApprovalRequiredNotification::class);
    }

    /** @test */
    public function organizer_can_approve_pending_booking(): void
    {
        Mail::fake();
        $data = $this->createFreeEvent(['free_ticket_auto_confirm' => false]);

        $booking = Booking::factory()->create([
            'event_id' => $data['event']->id,
            'status' => 'pending_approval',
            'payment_status' => 'paid',
            'total' => 0,
        ]);

        $response = $this->actingAs($data['user'])
            ->post(route('organizer.bookings.approve', $booking));

        $response->assertRedirect();
        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
        $this->assertNotNull($booking->confirmed_at);

        Mail::assertSent(BookingConfirmation::class, fn ($mail) => $mail->hasTo($booking->customer_email));
    }

    /** @test */
    public function organizer_can_reject_pending_booking(): void
    {
        Mail::fake();
        $data = $this->createFreeEvent(['free_ticket_auto_confirm' => false]);

        $ticketType = $data['ticketType'];
        $ticketType->increment('quantity_sold', 1);

        $booking = Booking::factory()->create([
            'event_id' => $data['event']->id,
            'status' => 'pending_approval',
            'payment_status' => 'paid',
            'total' => 0,
        ]);

        $booking->items()->create([
            'ticket_type_id' => $ticketType->id,
            'price' => 0,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($data['user'])
            ->post(route('organizer.bookings.reject', $booking), [
                'rejection_reason' => 'Keine Kapazität mehr',
            ]);

        $response->assertRedirect();
        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);

        Mail::assertSent(BookingRejected::class, fn ($mail) => $mail->hasTo($booking->customer_email));

        // Ticket-Kontingent wurde zurückgegeben
        $ticketType->refresh();
        $this->assertEquals(0, $ticketType->quantity_sold);
    }

    /** @test */
    public function ticket_download_is_blocked_for_pending_approval_bookings(): void
    {
        $data = $this->createFreeEvent(['free_ticket_auto_confirm' => false]);

        $booking = Booking::factory()->create([
            'event_id' => $data['event']->id,
            'status' => 'pending_approval',
            'payment_status' => 'paid',
            'total' => 0,
        ]);

        $response = $this->actingAs($data['user'])
            ->get(route('bookings.ticket', $booking->booking_number));

        $response->assertStatus(403);
    }

    /** @test */
    public function pending_approval_booking_is_counted_in_available_tickets(): void
    {
        $data = $this->createFreeEvent([
            'free_ticket_auto_confirm' => false,
            'max_attendees' => 5,
        ]);

        // Create 3 pending_approval bookings
        for ($i = 0; $i < 3; $i++) {
            $booking = Booking::factory()->create([
                'event_id' => $data['event']->id,
                'status' => 'pending_approval',
                'payment_status' => 'paid',
                'total' => 0,
            ]);
            $booking->items()->create([
                'ticket_type_id' => $data['ticketType']->id,
                'price' => 0,
                'quantity' => 1,
            ]);
            $data['ticketType']->increment('quantity_sold');
        }

        $available = $data['event']->availableTickets();
        $this->assertEquals(2, $available); // 5 - 3 = 2
    }

    /** @test */
    public function needs_personalization_returns_false_for_pending_approval(): void
    {
        $data = $this->createFreeEvent();
        $booking = Booking::factory()->create([
            'event_id' => $data['event']->id,
            'status' => 'pending_approval',
            'payment_status' => 'paid',
            'tickets_personalized' => false,
        ]);

        // Even with multiple items, pending_approval should not need personalization
        for ($i = 0; $i < 3; $i++) {
            $booking->items()->create([
                'ticket_type_id' => $data['ticketType']->id,
                'price' => 0,
                'quantity' => 1,
            ]);
        }

        $this->assertFalse($booking->needsPersonalization());
    }

    /** @test */
    public function can_send_tickets_returns_false_for_pending_approval(): void
    {
        $data = $this->createFreeEvent();
        $booking = Booking::factory()->create([
            'event_id' => $data['event']->id,
            'status' => 'pending_approval',
            'payment_status' => 'paid',
            'tickets_personalized' => true,
        ]);

        $this->assertFalse($booking->canSendTickets());
    }

    /** @test */
    public function organizer_can_bulk_approve_all_pending_bookings(): void
    {
        Mail::fake();
        $data = $this->createFreeEvent(['free_ticket_auto_confirm' => false]);

        // Create 3 pending_approval bookings
        $bookings = collect();
        for ($i = 0; $i < 3; $i++) {
            $booking = Booking::factory()->create([
                'event_id' => $data['event']->id,
                'status' => 'pending_approval',
                'payment_status' => 'paid',
                'total' => 0,
            ]);
            $bookings->push($booking);
        }

        $response = $this->actingAs($data['user'])
            ->post(route('organizer.events.approve-all-pending', $data['event']));

        $response->assertRedirect();

        foreach ($bookings as $booking) {
            $booking->refresh();
            $this->assertEquals('confirmed', $booking->status);
        }

        Mail::assertSent(BookingConfirmation::class, 3);
    }
}


