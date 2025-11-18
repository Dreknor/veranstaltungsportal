<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Booking;
use App\Models\Event;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingCancelledNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_receives_notification_when_booking_confirmed()
    {
        Notification::fake();

        $user = User::factory()->create();
        $event = Event::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'pending',
        ]);

        // Confirm booking
        $booking->update(['status' => 'confirmed']);
        $booking->user->notify(new BookingConfirmedNotification($booking));

        Notification::assertSentTo($user, BookingConfirmedNotification::class);
    }

    #[Test]
    public function user_receives_notification_when_booking_cancelled()
    {
        Notification::fake();

        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
        ]);

        // Cancel booking
        $booking->update(['status' => 'cancelled']);
        $booking->user->notify(new BookingCancelledNotification($booking));

        Notification::assertSentTo($user, BookingCancelledNotification::class);
    }

    #[Test]
    public function user_can_view_notifications()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $user->notify(new BookingConfirmedNotification($booking));

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_mark_notification_as_read()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $user->notify(new BookingConfirmedNotification($booking));
        $notification = $user->notifications->first();

        $response = $this->actingAs($user)
            ->post(route('notifications.mark-read', $notification->id));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    #[Test]
    public function user_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        $booking1 = Booking::factory()->create(['user_id' => $user->id]);
        $booking2 = Booking::factory()->create(['user_id' => $user->id]);

        $user->notify(new BookingConfirmedNotification($booking1));
        $user->notify(new BookingConfirmedNotification($booking2));

        $response = $this->actingAs($user)->post(route('notifications.mark-all-read'));

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }
}



