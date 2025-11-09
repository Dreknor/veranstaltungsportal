<?php

namespace Tests\Unit\Models;

use App\Models\EventCategory;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_events()
    {
        $category = EventCategory::factory()->create();
        Event::factory()->count(5)->create(['event_category_id' => $category->id]);

        $this->assertCount(5, $category->events);
    }

    /** @test */
    public function it_has_slug()
    {
        $category = EventCategory::factory()->create([
            'name' => 'Technology Conference',
        ]);

        $this->assertNotNull($category->slug);
    }

    /** @test */
    public function it_can_have_parent_category()
    {
        $parent = EventCategory::factory()->create();
        $child = EventCategory::factory()->create(['parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent_id);
    }

    /** @test */
    public function it_can_have_child_categories()
    {
        $parent = EventCategory::factory()->create();
        EventCategory::factory()->count(3)->create(['parent_id' => $parent->id]);

        $this->assertCount(3, $parent->children);
    }

    /** @test */
    public function it_counts_events_correctly()
    {
        $category = EventCategory::factory()->create();
        Event::factory()->count(7)->create([
            'event_category_id' => $category->id,
            'is_published' => true,
        ]);

        $this->assertEquals(7, $category->events()->count());
    }
}

