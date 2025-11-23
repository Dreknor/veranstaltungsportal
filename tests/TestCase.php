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

        // Disable ReCaptcha for testing
        config(['recaptcha.enabled' => false]);
    }

    /**
     * Create required roles for tests
     */
    protected function createRolesIfNeeded(): void
    {
        $roles = ['admin', 'organizer', 'participant', 'user'];

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

    /**
     * Create an organizer with an organization
     * Returns array with 'organizer' and 'organization'
     */
    protected function createOrganizerWithOrganization($organizer = null)
    {
        if (!$organizer) {
            $organizer = \App\Models\User::factory()->create();
        }

        // Ensure organizer role exists and assign it
        if (!\Spatie\Permission\Models\Role::where('name', 'organizer')->exists()) {
            \Spatie\Permission\Models\Role::create(['name' => 'organizer', 'guard_name' => 'web']);
        }
        $organizer->assignRole('organizer');

        $organization = \App\Models\Organization::factory()->create();

        $organization->users()->attach($organizer->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        // Set current organization in session for tests
        session(['current_organization_id' => $organization->id]);

        return [
            'organizer' => $organizer,
            'organization' => $organization,
        ];
    }

    /**
     * Create an organization for a user
     */
    protected function createOrganization($user)
    {
        $organization = \App\Models\Organization::factory()->create();

        $organization->users()->attach($user->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return $organization;
    }
}

