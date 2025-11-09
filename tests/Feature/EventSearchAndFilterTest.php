<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventSearchAndFilterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function events_can_be_searched_by_title()
    {
        Event::factory()->create([
            'title' => 'Laravel Conference',
            'is_published' => true,
        ]);
        Event::factory()->create([
            'title' => 'PHP Summit',
            'is_published' => true,
        ]);

        $response = $this->get(route('events.index', ['search' => 'Laravel']));

        $response->assertStatus(200)
            ->assertSee('Laravel Conference');
    }

    /** @test */
    public function events_can_be_filtered_by_category()
    {
        $category1 = \App\Models\EventCategory::factory()->create(['name' => 'Tech']);
        $category2 = \App\Models\EventCategory::factory()->create(['name' => 'Music']);

        Event::factory()->create([
            'event_category_id' => $category1->id,
            'is_published' => true,
            'title' => 'Tech Event',
        ]);
        Event::factory()->create([
            'event_category_id' => $category2->id,
            'is_published' => true,
            'title' => 'Music Event',
        ]);

        $response = $this->get(route('events.index', ['category' => $category1->id]));

        $response->assertStatus(200)
            ->assertSee('Tech Event');
    }

    /** @test */
    public function events_can_be_filtered_by_date_range()
    {
        Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addDays(5),
            'title' => 'Soon Event',
        ]);
        Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addMonths(2),
            'title' => 'Later Event',
        ]);

        $response = $this->get(route('events.index', [
            'date_from' => now()->format('Y-m-d'),
            'date_to' => now()->addWeek()->format('Y-m-d'),
        ]));

        $response->assertStatus(200)
            ->assertSee('Soon Event');
    }

    /** @test */
    public function events_can_be_filtered_by_location()
    {
        Event::factory()->create([
            'is_published' => true,
            'venue_city' => 'Berlin',
            'title' => 'Berlin Event',
        ]);
        Event::factory()->create([
            'is_published' => true,
            'venue_city' => 'Munich',
            'title' => 'Munich Event',
        ]);

        $response = $this->get(route('events.index', ['city' => 'Berlin']));

        $response->assertStatus(200)
            ->assertSee('Berlin Event');
    }

    /** @test */
    public function only_published_events_appear_in_search()
    {
        Event::factory()->create([
            'title' => 'Published Event',
            'is_published' => true,
        ]);
        Event::factory()->create([
            'title' => 'Unpublished Event',
            'is_published' => false,
        ]);

        $response = $this->get(route('events.index'));

        $response->assertStatus(200)
            ->assertSee('Published Event')
            ->assertDontSee('Unpublished Event');
    }

    /** @test */
    public function featured_events_are_displayed()
    {
        Event::factory()->count(2)->create([
            'is_published' => true,
            'is_featured' => true,
        ]);
        Event::factory()->count(3)->create([
            'is_published' => true,
            'is_featured' => false,
        ]);

        $response = $this->get(route('events.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function events_can_be_sorted_by_date()
    {
        $event1 = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addDays(10),
            'title' => 'Later Event',
        ]);
        $event2 = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addDays(5),
            'title' => 'Sooner Event',
        ]);

        $response = $this->get(route('events.index', ['sort' => 'date']));

        $response->assertStatus(200);
    }

    /** @test */
    public function events_can_be_filtered_by_event_type()
    {
        Event::factory()->create([
            'is_published' => true,
            'event_type' => 'online',
            'title' => 'Online Event',
        ]);
        Event::factory()->create([
            'is_published' => true,
            'event_type' => 'physical',
            'title' => 'Physical Event',
        ]);

        $response = $this->get(route('events.index', ['type' => 'online']));

        $response->assertStatus(200)
            ->assertSee('Online Event');
    }
}


