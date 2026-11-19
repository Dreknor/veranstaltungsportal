<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Throwable;

/**
 * Online-Updater: aktualisiert die Anwendung aus dem Git-Repository.
 *
 * Ablauf: git fetch → Wartungsmodus → git merge --ff-only → composer install →
 * migrate → (npm ci) → npm run build → Caches leeren → Queue-Worker neu starten.
 *
 * Fortschritt wird nach storage/logs/updater.log geschrieben, der Zustand
 * (queued/running/success/failed) nach storage/app/updater/state.json, damit die
 * Admin-Oberfläche einen im Hintergrund laufenden Update-Prozess verfolgen kann.
 */
class UpdateService
{
    public const STATUS_IDLE = 'idle';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_RUNNING = 'running';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    /** Nach dieser Zeit gilt ein nicht beendetes Update als abgebrochen (Sekunden). */
    private const STALE_AFTER = 3600;

    /** Änderungen an diesen Pfaden erfordern einen neuen Asset-Build. */
    private const ASSET_PATHS = ['resources/', 'vite.config.js', 'package.json', 'package-lock.json', 'tailwind.config.js', 'postcss.config.js'];

    /** @var resource|null */
    private $logHandle = null;

    /** @var callable|null */
    private $output = null;

    /**
     * Ermittelt installierte und verfügbare Version.
     *
     * @return array{branch: string, target_ref: string, current: string, current_date: string, target: string, behind: int, ahead: int, commits: list<array{hash: string, short: string, subject: string, author: string, date: string}>, dirty: list<string>}
     */
    public function status(bool $fetch = true): array
    {
        $remoteRef = $this->remoteRef();

        if ($fetch) {
            $this->git(['fetch', '--quiet', config('updater.remote'), $this->targetBranch()]);
        }

        $log = $this->git(['log', '--format=%H%x1f%h%x1f%s%x1f%an%x1f%cI', 'HEAD..'.$remoteRef]);

        $commits = array_map(function (string $line) {
            [$hash, $short, $subject, $author, $date] = array_pad(explode("\x1f", $line), 5, '');

            return compact('hash', 'short', 'subject', 'author', 'date');
        }, $this->lines($log));

        return [
            'branch' => trim($this->git(['rev-parse', '--abbrev-ref', 'HEAD'])),
            'target_ref' => $remoteRef,
            'current' => trim($this->git(['rev-parse', 'HEAD'])),
            'current_date' => trim($this->git(['log', '-1', '--format=%cI', 'HEAD'])),
            'target' => trim($this->git(['rev-parse', $remoteRef])),
            'behind' => count($commits),
            'ahead' => (int) trim($this->git(['rev-list', '--count', $remoteRef.'..HEAD'])),
            'commits' => $commits,
            'dirty' => $this->dirtyFiles(),
        ];
    }

