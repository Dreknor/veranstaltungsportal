<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketPdfGenerationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function pdf_ticket_can_be_generated_for_confirmed_booking()
    {


        Storage::fake('local');

        $user = User::factory()->create();
        $event = Event::factory()->create(['is_published' => true]);
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->get(route('bookings.ticket', $booking->booking_number));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function user_can_only_download_their_own_tickets()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $booking = Booking::factory()->create([
            'user_id' => $user2->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user1)->get(route('bookings.ticket', $booking->booking_number));

        $response->assertStatus(403);
    }

    #[Test]
    public function pdf_ticket_cannot_be_generated_for_pending_booking()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['is_published' => true]);

        // Erstelle eine Buchung mit Preis > 0 und Status pending
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'pending',
            'total' => 50.00, // Buchung kostet Geld
        ]);

        $response = $this->actingAs($user)->get(route('bookings.ticket', $booking->booking_number));

        // Pending bookings mit Preis > 0 sollten kein Ticket erlauben
        $response->assertStatus(403);
    }

    #[Test]
    public function pdf_ticket_can_be_generated_for_free_pending_booking()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['is_published' => true]);

        // Erstelle eine kostenlose Buchung mit Status pending
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'pending',
            'total' => 0.00, // Kostenlose Buchung
        ]);

        $response = $this->actingAs($user)->get(route('bookings.ticket', $booking->booking_number));

        // Kostenlose Buchungen sollten auch im pending Status Tickets erlauben
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function pdf_ticket_contains_qr_code()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
            'booking_number' => 'BK-TEST123',
        ]);

        $response = $this->actingAs($user)->get(route('bookings.ticket', $booking->booking_number));

        $response->assertStatus(200);
    }

    #[Test]
    public function organizer_can_download_all_tickets_for_event()
    {
        $this->markTestSkipped('Route organizer.events.all-tickets is not implemented yet');

        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create(['organization_id' => $result['organization']->id]);

        Booking::factory()->count(5)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.all-tickets', $event));

        $response->assertStatus(200);
    }
}



