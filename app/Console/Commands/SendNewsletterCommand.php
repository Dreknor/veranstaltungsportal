<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use App\Mail\NewsletterMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNewsletterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:send
                            {--type=weekly : Newsletter type (weekly, monthly)}
                            {--test : Send test newsletter to admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send newsletter to subscribed users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $isTest = $this->option('test');

        $this->info("📧 Preparing {$type} newsletter...");

        // Get upcoming events
        $upcomingEvents = Event::published()
            ->where('start_date', '>', now())
            ->where('start_date', '<', now()->addDays(30))
            ->orderBy('start_date')
            ->limit(10)
            ->get();

        if ($upcomingEvents->isEmpty()) {
            $this->warn('No upcoming events found. Newsletter not sent.');
            return 0;
        }

        // Get featured events
        $featuredEvents = Event::published()
            ->where('is_featured', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Get subscribers
        if ($isTest) {
            $subscribers = User::where('is_admin', true)->get();
            $this->info('Sending test newsletter to admins...');
        } else {
            $subscribers = User::where('newsletter_subscribed', true)->get();
            $this->info("Found {$subscribers->count()} subscribers");
        }

        $sent = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
            try {
                // Get personalized recommendations
                $recommendations = $subscriber->getRecommendedEvents(5);

                Mail::to($subscriber->email)->send(new NewsletterMail(
                    $subscriber,
                    $upcomingEvents,
                    $featuredEvents,
                    $recommendations,
                    $type
                ));

                $sent++;
                $this->line("✓ Sent to: {$subscriber->email}");
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to send to {$subscriber->email}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("📊 Newsletter sent successfully!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Sent', $sent],
                ['Failed', $failed],
                ['Total', $sent + $failed],
            ]
        );

        return 0;
    }
}

