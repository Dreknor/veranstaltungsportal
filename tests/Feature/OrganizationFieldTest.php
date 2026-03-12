<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\Organization;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrganizationFieldTest extends TestCase
{
    use RefreshDatabase;

    protected function createOrganizerWithEvent(array $eventAttributes = []): array
    {
        $result = $this->createOrganizerWithOrganization();

        $event = Event::factory()->create(array_merge([
            'organization_id' => $result['organization']->id,
            'is_published' => true,
            'start_date' => now()->addWeek(),
            'max_attendees' => 100,
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
            'customer_name' => 'Test Teilnehmer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '',
            'billing_address' => 'Teststraße 1',
            'billing_postal_code' => '12345',
            'billing_city' => 'Teststadt',
            'billing_country' => 'Germany',
            'tickets' => [['ticket_type_id' => $ticketTypeId, 'quantity' => 1]],
            'payment_method' => 'invoice',
            'privacy_accepted' => '1',
        ], $extra);
    }

    /** @test */
    public function organization_field_is_not_shown_when_mode_is_none(): void
    {
        $data = $this->createOrganizerWithEvent(['organization_field_mode' => 'none']);

        $response = $this->get(route('bookings.create', $data['event']));
        $response->assertStatus(200);
        $response->assertDontSee('customer_organization');
    }

    /** @test */
    public function organization_field_is_shown_when_mode_is_optional(): void
    {
        $data = $this->createOrganizerWithEvent(['organization_field_mode' => 'optional']);

        $response = $this->get(route('bookings.create', $data['event']));
        $response->assertStatus(200);
        $response->assertSee('customer_organization');
        $response->assertSee('optional');
    }

    /** @test */
    public function organization_field_is_shown_as_required_when_mode_is_required(): void
    {
        $data = $this->createOrganizerWithEvent(['organization_field_mode' => 'required']);

        $response = $this->get(route('bookings.create', $data['event']));
        $response->assertStatus(200);
        $response->assertSee('customer_organization');
        $response->assertSee('required');
    }

    /** @test */
    public function booking_fails_validation_when_organization_is_required_but_missing(): void
    {
        Mail::fake();
        $data = $this->createOrganizerWithEvent(['organization_field_mode' => 'required']);

        $response = $this->post(
            route('bookings.store', $data['event']),
            $this->bookingData($data['ticketType']->id)
        );

        $response->assertSessionHasErrors('customer_organization');
    }

    /** @test */
    public function booking_succeeds_when_organization_is_required_and_provided(): void
    {
        Mail::fake();
        $data = $this->createOrganizerWithEvent([
            'organization_field_mode' => 'required',
            'free_ticket_auto_confirm' => true,
        ]);

        $response = $this->post(
            route('bookings.store', $data['event']),
            $this->bookingData($data['ticketType']->id, ['customer_organization' => 'Testschule GmbH'])
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'event_id' => $data['event']->id,
            'customer_organization' => 'Testschule GmbH',
        ]);
    }

    /** @test */
    public function organization_is_saved_to_booking_item_for_single_ticket(): void
    {
        Mail::fake();
        $data = $this->createOrganizerWithEvent([
            'organization_field_mode' => 'optional',
            'free_ticket_auto_confirm' => true,
        ]);

        $this->post(
            route('bookings.store', $data['event']),
            $this->bookingData($data['ticketType']->id, ['customer_organization' => 'Meine Schule'])
        );

        $booking = Booking::where('event_id', $data['event']->id)->first();
        $this->assertNotNull($booking);
        $this->assertDatabaseHas('booking_items', [
            'booking_id' => $booking->id,
            'attendee_organization' => 'Meine Schule',
        ]);
    }

    /** @test */
    public function event_model_helpers_work_correctly(): void
    {
        $event = Event::factory()->make(['organization_field_mode' => 'none']);
        $this->assertFalse($event->showsOrganizationField());
        $this->assertFalse($event->requiresOrganizationField());

        $event->organization_field_mode = 'optional';
        $this->assertTrue($event->showsOrganizationField());
        $this->assertFalse($event->requiresOrganizationField());

        $event->organization_field_mode = 'required';
        $this->assertTrue($event->showsOrganizationField());
        $this->assertTrue($event->requiresOrganizationField());
    }
}


