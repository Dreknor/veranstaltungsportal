<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\EventDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for Events with multiple dates functionality
 * (Replaced EventSeries functionality)
 */
class EventWithMultipleDatesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function event_can_have_multiple_dates()
    {
        $event = Event::factory()->create([
            'has_multiple_dates' => true,
        ]);

        EventDate::factory()->count(5)->create([
            'event_id' => $event->id,
        ]);

        $this->assertTrue($event->hasMultipleDates());
        $this->assertCount(5, $event->dates);
    }

    #[Test]
    public function event_dates_are_ordered_by_start_date()
    {
        $event = Event::factory()->create([
            'has_multiple_dates' => true,
        ]);

        $date1 = EventDate::factory()->create([
            'event_id' => $event->id,
            'start_date' => now()->addDays(10),
        ]);
        $date2 = EventDate::factory()->create([
            'event_id' => $event->id,
            'start_date' => now()->addDays(5),
        ]);
        $date3 = EventDate::factory()->create([
            'event_id' => $event->id,
            'start_date' => now()->addDays(15),
        ]);

        $dates = $event->dates()->get();

        $this->assertEquals($date2->id, $dates->first()->id);
        $this->assertEquals($date3->id, $dates->last()->id);
    }

    #[Test]
    public function event_with_multiple_dates_shows_next_upcoming_date()
    {
        $event = Event::factory()->create([
            'has_multiple_dates' => true,
        ]);

        // Create past dates
        EventDate::factory()->past()->count(2)->create([
            'event_id' => $event->id,
        ]);

        // Create next upcoming date
        $nextDate = EventDate::factory()->create([
            'event_id' => $event->id,
            'start_date' => now()->addDays(3),
        ]);

        // Create future dates
        EventDate::factory()->count(2)->create([
            'event_id' => $event->id,
            'start_date' => now()->addDays(10),
        ]);

        $result = $event->getNextDate();

        $this->assertNotNull($result);
        $this->assertEquals($nextDate->id, $result->id);
    }

    #[Test]
    public function cancelled_event_date_is_marked_as_cancelled()
    {
        $event = Event::factory()->create([
            'has_multiple_dates' => true,
        ]);

        $cancelledDate = EventDate::factory()->cancelled()->create([
            'event_id' => $event->id,
        ]);

        $this->assertTrue($cancelledDate->is_cancelled);
        $this->assertNotNull($cancelledDate->cancellation_reason);
    }

    #[Test]
    public function event_date_can_have_different_venue_than_event()
    {
        $event = Event::factory()->create([
            'has_multiple_dates' => true,
            'venue_name' => 'Main Venue',
            'venue_city' => 'Munich',
        ]);

        $dateWithCustomVenue = EventDate::factory()->withCustomVenue()->create([
            'event_id' => $event->id,
        ]);

        $dateWithEventVenue = EventDate::factory()->create([
            'event_id' => $event->id,
            'venue_name' => null,
        ]);

        $this->assertNotEquals($event->venue_name, $dateWithCustomVenue->getRawOriginal('venue_name'));
        $this->assertNull($dateWithEventVenue->getRawOriginal('venue_name'));
        // But accessor should return event venue
        $this->assertEquals('Main Venue', $dateWithEventVenue->venue_name);
    }

    #[Test]
    public function single_event_returns_main_date_in_get_all_dates()
    {
        $event = Event::factory()->create([
            'has_multiple_dates' => false,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(5)->addHours(2),
        ]);

        $allDates = $event->getAllDates();

        $this->assertCount(1, $allDates);
        $this->assertEquals(
            $event->start_date->format('Y-m-d H:i'),
            $allDates->first()->start_date->format('Y-m-d H:i')
        );
    }

    #[Test]
    public function event_with_multiple_dates_capacity_applies_to_all_dates()
    {
        // This test documents the behavior that max_attendees applies to the entire event
        // not per individual date
        $event = Event::factory()->create([
            'has_multiple_dates' => true,
            'max_attendees' => 20,
        ]);

        EventDate::factory()->count(5)->create([
            'event_id' => $event->id,
        ]);

        // The same 20 people attend all 5 dates
        $this->assertEquals(20, $event->max_attendees);
        $this->assertCount(5, $event->dates);
    }
}