    /**
     * Führt das Update synchron aus.
     *
     * @param  callable(string): void|null  $output  erhält die gesamte Ausgabe (z. B. für die Konsole)
     */
    public function update(?callable $output = null, bool $force = false, bool $allowDirty = false): bool
    {
        File::ensureDirectoryExists(dirname($this->statePath()));

        $lock = fopen(dirname($this->statePath()).'/update.lock', 'c');
        if (! $lock || ! flock($lock, LOCK_EX | LOCK_NB)) {
            $output && $output("Es läuft bereits ein Update.\n");

            return false;
        }

        $this->output = $output;
        $this->logHandle = fopen($this->logPath(), 'w') ?: null;
        $this->writeState(['status' => self::STATUS_RUNNING, 'started_at' => now()->toIso8601String()]);

        $from = null;
        $maintenance = false;
        $success = false;

        try {
            $this->line('Update gestartet: '.now()->format('d.m.Y H:i:s'));

            $dirty = $this->dirtyFiles();
            if ($dirty && ! $allowDirty) {
                throw new RuntimeException('Lokale Änderungen im Arbeitsverzeichnis verhindern das Update: '.implode(', ', $dirty));
            }

            $this->step('Neue Version abrufen', [$this->gitBinary(), 'fetch', config('updater.remote'), $this->targetBranch()]);

            $from = trim($this->git(['rev-parse', 'HEAD']));
            $to = trim($this->git(['rev-parse', $this->remoteRef()]));

            if ($from === $to && ! $force) {
                $this->line('Bereits auf dem neuesten Stand ('.substr($from, 0, 7).').');
                $success = true;

                return true;
            }

            if (config('updater.maintenance_mode')) {
                $this->step('Wartungsmodus aktivieren', $this->artisan('down', '--retry=60'));
                $maintenance = true;
            }

            $this->step('Code aktualisieren ('.substr($from, 0, 7).' → '.substr($to, 0, 7).')', [$this->gitBinary(), 'merge', '--ff-only', $this->remoteRef()]);

            $changed = $from === $to ? [] : $this->lines($this->git(['diff', '--name-only', $from, $to]));

            $this->step('Composer-Abhängigkeiten installieren', $this->composerInstallCommand(), $this->composerEnvironment());
            $this->step('Datenbank migrieren', $this->artisan('migrate', '--force'));

            if (config('updater.build_assets') && ($force || $this->touchesAssets($changed))) {
                if ($force || ! is_dir(base_path('node_modules')) || in_array('package-lock.json', $changed, true)) {
                    $this->step('npm-Pakete installieren', [config('updater.npm_binary'), 'ci']);
                }
                $this->step('Frontend-Assets bauen', [config('updater.npm_binary'), 'run', 'build']);
            }

            $this->step('Caches leeren', $this->artisan('optimize:clear'));
            if (app()->isProduction()) {
                $this->step('Caches aufbauen', $this->artisan('optimize'));
            }
            $this->step('Queue-Worker neu starten', $this->artisan('queue:restart'));

            $this->line(PHP_EOL.'Update erfolgreich abgeschlossen. Version: '.substr($to, 0, 7));
            Log::info('Anwendung aktualisiert', ['from' => $from, 'to' => $to]);
            $success = true;

            return true;
        } catch (Throwable $e) {
            $this->line(PHP_EOL.'FEHLER: '.$e->getMessage());

            if ($from && $from !== $this->safeCurrentCommit()) {
                $this->line("Vorherige Version war {$from}. Zurücksetzen mit: git reset --hard {$from}");
            }

            Log::error('Update fehlgeschlagen', ['from' => $from, 'error' => $e->getMessage()]);

            return false;
        } finally {
            if ($maintenance) {
                try {
                    $this->step('Wartungsmodus beenden', $this->artisan('up'));
                } catch (Throwable $e) {
                    $this->line('Wartungsmodus konnte nicht beendet werden: '.$e->getMessage());
                }
            }

            $this->writeState([
                'status' => $success ? self::STATUS_SUCCESS : self::STATUS_FAILED,
                'started_at' => $this->state()['started_at'] ?? null,
                'finished_at' => now()->toIso8601String(),
            ]);

            fclose($this->logHandle);
            $this->logHandle = null;
            $this->output = null;

            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /**
     * Startet `php artisan app:update` als vom Web-Request losgelösten Prozess.
     */
    public function startInBackground(): void
    {
        File::ensureDirectoryExists(dirname($this->statePath()));
        File::put($this->logPath(), 'Update wird gestartet …'.PHP_EOL);
        $this->writeState(['status' => self::STATUS_QUEUED, 'started_at' => now()->toIso8601String()]);

        $command = escapeshellarg($this->phpBinary()).' '.escapeshellarg(base_path('artisan')).' app:update';

        Process::path(base_path())->run(PHP_OS_FAMILY === 'Windows'
            ? 'start "" /B '.$command.' > NUL 2>&1'
            : ['sh', '-c', 'nohup '.$command.' > /dev/null 2>&1 &']);
    }

    /**
     * @return array{status: string, started_at?: string|null, finished_at?: string|null}
     */
    public function state(): array
    {
        $state = File::exists($this->statePath())
            ? (json_decode(File::get($this->statePath()), true) ?: [])
            : [];

        $state['status'] ??= self::STATUS_IDLE;

        // Abgestürzte Prozesse sollen die Oberfläche nicht dauerhaft blockieren
        if ($this->isActiveStatus($state['status'])
            && isset($state['started_at'])
            && now()->diffInSeconds($state['started_at'], true) > self::STALE_AFTER) {
            $state['status'] = self::STATUS_FAILED;
        }

        return $state;
    }

    public function isRunning(): bool
    {
        return $this->isActiveStatus($this->state()['status']);
    }

    public function log(): string
    {
        return File::exists($this->logPath()) ? File::get($this->logPath()) : '';
    }

    public function statePath(): string
    {
        return storage_path('app/updater/state.json');
    }

    public function logPath(): string
    {
        return storage_path('logs/updater.log');
    }

    /**
     * @return list<string>
     */
    public function dirtyFiles(): array
    {
        $output = $this->git(['status', '--porcelain', '--untracked-files=no']);

        // Format: "XY pfad" – die ersten drei Zeichen sind Status und Leerzeichen
        return $this->lines(implode("\n", array_map(
            fn (string $line) => substr($line, 3),
            explode("\n", $output)
        )));
    }

    /**
     * @return list<string>
     */
    private function lines(string $text): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", $text))));
    }

    private function isActiveStatus(string $status): bool
    {
        return in_array($status, [self::STATUS_QUEUED, self::STATUS_RUNNING], true);
    }

    /**
     * @param  list<string>  $command
     * @param  array<string, string>  $environment
     */
    private function step(string $label, array $command, array $environment = []): void
    {
        $this->line(PHP_EOL.'==> '.$label);

        $result = Process::path(base_path())
            ->timeout(config('updater.timeout'))
            ->env($environment)
            ->run($command, fn (string $type, string $buffer) => $this->write($buffer));

        if ($result->failed()) {
            throw new RuntimeException("Schritt \"{$label}\" fehlgeschlagen (Exit-Code {$result->exitCode()}).");
        }
    }

    /**
     * @param  list<string>  $args
     */
    private function git(array $args): string
    {
        $result = Process::path(base_path())->timeout(120)->run([$this->gitBinary(), ...$args]);

        if ($result->failed()) {
            throw new RuntimeException('git '.implode(' ', $args).': '.trim($result->errorOutput() ?: $result->output()));
        }

        return $result->output();
    }

    private function safeCurrentCommit(): ?string
    {
        try {
            return trim($this->git(['rev-parse', 'HEAD']));
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param  list<string>  $changed
     */
    private function touchesAssets(array $changed): bool
    {
        foreach ($changed as $file) {
            if (Str::startsWith($file, self::ASSET_PATHS)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function artisan(string ...$args): array
    {
        return array_values([$this->phpBinary(), base_path('artisan'), ...$args, '--no-interaction']);
    }

    /**
     * @return list<string>
     */
    private function composerInstallCommand(): array
    {
        $composer = config('updater.composer_binary');
        $command = Str::endsWith($composer, '.phar') ? [$this->phpBinary(), $composer] : [$composer];

        $command = [...$command, 'install', '--no-interaction', '--prefer-dist', '--optimize-autoloader'];

        if (app()->isProduction()) {
            $command[] = '--no-dev';
        }

        return $command;
    }

    /**
     * Composer benötigt ein beschreibbares Home-Verzeichnis – unter dem
     * Webserver-Benutzer ist HOME oft nicht gesetzt.
     *
     * @return array<string, string>
     */
    private function composerEnvironment(): array
    {
        if (getenv('COMPOSER_HOME') || getenv('HOME') || getenv('APPDATA')) {
            return [];
        }

        return ['COMPOSER_HOME' => storage_path('app/updater/composer')];
    }

    private function remoteRef(): string
    {
        return config('updater.remote').'/'.$this->targetBranch();
    }

    /**
     * Branch, aus dem aktualisiert wird: UPDATER_BRANCH, sonst der aktuell ausgecheckte Branch
     * (bei losgelöstem HEAD "master"). So aktualisiert sich eine Installation auf einem
     * Feature-Branch aus genau diesem Branch.
     */
    public function targetBranch(): string
    {
        if (filled(config('updater.branch'))) {
            return (string) config('updater.branch');
        }

        $branch = trim($this->git(['rev-parse', '--abbrev-ref', 'HEAD']));

        return $branch !== '' && $branch !== 'HEAD' ? $branch : 'master';
    }

    private function gitBinary(): string
    {
        return config('updater.git_binary') ?: 'git';
    }

    private function phpBinary(): string
    {
        return config('updater.php_binary') ?: ((new PhpExecutableFinder)->find(false) ?: 'php');
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function writeState(array $state): void
    {
        File::put($this->statePath(), (string) json_encode($state, JSON_PRETTY_PRINT));
    }

    private function line(string $text): void
    {
        $this->write($text.PHP_EOL);
    }

    private function write(string $text): void
    {
        if ($this->logHandle) {
            fwrite($this->logHandle, $text);
            fflush($this->logHandle);
        }

        if ($this->output) {
            ($this->output)($text);
        }
    }
}
