<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_favorite_an_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['is_published' => true]);

        $response = $this->actingAs($user)->post(route('events.favorite', $event));

        $this->assertTrue($user->hasFavorited($event));
    }

    /** @test */
    public function user_can_unfavorite_an_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['is_published' => true]);

        // Favorite first
        $user->favoriteEvents()->attach($event);

        $response = $this->actingAs($user)->delete(route('events.unfavorite', $event));

        $this->assertFalse($user->hasFavorited($event));
    }

    /** @test */
    public function user_can_view_their_favorite_events()
    {
        $user = User::factory()->create();
        $events = Event::factory()->count(3)->create(['is_published' => true]);

        foreach ($events as $event) {
            $user->favoriteEvents()->attach($event);
        }

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function guest_cannot_favorite_events()
    {
        $event = Event::factory()->create(['is_published' => true]);

        $response = $this->post(route('events.favorite', $event));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function favorite_count_is_tracked()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $event = Event::factory()->create(['is_published' => true]);

        $user1->favoriteEvents()->attach($event);
        $user2->favoriteEvents()->attach($event);

        $this->assertEquals(2, $event->favoriteUsers()->count());
    }
}

