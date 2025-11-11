<?php

namespace Tests\Feature\Console;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendNewsletterCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    /** @test */
    public function command_sends_newsletter_to_subscribed_users()
    {
        // Create subscribers
        $subscribers = User::factory()->count(3)->create([
            'newsletter_subscribed' => true,
            'newsletter_subscribed_at' => now(),
        ]);

        // Create non-subscriber
        User::factory()->create([
            'newsletter_subscribed' => false,
        ]);

        // Create an upcoming event
        $category = EventCategory::factory()->create();
        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        $this->artisan('newsletter:send', ['--type' => 'weekly'])
            ->assertSuccessful();

        Mail::assertSent(\App\Mail\NewsletterMail::class, 3);
    }

    /** @test */
    public function command_does_not_send_to_non_subscribers()
    {
        User::factory()->count(3)->create([
            'newsletter_subscribed' => false,
        ]);

        $category = EventCategory::factory()->create();
        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        $this->artisan('newsletter:send', ['--type' => 'weekly'])
            ->assertSuccessful();

        Mail::assertNotSent(\App\Mail\NewsletterMail::class);
    }

    /** @test */
    public function command_accepts_weekly_type()
    {
        $subscriber = User::factory()->create([
            'newsletter_subscribed' => true,
        ]);

        $category = EventCategory::factory()->create();
        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        $this->artisan('newsletter:send', ['--type' => 'weekly'])
            ->assertSuccessful();

        Mail::assertSent(\App\Mail\NewsletterMail::class, function ($mail) {
            return $mail->type === 'weekly';
        });
    }

    /** @test */
    public function command_accepts_monthly_type()
    {
        $subscriber = User::factory()->create([
            'newsletter_subscribed' => true,
        ]);

        $category = EventCategory::factory()->create();
        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        $this->artisan('newsletter:send', ['--type' => 'monthly'])
            ->assertSuccessful();

        Mail::assertSent(\App\Mail\NewsletterMail::class, function ($mail) {
            return $mail->type === 'monthly';
        });
    }

    /** @test */
    public function command_sends_test_newsletter_to_admins_only()
    {
        // Create admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create regular user
        User::factory()->create([
            'newsletter_subscribed' => true,
        ]);

        $category = EventCategory::factory()->create();
        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        $this->artisan('newsletter:send', ['--type' => 'weekly', '--test' => true])
            ->assertSuccessful();

        // Only admins should receive test newsletter
        Mail::assertSent(\App\Mail\NewsletterMail::class, 1);
    }

    /** @test */
    public function command_does_not_send_when_no_upcoming_events()
    {
        $subscriber = User::factory()->create([
            'newsletter_subscribed' => true,
        ]);

        // No events created

        $this->artisan('newsletter:send', ['--type' => 'weekly'])
            ->assertSuccessful();

        Mail::assertNotSent(\App\Mail\NewsletterMail::class);
    }

    /** @test */
    public function command_includes_upcoming_events_in_newsletter()
    {
        $subscriber = User::factory()->create([
            'newsletter_subscribed' => true,
        ]);

        $category = EventCategory::factory()->create();

        $upcomingEvent = Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
            'title' => 'Upcoming Event',
        ]);

        $this->artisan('newsletter:send', ['--type' => 'weekly'])
            ->assertSuccessful();

        Mail::assertSent(\App\Mail\NewsletterMail::class, function ($mail) use ($upcomingEvent) {
            return $mail->upcomingEvents->contains('id', $upcomingEvent->id);
        });
    }

    /** @test */
    public function command_includes_featured_events_in_newsletter()
    {
        $subscriber = User::factory()->create([
            'newsletter_subscribed' => true,
        ]);

        $category = EventCategory::factory()->create();

        $featuredEvent = Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'is_featured' => true,
            'start_date' => now()->addDays(7),
            'title' => 'Featured Event',
        ]);

        $this->artisan('newsletter:send', ['--type' => 'weekly'])
            ->assertSuccessful();

        Mail::assertSent(\App\Mail\NewsletterMail::class, function ($mail) use ($featuredEvent) {
            return $mail->featuredEvents->contains('id', $featuredEvent->id);
        });
    }

    /** @test */
    public function command_includes_personalized_recommendations()
    {
        $category = EventCategory::factory()->create();

        $subscriber = User::factory()->create([
            'newsletter_subscribed' => true,
            'interested_category_ids' => [$category->id],
        ]);

        $recommendedEvent = Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        $this->artisan('newsletter:send', ['--type' => 'weekly'])
            ->assertSuccessful();

        Mail::assertSent(\App\Mail\NewsletterMail::class, function ($mail) use ($recommendedEvent) {
            return $mail->recommendations->contains('id', $recommendedEvent->id);
        });
    }

    /** @test */
    public function command_displays_statistics_after_sending()
    {
        User::factory()->count(5)->create([
            'newsletter_subscribed' => true,
        ]);

        $category = EventCategory::factory()->create();
        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        $this->artisan('newsletter:send', ['--type' => 'weekly'])
            ->expectsOutput('📊 Newsletter sent successfully!')
            ->assertSuccessful();
    }

    /** @test */
    public function command_uses_queue_for_sending()
    {
        $this->markTestSkipped('Queue testing requires additional setup');

        // This test would verify that NewsletterMail implements ShouldQueue
        // and is properly queued instead of sent immediately
    }
}

