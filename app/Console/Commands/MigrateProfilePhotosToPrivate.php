<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateProfilePhotosToPrivate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:migrate-to-private';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all user profile photos from public to private storage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting migration of profile photos from public to private storage...');

        $users = User::whereNotNull('profile_photo')->get();
        $total = $users->count();

        if ($total === 0) {
            $this->info('No profile photos to migrate.');
            return Command::SUCCESS;
        }

        $this->info("Found {$total} users with profile photos.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($users as $user) {
            try {
                $oldPath = $user->profile_photo;

                // Prüfe ob die Datei im public storage existiert
                if (Storage::disk('public')->exists($oldPath)) {
                    // Kopiere die Datei zu private storage
                    $content = Storage::disk('public')->get($oldPath);
                    Storage::disk('local')->put($oldPath, $content);

                    // Lösche die Datei aus public storage
                    Storage::disk('public')->delete($oldPath);

                    $migrated++;
                } elseif (Storage::disk('local')->exists($oldPath)) {
                    // Bereits im private storage
                    $skipped++;
                } else {
                    // Datei existiert nicht - bereinige den Datenbank-Eintrag
                    $user->profile_photo = null;
                    $user->save();
                    $this->warn("\nFile not found for user {$user->id}: {$oldPath}");
                    $errors++;
                }
            } catch (\Exception $e) {
                $this->error("\nError migrating photo for user {$user->id}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Migration completed!");
        $this->info("Migrated: {$migrated}");
        $this->info("Skipped (already private): {$skipped}");
        $this->info("Errors: {$errors}");

        return Command::SUCCESS;
    }
}
