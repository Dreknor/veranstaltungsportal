<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Console\Command;

class CheckAndAwardBadges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'badges:check-and-award {--user= : Specific user ID to check} {--all : Check all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and award badges to users based on their achievements';

    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        parent::__construct();
        $this->badgeService = $badgeService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->option('user');
        $all = $this->option('all');

        if ($userId) {
            return $this->checkUserBadges($userId);
        }

        if ($all) {
            return $this->checkAllUsersBadges();
        }

        $this->error('Please specify either --user=ID or --all option');
        return self::FAILURE;
    }

    /**
     * Check badges for a specific user
     */
    protected function checkUserBadges(int $userId): int
    {
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return self::FAILURE;
        }

        $this->info("Checking badges for user: {$user->fullName()} ({$user->email})");

        $newBadges = $this->badgeService->checkAndAwardBadges($user);

        if (empty($newBadges)) {
            $this->info('No new badges earned');
        } else {
            $this->info('Awarded ' . count($newBadges) . ' new badges:');
            foreach ($newBadges as $badge) {
                $this->line("  - {$badge->name} (+{$badge->points} points)");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Check badges for all users
     */
    protected function checkAllUsersBadges(): int
    {
        $users = User::all();
        $totalBadgesAwarded = 0;

        $this->info("Checking badges for {$users->count()} users...");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $newBadges = $this->badgeService->checkAndAwardBadges($user);
            $totalBadgesAwarded += count($newBadges);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Total badges awarded: {$totalBadgesAwarded}");

        return self::SUCCESS;
    }
}

