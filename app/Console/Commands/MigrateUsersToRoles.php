<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MigrateUsersToRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:migrate-to-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing users from is_admin/is_organizer flags to role-based permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration of users to role-based permissions...');

        $users = User::all();
        $migratedCount = 0;

        foreach ($users as $user) {
            $rolesAssigned = [];

            // Check if user has is_admin flag
            if (isset($user->is_admin) && $user->is_admin) {
                $user->assignRole('admin');
                $rolesAssigned[] = 'admin';
                $this->line("  ✓ Assigned 'admin' role to {$user->email}");
            }

            // Check if user has is_organizer flag
            if ($user->is_organizer) {
                if (!$user->hasRole('organizer')) {
                    $user->assignRole('organizer');
                    $rolesAssigned[] = 'organizer';
                    $this->line("  ✓ Assigned 'organizer' role to {$user->email}");
                }
            }

            // If user has no roles yet, assign 'user' role
            if ($user->roles()->count() === 0) {
                $user->assignRole('user');
                $rolesAssigned[] = 'user';
                $this->line("  ✓ Assigned 'user' role to {$user->email}");
            }

            if (!empty($rolesAssigned)) {
                $migratedCount++;
            }
        }

        $this->info("\nMigration completed!");
        $this->info("Total users processed: {$users->count()}");
        $this->info("Users with roles assigned: {$migratedCount}");

        // Ask if we should remove is_admin column
        if ($this->confirm('Do you want to remove the is_admin column from users table?', false)) {
            $this->info('Creating migration to remove is_admin column...');
            $this->call('make:migration', ['name' => 'remove_is_admin_from_users_table']);
            $this->info('Please edit the generated migration file and run php artisan migrate');
        }

        return Command::SUCCESS;
    }
}
