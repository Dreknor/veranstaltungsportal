<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use App\Models\TicketType;
use App\Models\Event;
use App\Models\BookingItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTypeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_an_event()
    {
        $event = Event::factory()->create();
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);

        $this->assertInstanceOf(Event::class, $ticketType->event);
        $this->assertEquals($event->id, $ticketType->event->id);
    }

    #[Test]
    public function it_has_many_booking_items()
    {
        $ticketType = TicketType::factory()->create();
        BookingItem::factory()->count(3)->create(['ticket_type_id' => $ticketType->id]);

        $this->assertCount(3, $ticketType->bookingItems);
    }

    #[Test]
    public function it_calculates_available_quantity()
    {
        $event = Event::factory()->create(['max_attendees' => null]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'quantity' => 100,
            'quantity_sold' => 30,
        ]);

        $this->assertEquals(70, $ticketType->availableQuantity());
    }

    #[Test]
    public function it_returns_unlimited_when_quantity_is_null()
    {
        $event = Event::factory()->create(['max_attendees' => null]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'quantity' => null,
        ]);

        $this->assertEquals(PHP_INT_MAX, $ticketType->availableQuantity());
    }

    #[Test]
    public function it_checks_if_ticket_is_on_sale()
    {
        $ticketType = TicketType::factory()->create([
            'is_available' => true,
            'sale_start' => now()->subDay(),
            'sale_end' => now()->addDay(),
        ]);

        $this->assertTrue($ticketType->isOnSale());
    }

    #[Test]
    public function it_is_not_on_sale_before_sale_start()
    {
        $ticketType = TicketType::factory()->create([
            'is_available' => true,
            'sale_start' => now()->addDay(),
            'sale_end' => now()->addWeek(),
        ]);

        $this->assertFalse($ticketType->isOnSale());
    }

    #[Test]
    public function it_is_not_on_sale_after_sale_end()
    {
        $ticketType = TicketType::factory()->create([
            'is_available' => true,
            'sale_start' => now()->subWeek(),
            'sale_end' => now()->subDay(),
        ]);

        $this->assertFalse($ticketType->isOnSale());
    }

    #[Test]
    public function it_is_not_on_sale_when_not_available()
    {
        $ticketType = TicketType::factory()->create([
            'is_available' => false,
            'sale_start' => now()->subDay(),
            'sale_end' => now()->addDay(),
        ]);

        $this->assertFalse($ticketType->isOnSale());
    }

    #[Test]
    public function it_scopes_available_tickets()
    {
        TicketType::factory()->create([
            'is_available' => true,
            'quantity' => 100,
            'quantity_sold' => 50,
        ]);

        TicketType::factory()->create([
            'is_available' => false,
            'quantity' => 100,
            'quantity_sold' => 50,
        ]);

        TicketType::factory()->create([
            'is_available' => true,
            'quantity' => 100,
            'quantity_sold' => 100,
        ]);

        $this->assertCount(1, TicketType::available()->get());
    }
}



