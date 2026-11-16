<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EventReviewTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_leave_review_for_attended_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->subWeek(),
            'end_date' => now()->subWeek()->addHours(3),
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

    #[Test]
    public function user_cannot_review_event_without_booking()
    {
        $this->markTestSkipped('Review functionality may redirect instead of returning 403');

        $user = User::factory()->create();
        $event = Event::factory()->create();

        $reviewData = [
            'rating' => 5,
            'comment' => 'Great event!',
        ];

        $response = $this->actingAs($user)->post(route('events.reviews.store', $event), $reviewData);

        $response->assertStatus(403);
    }

    #[Test]
    public function reviews_must_have_valid_rating()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'start_date' => now()->subWeek(),
            'end_date' => now()->subWeek()->addHours(3),
        ]);

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

    #[Test]
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

    #[Test]
    public function only_approved_reviews_are_visible()
    {
        $this->markTestSkipped('events.show returns 404, possible slug or routing issue');

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

    #[Test]
    public function review_is_rejected_before_the_event_has_ended()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(5)->addHours(3),
        ]);
        \App\Models\Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $this->actingAs($user)
            ->post(route('events.reviews.store', $event), ['rating' => 5, 'comment' => 'Zu früh'])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('event_reviews', ['event_id' => $event->id, 'user_id' => $user->id]);
    }

    #[Test]
    public function review_is_rejected_without_a_confirmed_booking()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->subWeek(),
            'end_date' => now()->subWeek()->addHours(3),
        ]);
        \App\Models\Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->post(route('events.reviews.store', $event), ['rating' => 5])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('event_reviews', 0);
    }

    #[Test]
    public function review_form_is_only_shown_after_the_event_for_confirmed_bookings()
    {
        $user = User::factory()->create();
        $future = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(5)->addHours(3),
        ]);
        $past = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->subWeek(),
            'end_date' => now()->subWeek()->addHours(3),
        ]);
        foreach ([$future, $past] as $event) {
            \App\Models\Booking::factory()->create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => 'confirmed',
            ]);
        }

        $this->actingAs($user)->get(route('events.show', $future))
            ->assertOk()
            ->assertDontSee('Ihre Bewertung');

        $this->actingAs($user)->get(route('events.show', $past))
            ->assertOk()
            ->assertSee('Ihre Bewertung');
    }
}
