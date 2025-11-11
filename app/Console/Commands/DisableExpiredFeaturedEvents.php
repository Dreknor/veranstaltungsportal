<?php

namespace App\Console\Commands;

use App\Services\FeaturedEventService;
use Illuminate\Console\Command;

class DisableExpiredFeaturedEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'featured:disable-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deaktiviert abgelaufene Featured Events automatisch';

    /**
     * Execute the console command.
     */
    public function handle(FeaturedEventService $featuredEventService): int
    {
        $this->info('Suche nach abgelaufenen Featured Events...');

        $count = $featuredEventService->disableExpiredFeaturedEvents();

        if ($count > 0) {
            $this->info("✓ {$count} abgelaufene Featured Event(s) wurden deaktiviert.");
        } else {
            $this->info('Keine abgelaufenen Featured Events gefunden.');
        }

        return Command::SUCCESS;
    }
}

