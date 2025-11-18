<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        $this->createRolesIfNeeded();
    }

    /**
     * Create required roles for tests
     */
    protected function createRolesIfNeeded(): void
    {
        $roles = ['admin', 'organizer', 'participant'];

        foreach ($roles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
            }
        }
    }

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
