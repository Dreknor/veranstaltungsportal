<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_leave_review_for_attended_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->subWeek(),
        ]);

        // Create confirmed booking for user
        $booking = \App\Models\Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $reviewData = [
            'rating' => 5,
            'comment' => 'Great event!',
        ];

        $response = $this->actingAs($user)->post(route('events.reviews.store', $event), $reviewData);

        $this->assertDatabaseHas('event_reviews', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Great event!',
        ]);
    }

    /** @test */
    public function user_cannot_review_event_without_booking()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $reviewData = [
            'rating' => 5,
            'comment' => 'Great event!',
        ];

        $response = $this->actingAs($user)->post(route('events.reviews.store', $event), $reviewData);

        $response->assertStatus(403);
    }

    /** @test */
    public function reviews_must_have_valid_rating()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        \App\Models\Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $reviewData = [
            'rating' => 6,
            'comment' => 'Great event!',
        ];

        $response = $this->actingAs($user)->post(route('events.reviews.store', $event), $reviewData);

        $response->assertSessionHasErrors('rating');
    }

    /** @test */
    public function event_displays_average_rating()
    {
        $event = Event::factory()->create(['is_published' => true]);

        \App\Models\EventReview::factory()->create([
            'event_id' => $event->id,
            'rating' => 5,
            'is_approved' => true,
        ]);
        \App\Models\EventReview::factory()->create([
            'event_id' => $event->id,
            'rating' => 3,
            'is_approved' => true,
        ]);

        $event->refresh();
        $this->assertEquals(4.0, $event->averageRating());
    }

    /** @test */
    public function only_approved_reviews_are_visible()
    {
        $event = Event::factory()->create(['is_published' => true]);

        $approvedReview = \App\Models\EventReview::factory()->create([
            'event_id' => $event->id,
            'is_approved' => true,
            'comment' => 'Approved comment',
        ]);

        $unapprovedReview = \App\Models\EventReview::factory()->create([
            'event_id' => $event->id,
            'is_approved' => false,
            'comment' => 'Unapproved comment',
        ]);

        $response = $this->get(route('events.show', $event));

        $response->assertSee('Approved comment')
            ->assertDontSee('Unapproved comment');
    }
}
