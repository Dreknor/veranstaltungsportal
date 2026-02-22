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
 * Tests für SoftDeletes auf dem Booking-Model (DSGVO-Aufbewahrungspflicht).
 */
class BookingSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function booking_is_soft_deleted_not_hard_deleted(): void
    {
        $booking = Booking::factory()->create();
        $bookingId = $booking->id;

        $booking->delete();

        // Soft-deleted: In DB vorhanden (mit deleted_at), aber aus normalen Queries ausgeblendet
        $this->assertSoftDeleted('bookings', ['id' => $bookingId]);
        $this->assertNull(Booking::find($bookingId));
        $this->assertNotNull(Booking::withTrashed()->find($bookingId));
    }

    #[Test]
    public function soft_deleted_booking_is_excluded_from_normal_queries(): void
    {
        Booking::factory()->count(3)->create();
        $deletedBooking = Booking::factory()->create();
        $deletedBooking->delete();

        $this->assertCount(3, Booking::all());
        $this->assertCount(4, Booking::withTrashed()->get());
    }

    #[Test]
    public function soft_deleted_booking_can_be_restored(): void
    {
        $booking = Booking::factory()->create();
        $bookingId = $booking->id;

        $booking->delete();
        $this->assertSoftDeleted('bookings', ['id' => $bookingId]);

        Booking::withTrashed()->find($bookingId)->restore();
        $this->assertNotSoftDeleted('bookings', ['id' => $bookingId]);
        $this->assertNotNull(Booking::find($bookingId));
    }

    #[Test]
    public function download_endpoints_work_for_soft_deleted_bookings(): void
    {
        // Sicherstellen dass auch nach "Löschung" Downloads über Buchungsnummer möglich sind
        $booking = Booking::factory()->create();
        $bookingNumber = $booking->booking_number;

        $booking->delete();

        $found = Booking::withTrashed()
            ->where('booking_number', $bookingNumber)
            ->first();

        $this->assertNotNull($found, 'Soft-deleted Buchung muss per booking_number über withTrashed() findbar sein');
        $this->assertEquals($bookingNumber, $found->booking_number);
        $this->assertNotNull($found->deleted_at);
    }

    #[Test]
    public function admin_can_restore_booking_via_policy(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $booking = Booking::factory()->create();

        $this->assertTrue(
            $admin->can('restore', $booking),
            'Admin muss soft-deleted Buchungen wiederherstellen dürfen'
        );
    }

    #[Test]
    public function regular_user_cannot_restore_booking(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $this->assertFalse(
            $user->can('restore', $booking),
            'Reguläre User dürfen keine Buchungen wiederherstellen'
        );
    }

    #[Test]
    public function force_delete_is_never_allowed(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $this->assertFalse(
            $admin->can('forceDelete', $booking),
            'Selbst Admins dürfen Buchungen nicht hard-löschen (DSGVO-Aufbewahrungspflicht)'
        );
    }
}

