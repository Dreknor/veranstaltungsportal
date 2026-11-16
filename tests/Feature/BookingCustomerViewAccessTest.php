<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookingCustomerViewAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeBooking(): array
    {
        $setup = createOrganizerWithOrganization();
        $event = Event::factory()->create(['organization_id' => $setup['organization']->id]);
        $customer = User::factory()->create();
        $booking = Booking::factory()->create(['event_id' => $event->id, 'user_id' => $customer->id]);

        return [$booking, $customer, $setup['organizer']];
    }

    #[Test]
    public function booking_owner_can_open_customer_view(): void
    {
        [$booking, $customer] = $this->makeBooking();

        $this->actingAs($customer)->get(route('bookings.show', $booking->booking_number))->assertOk();
    }

    #[Test]
    public function organizer_of_the_event_can_open_customer_view_read_only(): void
    {
        [$booking, , $organizer] = $this->makeBooking();

        $this->actingAs($organizer)
            ->get(route('bookings.show', $booking->booking_number))
            ->assertOk()
            ->assertDontSee(route('bookings.cancel', $booking->booking_number));
    }

    #[Test]
    public function admin_can_open_customer_view(): void
    {
        [$booking] = $this->makeBooking();

        $this->actingAs(createAdmin())->get(route('bookings.show', $booking->booking_number))->assertOk();
    }

    #[Test]
    public function other_organizer_cannot_open_customer_view(): void
    {
        [$booking] = $this->makeBooking();
        $other = createOrganizerWithOrganization()['organizer'];

        $this->actingAs($other)->get(route('bookings.show', $booking->booking_number))->assertNotFound();
    }

    #[Test]
    public function guest_is_redirected_to_verification(): void
    {
        [$booking] = $this->makeBooking();

        $this->get(route('bookings.show', $booking->booking_number))
            ->assertRedirect(route('bookings.verify', $booking->booking_number));
    }

    #[Test]
    public function organizer_cannot_cancel_customer_booking(): void
    {
        [$booking, , $organizer] = $this->makeBooking();

        $this->actingAs($organizer)
            ->post(route('bookings.cancel', $booking->booking_number))
            ->assertForbidden();
    }
}
