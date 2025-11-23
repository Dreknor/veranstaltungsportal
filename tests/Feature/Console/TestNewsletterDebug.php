<?php

namespace Tests\Feature\Console;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TestNewsletterDebug extends TestCase
{
    use RefreshDatabase;

    public function test_debug_newsletter_command()
    {
        Mail::fake();

        // Create subscriber
        $user = User::factory()->create([
            'newsletter_subscribed' => true,
            'newsletter_subscribed_at' => now(),
        ]);

        echo "\n\nUser created: " . $user->id . " - " . $user->email . "\n";
        echo "Newsletter subscribed: " . ($user->newsletter_subscribed ? 'YES' : 'NO') . "\n\n";

        // Create event
        $category = EventCategory::factory()->create();
        $event = Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
        ]);

        echo "Event created: " . $event->id . " - " . $event->title . "\n";
        echo "Published: " . ($event->is_published ? 'YES' : 'NO') . "\n";
        echo "Start date: " . $event->start_date . "\n\n";

        // Check if events are found by query
        $upcomingEvents = Event::published()
            ->where('start_date', '>', now())
            ->where('start_date', '<', now()->addDays(30))
            ->get();

        echo "Upcoming events found: " . $upcomingEvents->count() . "\n\n";

        // Check subscribers
        $subscribers = User::where('newsletter_subscribed', true)->get();
        echo "Subscribers found: " . $subscribers->count() . "\n\n";

        // Run command
        $this->artisan('newsletter:send', ['--type' => 'weekly'])
            ->assertSuccessful();

        echo "\n\nMails sent: " . Mail::sent(\App\Mail\NewsletterMail::class)->count() . "\n\n";

        $this->assertTrue(true);
    }
}

