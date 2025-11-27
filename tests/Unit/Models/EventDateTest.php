<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Event;
use App\Models\EventDate;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests for EventDate model (multiple dates per event)
 */
class EventDateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_an_event()
    {
        $event = Event::factory()->create(['has_multiple_dates' => true]);
        $eventDate = EventDate::factory()->create(['event_id' => $event->id]);

        $this->assertInstanceOf(Event::class, $eventDate->event);
        $this->assertEquals($event->id, $eventDate->event->id);
    }

    #[Test]
    public function it_can_have_custom_venue()
    {
        $event = Event::factory()->create([
            'has_multiple_dates' => true,
            'venue_name' => 'Main Venue',
        ]);

        $eventDate = EventDate::factory()->create([
            'event_id' => $event->id,
            'venue_name' => 'Alternative Venue',
            'venue_city' => 'Berlin',
        ]);

        $this->assertEquals('Alternative Venue', $eventDate->venue_name);
        $this->assertEquals('Berlin', $eventDate->venue_city);
    }

    #[Test]
    public function it_falls_back_to_event_venue_if_not_specified()
    {
        $event = Event::factory()->create([
            'has_multiple_dates' => true,
            'venue_name' => 'Main Venue',
            'venue_city' => 'Munich',
        ]);

        $eventDate = EventDate::factory()->create([
            'event_id' => $event->id,
            'venue_name' => null,
            'venue_city' => null,
        ]);

        // Accessors should fall back to event venue
        $this->assertEquals('Main Venue', $eventDate->venue_name);
        $this->assertEquals('Munich', $eventDate->venue_city);
    }

    #[Test]
    public function it_can_be_cancelled()
    {
        $event = Event::factory()->create(['has_multiple_dates' => true]);
        $eventDate = EventDate::factory()->cancelled()->create(['event_id' => $event->id]);

        $this->assertTrue($eventDate->is_cancelled);
        $this->assertNotNull($eventDate->cancellation_reason);
    }

    #[Test]
    public function it_can_determine_if_upcoming()
    {
        $event = Event::factory()->create(['has_multiple_dates' => true]);
        $upcomingDate = EventDate::factory()->upcoming()->create(['event_id' => $event->id]);
        $pastDate = EventDate::factory()->past()->create(['event_id' => $event->id]);

        $this->assertTrue($upcomingDate->isUpcoming());
        $this->assertFalse($pastDate->isUpcoming());
        $this->assertTrue($pastDate->isPast());
    }

    #[Test]
    public function event_can_have_multiple_dates()
    {
        $event = Event::factory()->create(['has_multiple_dates' => true]);

        EventDate::factory()->count(5)->create(['event_id' => $event->id]);

        $this->assertTrue($event->hasMultipleDates());
        $this->assertCount(5, $event->dates);
    }

    #[Test]
    public function event_returns_next_upcoming_date()
    {
        $event = Event::factory()->create(['has_multiple_dates' => true]);

        // Create past and future dates
        EventDate::factory()->past()->count(2)->create(['event_id' => $event->id]);
        $nextDate = EventDate::factory()->upcoming()->create([
            'event_id' => $event->id,
            'start_date' => now()->addDays(5),
        ]);
        EventDate::factory()->create([
            'event_id' => $event->id,
            'start_date' => now()->addDays(10),
        ]);

        $next = $event->getNextDate();

        $this->assertNotNull($next);
        $this->assertEquals($nextDate->id, $next->id);
    }
}

