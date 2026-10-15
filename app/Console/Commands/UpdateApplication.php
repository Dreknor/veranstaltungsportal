<?php

namespace App\Console\Commands;

use App\Services\UpdateService;
use Illuminate\Console\Command;
use Throwable;

/**
 * Aktualisiert die Anwendung aus dem Git-Repository.
 *
 *   php artisan app:update               Update ausführen (falls neue Version vorhanden)
 *   php artisan app:update --check       Nur prüfen und ausstehende Änderungen anzeigen
 *   php artisan app:update --force       Alle Schritte auch ohne neue Version ausführen
 *
 * Exit-Code 1, wenn das Update fehlschlägt.
 */
class UpdateApplication extends Command
{
    protected $signature = 'app:update
                            {--check : Nur prüfen, ob eine neue Version verfügbar ist}
                            {--force : Alle Schritte ausführen, auch ohne neue Version}
                            {--allow-dirty : Update trotz lokaler Änderungen ausführen}';

    protected $description = 'Aktualisiert die Anwendung aus dem Git-Repository';

    public function handle(UpdateService $updater): int
    {
        if ($this->option('check')) {
            return $this->check($updater);
        }

        $success = $updater->update(
            fn (string $text) => $this->output->write($text),
            (bool) $this->option('force'),
            (bool) $this->option('allow-dirty'),
        );

        return $success ? self::SUCCESS : self::FAILURE;
    }

    private function check(UpdateService $updater): int
    {
        try {
            $status = $updater->status();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->line('Installiert: '.substr($status['current'], 0, 7).' ('.$status['branch'].')');

        if ($status['dirty']) {
            $this->warn('Lokale Änderungen: '.implode(', ', $status['dirty']));
        }

        if ($status['behind'] === 0) {
            $this->info('Die Anwendung ist auf dem neuesten Stand.');

            return self::SUCCESS;
        }

        $this->info($status['behind'].' neue Änderung(en) verfügbar:');
        $this->table(
            ['Commit', 'Beschreibung', 'Autor', 'Datum'],
            array_map(fn (array $c) => [$c['short'], $c['subject'], $c['author'], $c['date']], $status['commits'])
        );

        return self::SUCCESS;
    }
}
