<?php

namespace Tests\Unit\Models;

use App\Models\BookingItem;
use App\Models\Booking;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_booking()
    {
        $booking = Booking::factory()->create();
        $item = BookingItem::factory()->create(['booking_id' => $booking->id]);

        $this->assertInstanceOf(Booking::class, $item->booking);
        $this->assertEquals($booking->id, $item->booking->id);
    }

    /** @test */
    public function it_belongs_to_a_ticket_type()
    {
        $ticketType = TicketType::factory()->create();
        $item = BookingItem::factory()->create(['ticket_type_id' => $ticketType->id]);

        $this->assertInstanceOf(TicketType::class, $item->ticketType);
        $this->assertEquals($ticketType->id, $item->ticketType->id);
    }

    /** @test */
    public function it_calculates_subtotal()
    {
        $item = BookingItem::factory()->create([
            'quantity' => 5,
            'price' => 20.00,
        ]);

        $this->assertEquals(100.00, $item->quantity * $item->price);
    }

    /** @test */
    public function it_casts_price_to_decimal()
    {
        $item = BookingItem::factory()->create(['price' => 49.99]);

        $this->assertEquals('49.99', $item->price);
    }
}

