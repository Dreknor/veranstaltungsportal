<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use App\Models\EventCategory;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_many_events()
    {
        $category = EventCategory::factory()->create();
        Event::factory()->count(5)->create(['event_category_id' => $category->id]);

        $this->assertCount(5, $category->events);
    }

    #[Test]
    public function it_has_slug()
    {
        $category = EventCategory::factory()->create([
            'name' => 'Technology Conference',
        ]);

        $this->assertNotNull($category->slug);
    }

    #[Test]
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



