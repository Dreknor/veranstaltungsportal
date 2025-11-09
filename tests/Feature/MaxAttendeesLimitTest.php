<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaxAttendeesLimitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ticket_type_available_quantity_respects_event_max_attendees()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $category = EventCategory::factory()->create();

        // Event mit max_attendees = 10
        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'event_category_id' => $category->id,
            'max_attendees' => 10,
            'is_published' => true,
        ]);

        // Ticket-Typ mit 100 Plätzen
        $ticketType = TicketType::create([
            'event_id' => $event->id,
            'name' => 'Standard',
            'price' => 50,
            'quantity' => 100,
            'quantity_sold' => 0,
            'is_available' => true,
        ]);

        // availableQuantity sollte 10 sein (event max_attendees limit)
        $this->assertEquals(10, $ticketType->availableQuantity());
    }

    /** @test */
    public function ticket_type_available_quantity_uses_ticket_limit_when_lower_than_event_limit()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $category = EventCategory::factory()->create();

        // Event mit max_attendees = 100
        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'event_category_id' => $category->id,
            'max_attendees' => 100,
            'is_published' => true,
        ]);

        // Ticket-Typ mit nur 20 Plätzen
        $ticketType = TicketType::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 100,
            'quantity' => 20,
            'quantity_sold' => 5,
            'is_available' => true,
        ]);

        // availableQuantity sollte 15 sein (ticket limit minus sold)
        $this->assertEquals(15, $ticketType->availableQuantity());
    }

    /** @test */
    public function event_max_attendees_blocks_booking_when_reached()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $category = EventCategory::factory()->create();

        // Event mit max_attendees = 5
        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'event_category_id' => $category->id,
            'max_attendees' => 5,
            'is_published' => true,
        ]);

        // Ticket-Typ mit 100 Plätzen
        $ticketType = TicketType::create([
            'event_id' => $event->id,
            'name' => 'Standard',
            'price' => 50,
            'quantity' => 100,
            'quantity_sold' => 0,
            'is_available' => true,
        ]);

        // Simuliere 5 verkaufte Tickets über Bookings
        for ($i = 0; $i < 5; $i++) {
            $booking = \App\Models\Booking::create([
                'event_id' => $event->id,
                'user_id' => User::factory()->create()->id,
                'customer_name' => 'Test User ' . $i,
                'customer_email' => 'test' . $i . '@example.com',
                'billing_address' => 'Test Address',
                'billing_postal_code' => '12345',
                'billing_city' => 'Test City',
                'billing_country' => 'Deutschland',
                'subtotal' => 50,
                'total' => 50,
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ]);

            \App\Models\BookingItem::create([
                'booking_id' => $booking->id,
                'ticket_type_id' => $ticketType->id,
                'price' => 50,
                'quantity' => 1,
            ]);
        }

        // Aktualisiere Event
        $event = $event->fresh();

        // availableTickets sollte 0 sein
        $this->assertEquals(0, $event->availableTickets());

        // hasAvailableTickets sollte false sein
        $this->assertFalse($event->hasAvailableTickets());

        // ticketType->availableQuantity sollte auch 0 sein
        $ticketType = $ticketType->fresh();
        $this->assertEquals(0, $ticketType->availableQuantity());
    }

    /** @test */
    public function multiple_ticket_types_respect_shared_event_max_attendees()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $category = EventCategory::factory()->create();

        // Event mit max_attendees = 10
        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'event_category_id' => $category->id,
            'max_attendees' => 10,
            'is_published' => true,
        ]);

        // Zwei Ticket-Typen mit jeweils 50 Plätzen
        $ticketType1 = TicketType::create([
            'event_id' => $event->id,
            'name' => 'Standard',
            'price' => 50,
            'quantity' => 50,
            'quantity_sold' => 0,
            'is_available' => true,
        ]);

        $ticketType2 = TicketType::create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 100,
            'quantity' => 50,
            'quantity_sold' => 0,
            'is_available' => true,
        ]);

        // Beide sollten maximal 10 verfügbare Plätze haben
        $this->assertEquals(10, $ticketType1->availableQuantity());
        $this->assertEquals(10, $ticketType2->availableQuantity());

        // Verkaufe 6 Standard-Tickets
        for ($i = 0; $i < 6; $i++) {
            $booking = \App\Models\Booking::create([
                'event_id' => $event->id,
                'user_id' => User::factory()->create()->id,
                'customer_name' => 'Test User ' . $i,
                'customer_email' => 'test' . $i . '@example.com',
                'billing_address' => 'Test Address',
                'billing_postal_code' => '12345',
                'billing_city' => 'Test City',
                'billing_country' => 'Deutschland',
                'subtotal' => 50,
                'total' => 50,
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ]);

            \App\Models\BookingItem::create([
                'booking_id' => $booking->id,
                'ticket_type_id' => $ticketType1->id,
                'price' => 50,
                'quantity' => 1,
            ]);
        }

        // Aktualisiere Objekte
        $event = $event->fresh();
        $ticketType1 = $ticketType1->fresh();
        $ticketType2 = $ticketType2->fresh();

        // Event sollte noch 4 Plätze haben
        $this->assertEquals(4, $event->availableTickets());

        // Beide Ticket-Typen sollten nur noch 4 Plätze zeigen (event limit)
        $this->assertEquals(4, $ticketType1->availableQuantity());
        $this->assertEquals(4, $ticketType2->availableQuantity());
    }
}

