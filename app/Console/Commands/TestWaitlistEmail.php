<?php

namespace App\Console\Commands;

use App\Models\EventWaitlist;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestWaitlistEmail extends Command
{
    protected $signature = 'waitlist:test-email {waitlistId}';
    protected $description = 'Test sending waitlist email';

    public function handle()
    {
        $waitlistId = $this->argument('waitlistId');

        $waitlist = EventWaitlist::find($waitlistId);

        if (!$waitlist) {
            $this->error("Waitlist entry #{$waitlistId} not found");
            return 1;
        }

        $this->info("Sending test email to: {$waitlist->email}");
        $this->info("Event: {$waitlist->event->title}");

        try {
            Mail::to($waitlist->email)->send(new \App\Mail\WaitlistTicketAvailable($waitlist));
            $this->info("✓ Email sent successfully!");

            // Check mail log if using log driver
            $mailDriver = config('mail.default');
            $this->info("Mail driver: {$mailDriver}");

            if ($mailDriver === 'log') {
                $this->warn("Using log driver - check storage/logs/laravel.log for email content");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("✗ Failed to send email: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

