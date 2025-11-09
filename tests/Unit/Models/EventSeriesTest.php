<?php

namespace Tests\Unit\Models;

use App\Models\EventSeries;
use App\Models\User;
use App\Models\EventCategory;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventSeriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $series = EventSeries::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $series->user);
        $this->assertEquals($user->id, $series->user->id);
    }

    /** @test */
    public function it_belongs_to_a_category()
    {
        $category = EventCategory::factory()->create();
        $series = EventSeries::factory()->create(['event_category_id' => $category->id]);

        $this->assertInstanceOf(EventCategory::class, $series->category);
        $this->assertEquals($category->id, $series->category->id);
    }

    /** @test */
    public function it_has_many_events()
    {
        $series = EventSeries::factory()->create();
        Event::factory()->count(5)->create([
            'series_id' => $series->id,
            'is_series_part' => true,
        ]);

        $this->assertCount(5, $series->events);
    }

    /** @test */
    public function it_returns_active_events()
    {
        $series = EventSeries::factory()->create();

        Event::factory()->count(3)->create([
            'series_id' => $series->id,
            'is_series_part' => true,
            'is_published' => true,
        ]);

        Event::factory()->count(2)->create([
            'series_id' => $series->id,
            'is_series_part' => true,
            'is_published' => false,
        ]);

        $this->assertCount(3, $series->activeEvents);
    }

    /** @test */
    public function it_returns_upcoming_events()
    {
        $series = EventSeries::factory()->create();

        Event::factory()->count(2)->create([
            'series_id' => $series->id,
            'is_series_part' => true,
            'start_date' => now()->addWeek(),
        ]);

        Event::factory()->count(3)->create([
            'series_id' => $series->id,
            'is_series_part' => true,
            'start_date' => now()->subWeek(),
        ]);

        $this->assertCount(2, $series->upcomingEvents);
    }

    /** @test */
    public function it_casts_recurrence_days_to_array()
    {
        $series = EventSeries::factory()->create([
            'recurrence_days' => [1, 3, 5],
        ]);

        $this->assertIsArray($series->recurrence_days);
        $this->assertEquals([1, 3, 5], $series->recurrence_days);
    }

    /** @test */
    public function it_casts_template_data_to_array()
    {
        $series = EventSeries::factory()->create([
            'template_data' => ['venue' => 'Main Hall', 'duration' => 120],
        ]);

        $this->assertIsArray($series->template_data);
        $this->assertEquals('Main Hall', $series->template_data['venue']);
    }
}
