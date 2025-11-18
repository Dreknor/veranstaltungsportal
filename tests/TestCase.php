<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create a booking item with ticket type
     */
    protected function createBookingItem($booking, $quantity = 1, $price = 50, $ticketType = null)
    {
        if (!$ticketType) {
            $ticketType = \App\Models\TicketType::factory()->create([
                'event_id' => $booking->event_id
            ]);
        }

        return $booking->items()->create([
            'quantity' => $quantity,
            'price' => $price,
            'ticket_type_id' => $ticketType->id
        ]);
    }
}
