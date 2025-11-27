<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use App\Models\Event;
use App\Models\User;
use App\Models\EventCategory;
use App\Models\EventDate;
use App\Models\TicketType;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $event->user);
        $this->assertEquals($user->id, $event->user->id);
    }

    #[Test]
    public function it_belongs_to_a_category()
    {
        $category = EventCategory::factory()->create();
        $event = Event::factory()->create(['event_category_id' => $category->id]);

        $this->assertInstanceOf(EventCategory::class, $event->category);
        $this->assertEquals($category->id, $event->category->id);
    }

    #[Test]
    public function it_can_have_multiple_dates()
    {
        $event = Event::factory()->create([
            'has_multiple_dates' => true,
        ]);

        EventDate::factory()->count(3)->create(['event_id' => $event->id]);

        $this->assertTrue($event->hasMultipleDates());
        $this->assertCount(3, $event->dates);
    }

    #[Test]
    public function it_has_many_ticket_types()
    {
        $event = Event::factory()->create();
        TicketType::factory()->count(3)->create(['event_id' => $event->id]);

        $this->assertCount(3, $event->ticketTypes);
    }

    #[Test]
    public function it_has_many_bookings()
    {
        $event = Event::factory()->create();
        Booking::factory()->count(2)->create(['event_id' => $event->id]);

        $this->assertCount(2, $event->bookings);
    }

    #[Test]
    public function it_calculates_available_tickets_correctly()
    {
        $event = Event::factory()->create(['max_attendees' => 100]);
        $ticketType = \App\Models\TicketType::factory()->create(['event_id' => $event->id]);

        $booking1 = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed'
        ]);
        $booking1->items()->create(['quantity' => 10, 'price' => 50, 'ticket_type_id' => $ticketType->id]);

        $booking2 = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed'
        ]);
        $booking2->items()->create(['quantity' => 15, 'price' => 50, 'ticket_type_id' => $ticketType->id]);

        $this->assertEquals(75, $event->availableTickets());
    }

    #[Test]
    public function it_returns_unlimited_tickets_when_max_attendees_is_null()
    {
        $event = Event::factory()->create(['max_attendees' => null]);

        $this->assertEquals(PHP_INT_MAX, $event->availableTickets());
    }

    #[Test]
    public function it_checks_if_event_has_available_tickets()
    {
        $event = Event::factory()->create(['max_attendees' => 10]);
        $ticketType = \App\Models\TicketType::factory()->create(['event_id' => $event->id]);

        $this->assertTrue($event->hasAvailableTickets());

        $booking = Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed'
        ]);
        $booking->items()->create(['quantity' => 10, 'price' => 50, 'ticket_type_id' => $ticketType->id]);

        $event->refresh();
        $this->assertFalse($event->hasAvailableTickets());
    }

    #[Test]
    public function it_identifies_online_events()
    {
        $event = Event::factory()->create(['event_type' => 'online']);

        $this->assertTrue($event->isOnline());
        $this->assertFalse($event->isPhysical());
        $this->assertFalse($event->isHybrid());
        $this->assertTrue($event->requiresOnlineInfo());
        $this->assertFalse($event->requiresVenue());
    }

    #[Test]
    public function it_identifies_physical_events()
    {
        $event = Event::factory()->create(['event_type' => 'physical']);

        $this->assertTrue($event->isPhysical());
        $this->assertFalse($event->isOnline());
        $this->assertFalse($event->isHybrid());
        $this->assertTrue($event->requiresVenue());
        $this->assertFalse($event->requiresOnlineInfo());
    }

    #[Test]
    public function it_identifies_hybrid_events()
    {
        $event = Event::factory()->create(['event_type' => 'hybrid']);

        $this->assertTrue($event->isHybrid());
        $this->assertFalse($event->isOnline());
        $this->assertFalse($event->isPhysical());
        $this->assertTrue($event->requiresVenue());
        $this->assertTrue($event->requiresOnlineInfo());
    }

    #[Test]
    public function it_gets_attendees_count()
    {
        $event = Event::factory()->create();

        Booking::factory()->count(3)->create([
            'event_id' => $event->id,
            'status' => 'confirmed'
        ]);

        Booking::factory()->count(2)->create([
            'event_id' => $event->id,
            'status' => 'pending'
        ]);

        $this->assertEquals(3, $event->getAttendeesCount());
    }

    #[Test]
    public function it_checks_if_event_has_attendees()
    {
        $event = Event::factory()->create();

        $this->assertFalse($event->hasAttendees());

        Booking::factory()->create([
            'event_id' => $event->id,
            'status' => 'confirmed'
        ]);

        $event->refresh();
        $this->assertTrue($event->hasAttendees());
    }

    #[Test]
    public function it_can_be_booked_when_conditions_are_met()
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'is_cancelled' => false,
            'start_date' => now()->addWeek(),
            'max_attendees' => 100,
        ]);

        $this->assertTrue($event->canBeBooked());
    }

    #[Test]
    public function it_cannot_be_booked_when_cancelled()
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'is_cancelled' => true,
            'start_date' => now()->addWeek(),
            'max_attendees' => 100,
        ]);

        $this->assertFalse($event->canBeBooked());
    }

    #[Test]
    public function it_cannot_be_booked_when_not_published()
    {
        $event = Event::factory()->create([
            'is_published' => false,
            'is_cancelled' => false,
            'start_date' => now()->addWeek(),
            'max_attendees' => 100,
        ]);

        $this->assertFalse($event->canBeBooked());
    }

    #[Test]
    public function it_cannot_be_booked_when_in_the_past()
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'is_cancelled' => false,
            'start_date' => now()->subWeek(),
            'max_attendees' => 100,
        ]);

        $this->assertFalse($event->canBeBooked());
    }

    #[Test]
    public function it_has_location_attribute()
    {
        $event = Event::factory()->create([
            'venue_name' => 'Kongresshalle',
            'venue_address' => 'Musterstraße 123',
            'venue_city' => 'Berlin',
        ]);

        $this->assertEquals('Kongresshalle, Musterstraße 123, Berlin', $event->location);
    }

    #[Test]
    public function it_scopes_published_events()
    {
        Event::factory()->count(3)->create(['is_published' => true]);
        Event::factory()->count(2)->create(['is_published' => false]);

        $this->assertCount(3, Event::published()->get());
    }

    #[Test]
    public function it_scopes_upcoming_events()
    {
        Event::factory()->count(2)->create(['start_date' => now()->addWeek()]);
        Event::factory()->count(3)->create(['start_date' => now()->subWeek()]);

        $this->assertCount(2, Event::upcoming()->get());
    }

    #[Test]
    public function it_scopes_featured_events()
    {
        Event::factory()->count(2)->create(['is_featured' => true]);
        Event::factory()->count(3)->create(['is_featured' => false]);

        $this->assertCount(2, Event::featured()->get());
    }
}



