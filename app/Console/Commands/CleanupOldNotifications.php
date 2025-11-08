<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup {--days=30 : Delete notifications older than X days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old read notifications to keep database clean';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');

        $this->info("Deleting read notifications older than {$days} days...");

        $cutoffDate = now()->subDays($days);

        $deletedCount = DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('read_at', '<', $cutoffDate)
            ->delete();

        $this->info("Deleted {$deletedCount} old notifications");

        return Command::SUCCESS;
    }
}

