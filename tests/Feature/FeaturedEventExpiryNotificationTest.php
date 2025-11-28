<?php

namespace Tests\Feature;

use App\Console\Commands\NotifyFeaturedExpiry;
use App\Mail\FeaturedExpiryReminder;
use App\Models\Event;
use App\Models\FeaturedEventFee;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FeaturedEventExpiryNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Organization $organization;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user with organization
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();
        $this->organization->users()->attach($this->user->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        // Create event
        $this->event = Event::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
            'is_published' => true,
        ]);
    }

    /** @test */
    public function it_sends_notification_for_featured_event_expiring_in_3_days()
    {
        Mail::fake();

        // Create featured fee expiring in exactly 3 days
        $fee = FeaturedEventFee::create([
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'duration_type' => 'weekly',
            'duration_days' => 7,
            'featured_start_date' => now()->subDays(4),
            'featured_end_date' => now()->addDays(3),
            'fee_amount' => 50.00,
            'payment_status' => 'paid',
            'paid_at' => now()->subDays(4),
        ]);

        // Run command
        $this->artisan('featured:notify-expiry')
            ->assertExitCode(0);

        // Assert email was sent
        Mail::assertSent(FeaturedExpiryReminder::class, function ($mail) use ($fee) {
            return $mail->featuredEventFee->id === $fee->id
                && $mail->hasTo($this->user->email);
        });

        // Assert fee was marked as notified
        $this->assertNotNull($fee->fresh()->expiry_notified_at);
    }

    /** @test */
    public function it_does_not_send_notification_for_non_expiring_events()
    {
        Mail::fake();

        // Create featured fee expiring in 7 days (not 3)
        FeaturedEventFee::create([
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'duration_type' => 'weekly',
            'duration_days' => 7,
            'featured_start_date' => now(),
            'featured_end_date' => now()->addDays(7),
            'fee_amount' => 50.00,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->artisan('featured:notify-expiry')
            ->assertExitCode(0);

        Mail::assertNotSent(FeaturedExpiryReminder::class);
    }

    /** @test */
    public function it_does_not_send_notification_twice()
    {
        Mail::fake();

        // Create featured fee already notified
        $fee = FeaturedEventFee::create([
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'duration_type' => 'weekly',
            'duration_days' => 7,
            'featured_start_date' => now()->subDays(4),
            'featured_end_date' => now()->addDays(3),
            'fee_amount' => 50.00,
            'payment_status' => 'paid',
            'paid_at' => now()->subDays(4),
            'expiry_notified_at' => now()->subDay(),
        ]);

        $this->artisan('featured:notify-expiry')
            ->assertExitCode(0);

        Mail::assertNotSent(FeaturedExpiryReminder::class);
    }

    /** @test */
    public function it_does_not_send_notification_for_unpaid_fees()
    {
        Mail::fake();

        // Create unpaid featured fee
        FeaturedEventFee::create([
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'duration_type' => 'weekly',
            'duration_days' => 7,
            'featured_start_date' => now()->subDays(4),
            'featured_end_date' => now()->addDays(3),
            'fee_amount' => 50.00,
            'payment_status' => 'pending',
        ]);

        $this->artisan('featured:notify-expiry')
            ->assertExitCode(0);

        Mail::assertNotSent(FeaturedExpiryReminder::class);
    }

    /** @test */
    public function expiry_reminder_email_contains_correct_data()
    {
        $fee = FeaturedEventFee::create([
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'duration_type' => 'weekly',
            'duration_days' => 7,
            'featured_start_date' => now()->subDays(4),
            'featured_end_date' => now()->addDays(3),
            'fee_amount' => 50.00,
            'payment_status' => 'paid',
            'paid_at' => now()->subDays(4),
        ]);

        $mail = new FeaturedExpiryReminder($fee);

        $this->assertEquals(
            'Featured-Status läuft bald ab: ' . $this->event->title,
            $mail->envelope()->subject
        );

        $content = $mail->content();
        $this->assertEquals('emails.featured.expiry-reminder', $content->view);
        $this->assertArrayHasKey('fee', $content->with);
        $this->assertArrayHasKey('event', $content->with);
        $this->assertArrayHasKey('user', $content->with);
    }

    /** @test */
    public function it_handles_multiple_expiring_fees()
    {
        Mail::fake();

        $user2 = User::factory()->create();
        $event2 = Event::factory()->create([
            'user_id' => $user2->id,
            'organization_id' => $this->organization->id,
        ]);

        // Create two featured fees expiring in 3 days
        FeaturedEventFee::create([
            'event_id' => $this->event->id,
            'user_id' => $this->user->id,
            'duration_type' => 'weekly',
            'duration_days' => 7,
            'featured_start_date' => now()->subDays(4),
            'featured_end_date' => now()->addDays(3),
            'fee_amount' => 50.00,
            'payment_status' => 'paid',
            'paid_at' => now()->subDays(4),
        ]);

        FeaturedEventFee::create([
            'event_id' => $event2->id,
            'user_id' => $user2->id,
            'duration_type' => 'monthly',
            'duration_days' => 30,
            'featured_start_date' => now()->subDays(27),
            'featured_end_date' => now()->addDays(3),
            'fee_amount' => 150.00,
            'payment_status' => 'paid',
            'paid_at' => now()->subDays(27),
        ]);

        $this->artisan('featured:notify-expiry')
            ->assertExitCode(0);

        // Assert both emails were queued
        Mail::assertQueued(FeaturedExpiryReminder::class, 2);
    }
}

