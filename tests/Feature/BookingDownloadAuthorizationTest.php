<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests für Policy-basierte Download-Autorisierung (Ticket, Rechnung, Zertifikat, iCal).
 */
class BookingDownloadAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function booking_owner_can_download_via_policy(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $this->assertTrue(
            $user->can('download', $booking),
            'Buchungsinhaber muss Download-Zugriff haben'
        );
    }

    #[Test]
    public function other_user_cannot_download_booking(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse(
            $other->can('download', $booking),
            'Fremder User darf keine Buchungsdokumente herunterladen'
        );
    }

    #[Test]
    public function admin_can_download_any_booking(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $booking = Booking::factory()->create();

        $this->assertTrue(
            $admin->can('download', $booking),
            'Admin muss alle Buchungsdokumente herunterladen können'
        );
    }

    #[Test]
    public function guest_with_session_access_can_download(): void
    {
        $booking = Booking::factory()->create(['user_id' => null]);

        // Session-Token setzen (wie nach E-Mail-Verifikation)
        session()->put('booking_access_' . $booking->id, true);

        // Gast (kein User) mit Session-Token prüfen via Policy direkt
        // Da authorize() einen User braucht, nutzen wir Gate::forUser(null)
        $policy = new \App\Policies\BookingPolicy();
        $result = $policy->download(null, $booking);

        $this->assertTrue($result, 'Gast mit Session-Token muss Download-Zugriff haben');
    }

    #[Test]
    public function guest_without_session_access_cannot_download(): void
    {
        $booking = Booking::factory()->create(['user_id' => null]);

        // Keine Session gesetzt
        $policy = new \App\Policies\BookingPolicy();
        $result = $policy->download(null, $booking);

        $this->assertFalse($result, 'Gast ohne Session-Token darf nicht herunterladen');
    }

    #[Test]
    public function organization_admin_can_download_event_bookings(): void
    {
        $orgAdmin = User::factory()->create();
        $orgAdmin->assignRole('organizer');

        $organization = Organization::factory()->create();
        $organization->users()->attach($orgAdmin->id, [
            'role' => 'admin',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $event = Event::factory()->create(['organization_id' => $organization->id]);
        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'user_id' => User::factory()->create()->id,
        ]);

        // Relationen laden, damit die Policy organization->users findet
        $booking->load(['event.organization.users']);

        $this->assertTrue(
            $orgAdmin->can('download', $booking),
            'Organisations-Admin muss Event-Buchungen herunterladen können'
        );
    }

    #[Test]
    public function download_route_returns_403_for_unauthorized_user(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $booking = Booking::factory()->create([
            'user_id' => $owner->id,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($other)
            ->get(route('bookings.invoice', $booking->booking_number));

        $response->assertStatus(403);
    }
}

