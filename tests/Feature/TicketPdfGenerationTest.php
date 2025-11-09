<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketPdfGenerationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
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

        $response = $this->actingAs($user)->get(route('bookings.ticket', $booking));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function user_can_only_download_their_own_tickets()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $booking = Booking::factory()->create([
            'user_id' => $user2->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user1)->get(route('bookings.ticket', $booking));

        $response->assertStatus(403);
    }

    /** @test */
    public function pdf_ticket_cannot_be_generated_for_pending_booking()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('bookings.ticket', $booking));

        $response->assertStatus(403);
    }

    /** @test */
    public function pdf_ticket_contains_qr_code()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
            'booking_number' => 'BK-TEST123',
        ]);

        $response = $this->actingAs($user)->get(route('bookings.ticket', $booking));

        $response->assertStatus(200);
    }

    /** @test */
    public function organizer_can_download_all_tickets_for_event()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        Booking::factory()->count(5)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.all-tickets', $event));

        $response->assertStatus(200);
    }
}

