<?php

namespace App\Console\Commands;

use App\Services\WaitlistService;
use Illuminate\Console\Command;

class CleanExpiredWaitlistEntries extends Command
{
    protected $signature = 'waitlist:clean-expired';
    protected $description = 'Beendet abgelaufene Wartelisten-Reservierungen und lässt die Nächsten nachrücken';

    public function handle(WaitlistService $waitlist): int
    {
        $count = $waitlist->expireReservations();
        $this->info("{$count} abgelaufene Reservierung(en) beendet; freie Plätze wurden weitergegeben.");

        return self::SUCCESS;
    }
}
