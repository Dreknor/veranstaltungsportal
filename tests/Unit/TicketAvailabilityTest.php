<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function event_with_price_from_and_available_ticket_types_shows_both()
    {
        // Create an event with price_from
        $event = Event::factory()->create([
            'price_from' => 10.00,
            'max_attendees' => 100,
        ]);

        // Create a ticket type that is currently on sale
        $ticketType = TicketType::create([
            'event_id' => $event->id,
            'name' => 'VIP-Ticket',
            'price' => 20.00,
            'quantity' => 50,
            'quantity_sold' => 0,
            'is_available' => true,
            'min_per_order' => 1,
            'max_per_order' => 10,
            'sale_start' => now()->subDay(),
            'sale_end' => now()->addDays(7),
        ]);

        // Test that event has available tickets
        $this->assertTrue($event->hasAvailableTickets());

        // Test that ticket type is on sale
        $this->assertTrue($ticketType->isOnSale());

        // Test that minimum price is the lower of the two
        $this->assertEquals(10.00, $event->getMinimumPrice());
    }

    /** @test */
    public function event_with_only_price_from_is_bookable()
    {
        $event = Event::factory()->create([
            'price_from' => 15.00,
            'max_attendees' => 100,
        ]);

        // No ticket types created

        // Event should still be bookable
        $this->assertTrue($event->hasAvailableTickets());
        $this->assertEquals(15.00, $event->getMinimumPrice());
    }

    /** @test */
    public function ticket_with_date_only_sale_start_is_available_on_that_date()
    {
        $event = Event::factory()->create();

        // Create ticket with sale_start at midnight (date only)
        $ticketType = TicketType::create([
            'event_id' => $event->id,
            'name' => 'Early Bird',
            'price' => 10.00,
            'quantity' => 50,
            'quantity_sold' => 0,
            'is_available' => true,
            'min_per_order' => 1,
            'max_per_order' => 10,
            'sale_start' => now()->startOfDay(), // Midnight today
            'sale_end' => now()->addDays(7)->startOfDay(),
        ]);

        // Should be available now (same day)
        $this->assertTrue($ticketType->isOnSale());
    }

    /** @test */
    public function ticket_with_date_only_sale_end_is_available_until_end_of_day()
    {
        $event = Event::factory()->create();

        // Create ticket with sale_end at midnight (meaning end of previous day)
        $ticketType = TicketType::create([
            'event_id' => $event->id,
            'name' => 'Regular',
            'price' => 15.00,
            'quantity' => 50,
            'quantity_sold' => 0,
            'is_available' => true,
            'min_per_order' => 1,
            'max_per_order' => 10,
            'sale_start' => now()->subDays(7)->startOfDay(),
            'sale_end' => now()->startOfDay(), // Midnight today = end of yesterday
        ]);

        // Should be available today (until end of day)
        $this->assertTrue($ticketType->isOnSale());
    }

    /** @test */
    public function event_shows_both_ticket_types_and_price_from_in_minimum_calculation()
    {
        $event = Event::factory()->create([
            'price_from' => 25.00,
            'max_attendees' => 100,
        ]);

        // Create cheaper ticket type
        TicketType::create([
            'event_id' => $event->id,
            'name' => 'Student Ticket',
            'price' => 15.00,
            'quantity' => 30,
            'quantity_sold' => 0,
            'is_available' => true,
            'min_per_order' => 1,
            'max_per_order' => 5,
            'sale_start' => now()->subDay(),
            'sale_end' => now()->addDays(7),
        ]);

        // Create more expensive ticket type
        TicketType::create([
            'event_id' => $event->id,
            'name' => 'VIP Ticket',
            'price' => 50.00,
            'quantity' => 10,
            'quantity_sold' => 0,
            'is_available' => true,
            'min_per_order' => 1,
            'max_per_order' => 2,
            'sale_start' => now()->subDay(),
            'sale_end' => now()->addDays(7),
        ]);

        // Minimum price should be the student ticket (15.00), not price_from (25.00)
        $this->assertEquals(15.00, $event->getMinimumPrice());
        $this->assertTrue($event->hasAvailableTickets());
    }
}
