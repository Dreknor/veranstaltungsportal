<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\EventCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_subscribe_to_newsletter()
    {
        $user = User::factory()->create([
            'newsletter_subscribed' => false,
        ]);

        $response = $this->actingAs($user)
            ->post(route('newsletter.subscribe'));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Sie haben den Newsletter erfolgreich abonniert!');

        $this->assertTrue($user->fresh()->newsletter_subscribed);
        $this->assertNotNull($user->fresh()->newsletter_subscribed_at);
    }

    #[Test]
    public function authenticated_user_can_unsubscribe_from_newsletter()
    {
        $user = User::factory()->create([
            'newsletter_subscribed' => true,
            'newsletter_subscribed_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->post(route('newsletter.subscribe'));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Sie haben den Newsletter erfolgreich abbestellt.');

        $this->assertFalse($user->fresh()->newsletter_subscribed);
    }

    #[Test]
    public function user_can_update_interests()
    {
        $user = User::factory()->create();
        $categories = EventCategory::factory()->count(3)->create();

        $categoryIds = $categories->pluck('id')->toArray();

        $response = $this->actingAs($user)
            ->post(route('newsletter.interests'), [
                'category_ids' => $categoryIds,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Ihre Interessen wurden aktualisiert.');

        $this->assertEquals($categoryIds, $user->fresh()->interested_category_ids);
    }

    #[Test]
    public function user_can_clear_interests()
    {
        $user = User::factory()->create([
            'interested_category_ids' => [1, 2, 3],
        ]);

        $response = $this->actingAs($user)
            ->post(route('newsletter.interests'), [
                'category_ids' => [],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals([], $user->fresh()->interested_category_ids);
    }

    #[Test]
    public function interests_must_be_valid_category_ids()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('newsletter.interests'), [
                'category_ids' => [999], // Non-existent category
            ]);

        $response->assertSessionHasErrors('category_ids.0');
    }

    #[Test]
    public function user_can_view_interests_settings_page()
    {
        $user = User::factory()->create();
        EventCategory::factory()->count(3)->create(['is_active' => true]);

        $response = $this->actingAs($user)
            ->get(route('settings.interests.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('settings.interests');
        $response->assertViewHas(['categories', 'recommendedEvents']);
    }

    #[Test]
    public function interests_page_shows_only_active_categories()
    {
        $user = User::factory()->create();

        EventCategory::factory()->create(['is_active' => true, 'name' => 'Active Category']);
        EventCategory::factory()->create(['is_active' => false, 'name' => 'Inactive Category']);

        $response = $this->actingAs($user)
            ->get(route('settings.interests.edit'));

        $categories = $response->viewData('categories');

        $this->assertEquals(1, $categories->count());
        $this->assertEquals('Active Category', $categories->first()->name);
    }

    #[Test]
    public function guest_cannot_access_interests_settings()
    {
        $response = $this->get(route('settings.interests.edit'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_subscribe_to_newsletter()
    {
        $response = $this->post(route('newsletter.subscribe'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function newsletter_subscription_creates_record_with_timestamp()
    {
        $user = User::factory()->create([
            'newsletter_subscribed' => false,
            'newsletter_subscribed_at' => null,
        ]);

        $this->actingAs($user)
            ->post(route('newsletter.subscribe'));

        $user = $user->fresh();

        $this->assertTrue($user->newsletter_subscribed);
        $this->assertNotNull($user->newsletter_subscribed_at);
        $this->assertTrue($user->newsletter_subscribed_at->isToday());
    }

    #[Test]
    public function user_can_check_if_interested_in_category()
    {
        $user = User::factory()->create();
        $category = EventCategory::factory()->create();

        $this->assertFalse($user->isInterestedInCategory($category->id));

        $user->interested_category_ids = [$category->id];
        $user->save();

        $this->assertTrue($user->isInterestedInCategory($category->id));
    }

    #[Test]
    public function user_can_add_interest()
    {
        $user = User::factory()->create(['interested_category_ids' => []]);
        $category = EventCategory::factory()->create();

        $user->addInterest($category->id);

        $this->assertTrue($user->isInterestedInCategory($category->id));
    }

    #[Test]
    public function adding_interest_twice_does_not_duplicate()
    {
        $user = User::factory()->create(['interested_category_ids' => []]);
        $category = EventCategory::factory()->create();

        $user->addInterest($category->id);
        $user->addInterest($category->id);

        $this->assertCount(1, $user->interested_category_ids);
    }

    #[Test]
    public function user_can_remove_interest()
    {
        $category = EventCategory::factory()->create();
        $user = User::factory()->create([
            'interested_category_ids' => [$category->id],
        ]);

        $user->removeInterest($category->id);

        $this->assertFalse($user->isInterestedInCategory($category->id));
        $this->assertEmpty($user->interested_category_ids);
    }

    #[Test]
    public function user_can_get_interested_categories()
    {
        $categories = EventCategory::factory()->count(3)->create();
        $categoryIds = $categories->pluck('id')->toArray();

        $user = User::factory()->create([
            'interested_category_ids' => $categoryIds,
        ]);

        $interestedCategories = $user->interestedCategories();

        $this->assertCount(3, $interestedCategories);
        $this->assertEquals($categoryIds, $interestedCategories->pluck('id')->toArray());
    }

    #[Test]
    public function newsletter_unsubscribe_route_works()
    {
        $user = User::factory()->create([
            'newsletter_subscribed' => true,
        ]);

        $response = $this->actingAs($user)
            ->post(route('newsletter.unsubscribe'));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Sie haben den Newsletter abbestellt.');

        $this->assertFalse($user->fresh()->newsletter_subscribed);
    }
}



