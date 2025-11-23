<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organizer_can_create_ticket_type_for_their_event()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $ticketData = [
            'name' => 'VIP Ticket',
            'description' => 'VIP access to the event',
            'price' => 100,
            'quantity' => 50,
            'is_available' => true,
        ];

        $response = $this->actingAs($organizer)
            ->post(route('organizer.events.ticket-types.store', $event), $ticketData);

        $this->assertDatabaseHas('ticket_types', [
            'event_id' => $event->id,
            'name' => 'VIP Ticket',
            'price' => 100,
        ]);
    }

    #[Test]
    public function organizer_can_update_ticket_type()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);

        $updateData = [
            'name' => 'Updated Ticket Name',
            'description' => $ticketType->description,
            'price' => 150,
            'quantity' => $ticketType->quantity,
            'is_available' => true,
        ];

        $response = $this->actingAs($organizer)
            ->put(route('organizer.events.ticket-types.update', [$event, $ticketType]), $updateData);

        $this->assertDatabaseHas('ticket_types', [
            'id' => $ticketType->id,
            'name' => 'Updated Ticket Name',
            'price' => 150,
        ]);
    }

    #[Test]
    public function organizer_can_delete_ticket_type()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($organizer)
            ->delete(route('organizer.events.ticket-types.destroy', [$event, $ticketType]));

        $this->assertDatabaseMissing('ticket_types', [
            'id' => $ticketType->id,
        ]);
    }

    #[Test]
    public function organizer_cannot_modify_ticket_types_of_other_events()
    {
        $organizer1 = User::factory()->create(['user_type' => 'organizer']);
        $organizer2 = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer2->id]);
        $ticketType = TicketType::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($organizer1)
            ->delete(route('organizer.events.ticket-types.destroy', [$event, $ticketType]));

        $response->assertStatus(403);
    }

    #[Test]
    public function ticket_type_can_have_sale_period()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $ticketData = [
            'name' => 'Early Bird',
            'price' => 50,
            'quantity' => 100,
            'sale_start' => now()->format('Y-m-d H:i:s'),
            'sale_end' => now()->addWeek()->format('Y-m-d H:i:s'),
            'is_available' => true,
        ];

        $response = $this->actingAs($organizer)
            ->post(route('organizer.events.ticket-types.store', $event), $ticketData);

        $this->assertDatabaseHas('ticket_types', [
            'event_id' => $event->id,
            'name' => 'Early Bird',
        ]);
    }

    #[Test]
    public function ticket_type_can_have_min_max_per_order()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $ticketData = [
            'name' => 'Group Ticket',
            'price' => 200,
            'quantity' => 50,
            'min_per_order' => 4,
            'max_per_order' => 10,
            'is_available' => true,
        ];

        $response = $this->actingAs($organizer)
            ->post(route('organizer.events.ticket-types.store', $event), $ticketData);

        $this->assertDatabaseHas('ticket_types', [
            'event_id' => $event->id,
            'min_per_order' => 4,
            'max_per_order' => 10,
        ]);
    }
}



