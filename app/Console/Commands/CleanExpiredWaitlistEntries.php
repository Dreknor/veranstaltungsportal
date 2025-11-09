<?php

namespace App\Console\Commands;

use App\Models\EventWaitlist;
use Illuminate\Console\Command;

class CleanExpiredWaitlistEntries extends Command
{
    protected $signature = 'waitlist:clean-expired';
    protected $description = 'Mark expired waitlist entries and notify next people';

    public function handle()
    {
        $expiredEntries = EventWaitlist::where('status', 'notified')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredEntries->isEmpty()) {
            $this->info('No expired waitlist entries found.');
            return 0;
        }

        $count = 0;
        foreach ($expiredEntries as $entry) {
            $entry->markAsExpired();
            $count++;

            $this->info("Marked entry #{$entry->id} as expired (Event: {$entry->event->title})");
        }

        $this->info("Total expired entries marked: {$count}");

        // Optionally notify next people on waitlist for each affected event
        $affectedEvents = $expiredEntries->pluck('event')->unique();

        foreach ($affectedEvents as $event) {
            if ($event->hasAvailableTickets()) {
                $this->info("Event '{$event->title}' has available tickets - you may want to notify waitlist.");
            }
        }

        return 0;
    }
}

