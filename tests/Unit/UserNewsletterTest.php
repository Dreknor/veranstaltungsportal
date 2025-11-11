<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserNewsletterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_subscribe_to_newsletter()
    {
        $user = User::factory()->create([
            'newsletter_subscribed' => false,
            'newsletter_subscribed_at' => null,
        ]);

        $user->subscribeToNewsletter();

        $this->assertTrue($user->newsletter_subscribed);
        $this->assertNotNull($user->newsletter_subscribed_at);
    }

    /** @test */
    public function user_can_unsubscribe_from_newsletter()
    {
        $user = User::factory()->create([
            'newsletter_subscribed' => true,
            'newsletter_subscribed_at' => now(),
        ]);

        $user->unsubscribeFromNewsletter();

        $this->assertFalse($user->newsletter_subscribed);
    }

    /** @test */
    public function subscription_sets_current_timestamp()
    {
        $user = User::factory()->create([
            'newsletter_subscribed' => false,
            'newsletter_subscribed_at' => null,
        ]);

        $beforeSubscribe = now();
        $user->subscribeToNewsletter();
        $afterSubscribe = now();

        $this->assertTrue(
            $user->newsletter_subscribed_at->between($beforeSubscribe, $afterSubscribe)
        );
    }

    /** @test */
    public function user_can_check_if_interested_in_category()
    {
        $category = EventCategory::factory()->create();
        $user = User::factory()->create([
            'interested_category_ids' => [$category->id],
        ]);

        $this->assertTrue($user->isInterestedInCategory($category->id));
        $this->assertFalse($user->isInterestedInCategory(999));
    }

    /** @test */
    public function user_can_add_interest_to_category()
    {
        $category = EventCategory::factory()->create();
        $user = User::factory()->create([
            'interested_category_ids' => [],
        ]);

        $user->addInterest($category->id);

        $this->assertTrue($user->isInterestedInCategory($category->id));
        $this->assertContains($category->id, $user->interested_category_ids);
    }

    /** @test */
    public function adding_existing_interest_does_not_duplicate()
    {
        $category = EventCategory::factory()->create();
        $user = User::factory()->create([
            'interested_category_ids' => [$category->id],
        ]);

        $user->addInterest($category->id);

        $this->assertCount(1, $user->fresh()->interested_category_ids);
    }

    /** @test */
    public function user_can_remove_interest_from_category()
    {
        $category1 = EventCategory::factory()->create();
        $category2 = EventCategory::factory()->create();

        $user = User::factory()->create([
            'interested_category_ids' => [$category1->id, $category2->id],
        ]);

        $user->removeInterest($category1->id);

        $this->assertFalse($user->isInterestedInCategory($category1->id));
        $this->assertTrue($user->isInterestedInCategory($category2->id));
    }

    /** @test */
    public function removing_non_existent_interest_does_not_error()
    {
        $user = User::factory()->create([
            'interested_category_ids' => [1, 2, 3],
        ]);

        $user->removeInterest(999);

        $this->assertEquals([1, 2, 3], $user->fresh()->interested_category_ids);
    }

    /** @test */
    public function user_can_get_interested_categories_collection()
    {
        $category1 = EventCategory::factory()->create(['name' => 'Category 1']);
        $category2 = EventCategory::factory()->create(['name' => 'Category 2']);

        $user = User::factory()->create([
            'interested_category_ids' => [$category1->id, $category2->id],
        ]);

        $interestedCategories = $user->interestedCategories();

        $this->assertCount(2, $interestedCategories);
        $this->assertTrue($interestedCategories->contains('id', $category1->id));
        $this->assertTrue($interestedCategories->contains('id', $category2->id));
    }

    /** @test */
    public function interested_categories_returns_empty_collection_when_no_interests()
    {
        $user = User::factory()->create([
            'interested_category_ids' => [],
        ]);

        $interestedCategories = $user->interestedCategories();

        $this->assertCount(0, $interestedCategories);
    }

    /** @test */
    public function user_gets_recommended_events_based_on_interests()
    {
        $category1 = EventCategory::factory()->create();
        $category2 = EventCategory::factory()->create();

        $user = User::factory()->create([
            'interested_category_ids' => [$category1->id],
        ]);

        // Create events in interested category
        $interestedEvent = Event::factory()->create([
            'event_category_id' => $category1->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        // Create events in other category
        Event::factory()->create([
            'event_category_id' => $category2->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        $recommendations = $user->getRecommendedEvents(5);

        $this->assertCount(1, $recommendations);
        $this->assertEquals($interestedEvent->id, $recommendations->first()->id);
    }

    /** @test */
    public function user_with_no_interests_gets_popular_events()
    {
        $category = EventCategory::factory()->create();

        $user = User::factory()->create([
            'interested_category_ids' => [],
        ]);

        // Create events with different view counts
        $popularEvent = Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
            'views' => 100,
        ]);

        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
            'views' => 10,
        ]);

        $recommendations = $user->getRecommendedEvents(1);

        $this->assertCount(1, $recommendations);
        $this->assertEquals($popularEvent->id, $recommendations->first()->id);
    }

    /** @test */
    public function recommended_events_respects_limit()
    {
        $category = EventCategory::factory()->create();

        $user = User::factory()->create([
            'interested_category_ids' => [$category->id],
        ]);

        Event::factory()->count(10)->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        $recommendations = $user->getRecommendedEvents(3);

        $this->assertCount(3, $recommendations);
    }

    /** @test */
    public function recommended_events_only_includes_future_published_events()
    {
        $category = EventCategory::factory()->create();

        $user = User::factory()->create([
            'interested_category_ids' => [$category->id],
        ]);

        // Future published event - should be included
        $futureEvent = Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        // Past event - should not be included
        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->subDays(7),
        ]);

        // Unpublished future event - should not be included
        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => false,
            'start_date' => now()->addDays(7),
        ]);

        $recommendations = $user->getRecommendedEvents(10);

        $this->assertCount(1, $recommendations);
        $this->assertEquals($futureEvent->id, $recommendations->first()->id);
    }

    /** @test */
    public function recommended_events_are_sorted_by_start_date()
    {
        $category = EventCategory::factory()->create();

        $user = User::factory()->create([
            'interested_category_ids' => [$category->id],
        ]);

        $event1 = Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(10),
        ]);

        $event2 = Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(5),
        ]);

        $recommendations = $user->getRecommendedEvents(10);

        $this->assertEquals($event2->id, $recommendations->first()->id);
        $this->assertEquals($event1->id, $recommendations->last()->id);
    }
}

