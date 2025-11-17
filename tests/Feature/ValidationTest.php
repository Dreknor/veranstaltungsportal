<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function event_creation_requires_title()
    {
        $organizer = createOrganizer();

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), [
            'description' => 'Test description',
            'event_type' => 'physical',
            'start_date' => now()->addWeek()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('title');
    }

    #[Test]
    public function event_creation_requires_valid_event_type()
    {
        $organizer = createOrganizer();

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), [
            'title' => 'Test Event',
            'event_type' => 'invalid_type',
            'start_date' => now()->addWeek()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('event_type');
    }

    #[Test]
    public function event_start_date_must_be_in_future()
    {
        $organizer = createOrganizer();

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), [
            'title' => 'Test Event',
            'event_type' => 'physical',
            'start_date' => now()->subWeek()->format('Y-m-d H:i:s'),
            'end_date' => now()->subDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('start_date');
    }

    #[Test]
    public function event_end_date_must_be_after_start_date()
    {
        $organizer = createOrganizer();

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), [
            'title' => 'Test Event',
            'event_type' => 'physical',
            'start_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('end_date');
    }

    #[Test]
    public function booking_requires_customer_name()
    {
        $user = createUser();
        $event = Event::factory()->create(['is_published' => true]);

        $response = $this->actingAs($user)->post(route('bookings.store', $event), [
            'customer_email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('customer_name');
    }

    #[Test]
    public function booking_requires_valid_email()
    {
        $user = createUser();
        $event = Event::factory()->create(['is_published' => true]);

        $response = $this->actingAs($user)->post(route('bookings.store', $event), [
            'customer_name' => 'John Doe',
            'customer_email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors('customer_email');
    }

    #[Test]
    public function ticket_type_price_must_be_numeric()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.tickets.store', $event), [
            'name' => 'VIP Ticket',
            'price' => 'not-a-number',
            'is_available' => true,
        ]);

        $response->assertSessionHasErrors('price');
    }

    #[Test]
    public function ticket_type_price_cannot_be_negative()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.tickets.store', $event), [
            'name' => 'VIP Ticket',
            'price' => -10,
            'is_available' => true,
        ]);

        $response->assertSessionHasErrors('price');
    }

    #[Test]
    public function discount_code_value_must_be_positive()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.discounts.store', $event), [
            'code' => 'INVALID',
            'type' => 'percentage',
            'value' => -20,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('value');
    }

    #[Test]
    public function percentage_discount_cannot_exceed_100()
    {
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->post(route('organizer.events.discounts.store', $event), [
            'code' => 'OVER100',
            'type' => 'percentage',
            'value' => 150,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors('value');
    }

    #[Test]
    public function max_attendees_must_be_positive()
    {
        $organizer = createOrganizer();

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), [
            'title' => 'Test Event',
            'event_type' => 'physical',
            'start_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            'end_date' => now()->addWeek()->addHours(2)->format('Y-m-d H:i:s'),
            'max_attendees' => -50,
        ]);

        $response->assertSessionHasErrors('max_attendees');
    }

    #[Test]
    public function phone_number_format_is_validated()
    {
        $user = createUser();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => 'invalid-phone',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    #[Test]
    public function review_rating_must_be_between_1_and_5()
    {
        $user = createUser();
        $event = Event::factory()->create();

        \App\Models\Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('events.reviews.store', $event), [
            'rating' => 10,
            'comment' => 'Great event!',
        ]);

        $response->assertSessionHasErrors('rating');
    }
}


