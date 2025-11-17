<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\EventSeries;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventSeriesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organizer_can_create_event_series()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $category = \App\Models\EventCategory::factory()->create();

        $seriesData = [
            'title' => 'Weekly Workshop Series',
            'description' => 'A series of weekly workshops',
            'event_category_id' => $category->id,
            'recurrence_type' => 'weekly',
            'recurrence_interval' => 1,
            'recurrence_count' => 10,
        ];

        $response = $this->actingAs($organizer)->post(route('organizer.series.store'), $seriesData);

        $this->assertDatabaseHas('event_series', [
            'title' => 'Weekly Workshop Series',
            'user_id' => $organizer->id,
        ]);
    }

    #[Test]
    public function organizer_can_view_their_series()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        EventSeries::factory()->count(3)->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.series.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function series_can_have_multiple_events()
    {
        $series = EventSeries::factory()->create();
        Event::factory()->count(5)->create([
            'series_id' => $series->id,
            'is_series_part' => true,
        ]);

        $this->assertCount(5, $series->events);
    }

    #[Test]
    public function events_in_series_are_ordered_by_position()
    {
        $series = EventSeries::factory()->create();

        $event1 = Event::factory()->create([
            'series_id' => $series->id,
            'is_series_part' => true,
            'series_position' => 1,
        ]);
        $event2 = Event::factory()->create([
            'series_id' => $series->id,
            'is_series_part' => true,
            'series_position' => 2,
        ]);

        $events = $series->events;

        $this->assertEquals(1, $events->first()->series_position);
        $this->assertEquals(2, $events->last()->series_position);
    }

    #[Test]
    public function organizer_can_update_series()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $series = EventSeries::factory()->create(['user_id' => $organizer->id]);

        $updateData = [
            'title' => 'Updated Series Title',
            'description' => $series->description,
            'event_category_id' => $series->event_category_id,
            'recurrence_type' => $series->recurrence_type,
        ];

        $response = $this->actingAs($organizer)->put(route('organizer.series.update', $series), $updateData);

        $this->assertDatabaseHas('event_series', [
            'id' => $series->id,
            'title' => 'Updated Series Title',
        ]);
    }

    #[Test]
    public function organizer_can_delete_series()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $series = EventSeries::factory()->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->delete(route('organizer.series.destroy', $series));

        $this->assertDatabaseMissing('event_series', [
            'id' => $series->id,
        ]);
    }

    #[Test]
    public function organizer_cannot_modify_other_organizers_series()
    {
        $organizer1 = User::factory()->create(['user_type' => 'organizer']);
        $organizer2 = User::factory()->create(['user_type' => 'organizer']);
        $series = EventSeries::factory()->create(['user_id' => $organizer2->id]);

        $response = $this->actingAs($organizer1)->get(route('organizer.series.edit', $series));

        $response->assertStatus(403);
    }
}




