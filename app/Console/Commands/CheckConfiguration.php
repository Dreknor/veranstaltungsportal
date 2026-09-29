<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

/**
 * Prüft, ob die Anwendung vollständig und plausibel konfiguriert ist.
 *
 * Aufruf nach jedem Deployment bzw. bei Problemen:
 *   php artisan app:check-config              (Regeln abhängig von APP_ENV)
 *   php artisan app:check-config --production (Produktionsregeln erzwingen)
 *   php artisan app:check-config --live       (zusätzlich SMTP-Verbindung testen)
 *   php artisan app:check-config --only=mail,paypal
 *
 * Exit-Code 1 bei Fehlern (mit --strict auch bei Warnungen) – geeignet für Deploy-Skripte.
 */
class CheckConfiguration extends Command
{
    protected $signature = 'app:check-config
                            {--production : Produktionsregeln anwenden, unabhängig von APP_ENV}
                            {--live : Verbindungen zu externen Diensten testen (SMTP)}
                            {--strict : Warnungen wie Fehler behandeln (Exit-Code 1)}
                            {--only= : Nur bestimmte Bereiche prüfen, kommagetrennt}';

    protected $description = 'Prüft, ob alle Konfigurationen korrekt gesetzt sind';

    /**
     * Bereiche in Ausgabereihenfolge (Schlüssel für --only).
     */
    public const SECTIONS = [
        'app' => 'Anwendung',
        'database' => 'Datenbank',
        'queue' => 'Queue & Scheduler',
        'cache' => 'Cache & Sitzungen',
        'mail' => 'E-Mail-Versand',
        'files' => 'Dateien & Frontend-Assets',
        'security' => 'reCAPTCHA & Registrierung',
        'sso' => 'Single Sign-on',
        'paypal' => 'PayPal (Organisationen)',
        'billing' => 'Plattform-Rechnungsdaten',
        'privacy' => 'Datenschutz-Fristen',
        'organizations' => 'Organisationen',
        'events' => 'Veranstaltungen',
    ];

    /** @var array<int, array{section: string, level: string, message: string, hint: ?string}> */
    protected array $results = [];

    protected bool $production = false;

    protected bool $databaseAvailable = false;

    protected string $section = 'app';

    public function handle(): int
    {
        $this->production = (bool) $this->option('production') || app()->isProduction();

        $only = collect(explode(',', (string) $this->option('only')))->map(fn ($s) => trim($s))->filter();
        $unknown = $only->diff(array_keys(self::SECTIONS));
        if ($unknown->isNotEmpty()) {
            $this->error('Unbekannte Bereiche: ' . $unknown->implode(', ') . '. Verfügbar: ' . implode(', ', array_keys(self::SECTIONS)));

            return self::INVALID;
        }

        $this->newLine();
        $this->line(sprintf(
            '<options=bold>Konfigurationsprüfung</> · Umgebung: <comment>%s</comment>%s',
            app()->environment(),
            $this->production ? ' · <fg=yellow>Produktionsregeln aktiv</>' : ''
        ));

        // Datenbank immer ermitteln – viele Bereiche hängen davon ab
        $this->databaseAvailable = $this->databaseReachable();

        foreach (array_keys(self::SECTIONS) as $key) {
            if ($only->isNotEmpty() && !$only->contains($key)) {
                continue;
            }
            $this->section = $key;
            $this->{'check' . ucfirst($key)}();
        }

        return $this->report();
    }

    // ─── Bereiche ───────────────────────────────────────────────────────────

    protected function checkApp(): void
    {
        $key = (string) config('app.key');
        if ($key === '' || str_contains($key, 'PLEASE_RUN')) {
            $this->markError('APP_KEY ist nicht gesetzt.', 'php artisan key:generate ausführen (nur bei Neuinstallation – ein neuer Schlüssel macht verschlüsselte Daten wie PayPal-Zugangsdaten unlesbar).');
        } else {
            $raw = str_starts_with($key, 'base64:') ? base64_decode(substr($key, 7), true) : $key;
            $expected = strtolower((string) config('app.cipher')) === 'aes-128-cbc' ? 16 : 32;
            $raw !== false && strlen($raw) === $expected
                ? $this->markOk('APP_KEY ist gesetzt und passt zum Verschlüsselungsverfahren.')
                : $this->markError('APP_KEY hat ein ungültiges Format.', 'Erwartet wird ein Schlüssel wie von "php artisan key:generate" erzeugt.');
        }

        $env = app()->environment();
        in_array($env, ['local', 'testing', 'staging', 'production'], true)
            ? $this->markOk("APP_ENV = {$env}")
            : $this->markWarning("APP_ENV = {$env} ist ungewöhnlich.", 'Üblich sind local, staging oder production.');

        if ($this->production && config('app.debug')) {
            $this->markError('APP_DEBUG ist in Produktion aktiv – Fehlerseiten zeigen Interna und Zugangsdaten.', 'APP_DEBUG=false setzen.');
        } elseif ($this->production) {
            $this->markOk('APP_DEBUG ist deaktiviert.');
        }

        $url = (string) config('app.url');
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->markError("APP_URL ist keine gültige URL: \"{$url}\".", 'Links in E-Mails (Buchung, Tickets, Warteliste) funktionieren sonst nicht.');
        } else {
            $host = (string) parse_url($url, PHP_URL_HOST);
            $isLocalHost = in_array($host, ['localhost', '127.0.0.1', '::1'], true) || preg_match('/\.(local|test|localhost)$/', $host);
            if ($this->production && $isLocalHost) {
                $this->markError("APP_URL zeigt auf eine lokale Adresse ({$url}).", 'Links in E-Mails wären für Teilnehmende nicht erreichbar.');
            } elseif ($this->production && parse_url($url, PHP_URL_SCHEME) !== 'https') {
                $this->markWarning("APP_URL verwendet kein HTTPS ({$url}).", 'Für Produktion https:// verwenden (auch für PayPal-Webhooks und SSO).');
            } else {
                $this->markOk("APP_URL = {$url}");
            }
        }

        config('app.locale') === 'de'
            ? $this->markOk('Sprache: de')
            : $this->markWarning('APP_LOCALE ist "' . config('app.locale') . '" – Monats- und Wochentagsnamen erscheinen dann nicht auf Deutsch.', 'APP_LOCALE=de setzen.');

        config('app.timezone') === 'Europe/Berlin'
            ? $this->markOk('Zeitzone: Europe/Berlin')
            : $this->markWarning('Zeitzone ist ' . config('app.timezone') . ' – Termine und Erinnerungen würden verschoben.', "In config/app.php 'timezone' => 'Europe/Berlin' setzen.");

        if ($this->production && !app()->configurationIsCached()) {
            $this->markWarning('Die Konfiguration ist nicht gecacht.', 'Nach jedem Deployment "php artisan optimize" ausführen.');
        }
    }

    protected function checkDatabase(): void
    {
        if (!$this->databaseAvailable) {
            $this->markError('Keine Verbindung zur Datenbank (' . config('database.default') . ').', 'DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME und DB_PASSWORD prüfen.');

            return;
        }

        $driver = DB::connection()->getDriverName();
        $this->markOk("Verbindung zur Datenbank ({$driver}: " . DB::connection()->getDatabaseName() . ') hergestellt.');

        if ($this->production && $driver === 'sqlite') {
            $this->markWarning('In Produktion wird SQLite verwendet.', 'Für mehrere gleichzeitige Nutzer MySQL/MariaDB verwenden.');
        }

        $migrator = app('migrator');
        if (!$migrator->repositoryExists()) {
            $this->markError('Die Migrationstabelle fehlt – die Datenbank ist nicht eingerichtet.', 'php artisan migrate --force ausführen.');

            return;
        }

        $files = $migrator->getMigrationFiles(array_merge([database_path('migrations')], $migrator->paths()));
        $pending = array_diff(array_keys($files), $migrator->getRepository()->getRan());
        empty($pending)
            ? $this->markOk('Alle Migrationen sind ausgeführt.')
            : $this->markError(count($pending) . ' Migration(en) stehen aus: ' . implode(', ', array_slice($pending, 0, 3)) . (count($pending) > 3 ? ' …' : ''), 'php artisan migrate --force ausführen.');
    }

    protected function checkQueue(): void
    {
        $connection = (string) config('queue.default');
        $driver = (string) config("queue.connections.{$connection}.driver", $connection);

        if ($driver === 'sync') {
            $this->production
                ? $this->markWarning('Queue läuft synchron (QUEUE_CONNECTION=sync).', 'Erinnerungen und Massenversand verlangsamen dann Seitenaufrufe. Empfohlen: database.')
                : $this->markOk('Queue: sync (Jobs werden sofort ausgeführt).');
        } else {
            $this->markOk("Queue-Verbindung: {$connection}");
        }

        $mode = (string) config('queue.worker_mode');
        in_array($mode, ['cronjob', 'supervisor'], true)
            ? $this->markOk('Worker-Modus: ' . $mode . ($mode === 'cronjob' ? ' (Jobs werden jede Minute vom Scheduler abgearbeitet)' : ' (dauerhafter Worker, z. B. Supervisor)'))
            : $this->markError("QUEUE_WORKER_MODE = \"{$mode}\" ist ungültig.", 'Erlaubt: cronjob oder supervisor.');

        if ($this->databaseAvailable && $driver === 'database') {
            $table = (string) config("queue.connections.{$connection}.table", 'jobs');
            if (!Schema::hasTable($table)) {
                $this->markError("Die Queue-Tabelle \"{$table}\" fehlt.", 'php artisan migrate --force ausführen.');
            } else {
                $stuck = DB::table($table)->where('available_at', '<', now()->subMinutes(15)->getTimestamp())->count();
                $stuck > 0
                    ? $this->markWarning("{$stuck} Job(s) warten seit über 15 Minuten – die Queue wird offenbar nicht abgearbeitet.", $mode === 'supervisor'
                        ? 'Läuft der Worker? z. B. "supervisorctl status" prüfen.'
                        : 'Läuft der Cronjob für "php artisan schedule:run"?')
                    : $this->markOk('Keine hängenden Jobs in der Queue.');
            }
        }

        if ($this->databaseAvailable) {
            $failedTable = (string) config('queue.failed.table', 'failed_jobs');
            if (Schema::hasTable($failedTable)) {
                $failed = DB::table($failedTable)->count();
                $failed > 0
                    ? $this->markWarning("{$failed} fehlgeschlagene Job(s) – betroffene E-Mails wurden nicht versendet.", 'Ursache mit "php artisan queue:failed" prüfen, danach "php artisan queue:retry all".')
                    : $this->markOk('Keine fehlgeschlagenen Jobs.');
            }
        }

        // Lebenszeichen des Schedulers (siehe routes/console.php)
        $lastRun = Cache::get('scheduler:last_run');
        $cronHint = 'Cronjob einrichten: * * * * * cd ' . base_path() . ' && php artisan schedule:run >> /dev/null 2>&1';
        if (!$lastRun) {
            $message = 'Der Scheduler ist noch nie gelaufen – Erinnerungen, Nachbereitung, Warteliste und Anonymisierung werden nicht ausgeführt.';
            $this->production ? $this->markError($message, $cronHint) : $this->markWarning($message, $cronHint);
        } elseif (\Illuminate\Support\Carbon::parse($lastRun)->lt(now()->subMinutes(5))) {
            $message = 'Der Scheduler lief zuletzt ' . \Illuminate\Support\Carbon::parse($lastRun)->diffForHumans() . '.';
            $this->production ? $this->markError($message, $cronHint) : $this->markWarning($message, $cronHint);
        } else {
            $this->markOk('Scheduler läuft (zuletzt ' . \Illuminate\Support\Carbon::parse($lastRun)->diffForHumans() . ').');
        }
    }

    protected function checkCache(): void
    {
        $store = (string) config('cache.default');
        $cacheDriver = (string) config("cache.stores.{$store}.driver");

        if ($this->production && $cacheDriver === 'array') {
            $this->markWarning('Cache-Store "array" speichert nichts dauerhaft.', 'CACHE_STORE=database oder redis verwenden.');
        }

        if ($cacheDriver === 'database' && $this->databaseAvailable && !Schema::hasTable((string) config("cache.stores.{$store}.table", 'cache'))) {
            $this->markError('Die Cache-Tabelle fehlt.', 'php artisan migrate --force ausführen.');
        } else {
            try {
                Cache::put('config-check:probe', 'ok', 30);
                Cache::get('config-check:probe') === 'ok'
                    ? $this->markOk("Cache ({$store}) ist beschreibbar.")
                    : $this->markError("Cache ({$store}) speichert keine Werte.");
                Cache::forget('config-check:probe');
            } catch (\Throwable $e) {
                $this->markError("Cache ({$store}) ist nicht nutzbar: " . $e->getMessage());
            }
        }

        $sessionDriver = (string) config('session.driver');
        if ($sessionDriver === 'database' && $this->databaseAvailable && !Schema::hasTable((string) config('session.table', 'sessions'))) {
            $this->markError('Die Sitzungstabelle fehlt – Anmeldung und Buchungszugriff funktionieren nicht.', 'php artisan migrate --force ausführen.');
        } else {
            $this->markOk("Sitzungen: {$sessionDriver}");
        }

        if ($this->production && str_starts_with((string) config('app.url'), 'https://') && !config('session.secure')) {
            $this->markWarning('Sitzungs-Cookies werden nicht nur über HTTPS übertragen.', 'SESSION_SECURE_COOKIE=true setzen.');
        }
    }

    protected function checkMail(): void
    {
        $mailer = (string) config('mail.default');
        $transport = (string) config("mail.mailers.{$mailer}.transport", $mailer);

        if (in_array($transport, ['log', 'array'], true)) {
            $this->production
                ? $this->markError("MAIL_MAILER = {$mailer}: E-Mails werden nicht versendet (Buchungsbestätigungen, Tickets, Zugangsdaten!).", 'Einen echten Mailer (z. B. smtp) konfigurieren.')
                : $this->markOk("MAIL_MAILER = {$mailer} (E-Mails werden nur protokolliert).");
        } elseif ($transport === 'smtp') {
            $host = (string) config("mail.mailers.{$mailer}.host");
            $port = config("mail.mailers.{$mailer}.port");
            $user = (string) config("mail.mailers.{$mailer}.username");
            $pass = (string) config("mail.mailers.{$mailer}.password");

            if ($host === '' || !$port) {
                $this->markError('SMTP-Server oder -Port fehlt.', 'MAIL_HOST und MAIL_PORT setzen.');
            } else {
                $this->markOk("SMTP: {$host}:{$port}");
            }

            if (preg_match('/^your[-_]|mailtrap-username|mailtrap-password|^null$/i', $user . ' ' . $pass) || str_contains($user, 'your-')) {
                $this->markError('MAIL_USERNAME/MAIL_PASSWORD enthalten noch Platzhalter aus .env.example.', 'Echte Zugangsdaten des Mailservers eintragen.');
            }

            if ($this->production && str_contains($host, 'mailtrap')) {
                $this->markWarning('In Produktion wird ein Test-Postfach (Mailtrap) verwendet – Teilnehmende erhalten keine E-Mails.');
            }

            if ($this->option('live')) {
                try {
                    $symfony = Mail::mailer($mailer)->getSymfonyTransport();
                    if (method_exists($symfony, 'start')) {
                        $symfony->start();
                        $symfony->stop();
                    }
                    $this->markOk('SMTP-Verbindung und Anmeldung erfolgreich.');
                } catch (\Throwable $e) {
                    $this->markError('SMTP-Verbindung fehlgeschlagen: ' . $e->getMessage(), 'Server, Port, Verschlüsselung (MAIL_SCHEME) und Zugangsdaten prüfen.');
                }
            }
        } else {
            $this->markOk("MAIL_MAILER = {$mailer}");
        }

        $from = (string) config('mail.from.address');
        if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $this->markError("Absenderadresse ungültig: \"{$from}\".", 'MAIL_FROM_ADDRESS setzen.');
        } elseif ($this->production && preg_match('/@(.+\.)?(local|test|example\.(com|org|net)|localhost)$/i', $from)) {
            $this->markError("Absenderadresse {$from} gehört zu keiner echten Domain – E-Mails landen im Spam oder werden abgewiesen.", 'Adresse der eigenen Domain mit SPF/DKIM verwenden.');
        } else {
            $this->markOk("Absender: {$from}");
        }

        if (in_array((string) config('mail.from.name'), ['', 'Laravel', 'Example'], true)) {
            $this->markWarning('Absendername ist "' . config('mail.from.name') . '".', 'MAIL_FROM_NAME bzw. APP_NAME setzen.');
        }
    }

    protected function checkFiles(): void
    {
        $link = public_path('storage');
        is_link($link) || is_dir($link)
            ? $this->markOk('Öffentlicher Speicher ist verlinkt (public/storage).')
            : $this->markError('public/storage fehlt – Logos und Veranstaltungsbilder werden nicht angezeigt.', 'php artisan storage:link ausführen.');

        foreach (['storage/app', 'storage/framework/cache', 'storage/framework/views', 'storage/logs', 'bootstrap/cache'] as $dir) {
            if (!is_writable(base_path($dir))) {
                $this->markError("{$dir} ist nicht beschreibbar.", 'Dateirechte für den Webserver-Benutzer anpassen.');
            }
        }
        $this->markOk('Speicherverzeichnisse sind beschreibbar.');

        if (!is_file(public_path('build/manifest.json'))) {
            $this->markError('Frontend-Assets fehlen (public/build/manifest.json).', 'npm ci && npm run build ausführen.');
        } else {
            $this->markOk('Frontend-Assets sind gebaut.');
        }

        if (is_file(public_path('hot'))) {
            $this->production
                ? $this->markError('public/hot existiert – Seiten laden Styles vom Vite-Entwicklungsserver und erscheinen ungestaltet.', 'Datei public/hot löschen und npm run build ausführen.')
                : $this->markOk('Vite-Entwicklungsserver ist aktiv (public/hot).');
        }

        if (is_dir(public_path('_preview_tmp'))) {
            $this->markWarning('public/_preview_tmp enthält temporäre Vorschau-Dateien und ist öffentlich erreichbar.', 'Ordner löschen.');
        }
    }

    protected function checkSecurity(): void
    {
        if (config('recaptcha.enabled')) {
            if (!config('recaptcha.site_key') || !config('recaptcha.secret_key')) {
                $this->markError('reCAPTCHA ist aktiviert, aber Site-Key oder Secret-Key fehlen – Buchungen und Registrierungen schlagen fehl.', 'RECAPTCHA_SITE_KEY und RECAPTCHA_SECRET_KEY setzen oder RECAPTCHA_ENABLED=false.');
            } else {
                $this->markOk('reCAPTCHA ist aktiv und konfiguriert.');
            }
            $threshold = (float) config('recaptcha.score_threshold');
            if ($threshold < 0 || $threshold > 1) {
                $this->markError("RECAPTCHA_SCORE_THRESHOLD = {$threshold} liegt nicht zwischen 0 und 1.");
            }
        } else {
            $this->production
                ? $this->markWarning('reCAPTCHA ist deaktiviert – Buchungs- und Registrierungsformulare sind nicht vor Bots geschützt.', 'RECAPTCHA_ENABLED=true und Schlüssel setzen.')
                : $this->markOk('reCAPTCHA ist deaktiviert (Entwicklung).');
        }

        $this->markOk('Veranstalter-Registrierung: ' . (config('app.allow_organizer_registration') ? 'erlaubt' : 'deaktiviert'));
    }

    protected function checkSso(): void
    {
        $providers = [
            'keycloak' => ['name' => 'Keycloak', 'required' => ['client_id', 'client_secret', 'redirect', 'base_url', 'realms']],
            'google' => ['name' => 'Google', 'required' => ['client_id', 'client_secret', 'redirect']],
            'github' => ['name' => 'GitHub', 'required' => ['client_id', 'client_secret', 'redirect']],
        ];
        $appUrl = rtrim((string) config('app.url'), '/');

        foreach ($providers as $key => $provider) {
            $config = (array) config("services.{$key}", []);
            if (empty($config['client_id']) && empty($config['client_secret'])) {
                $this->markOk("{$provider['name']}: nicht eingerichtet (optional).");
                continue;
            }

            $missing = array_filter($provider['required'], fn ($field) => empty($config[$field]));
            if ($missing) {
                $this->markError("{$provider['name']}: unvollständig – es fehlt " . implode(', ', $missing) . '.', 'Fehlende Werte setzen oder Client-ID/Secret entfernen.');
                continue;
            }

            if (!str_starts_with((string) $config['redirect'], $appUrl)) {
                $this->markWarning("{$provider['name']}: Redirect-URI ({$config['redirect']}) passt nicht zu APP_URL ({$appUrl}).", 'Anmeldungen schlagen fehl, wenn die URI nicht beim Anbieter hinterlegt ist.');
            } else {
                $this->markOk("{$provider['name']}: eingerichtet.");
            }

            if ($key === 'keycloak' && !filter_var($config['base_url'], FILTER_VALIDATE_URL)) {
                $this->markError('Keycloak: KEYCLOAK_BASE_URL ist keine gültige URL.');
            }
        }
    }

    protected function checkPaypal(): void
    {
        if (!$this->requireDatabase()) {
            return;
        }

        $organizations = Organization::where('paypal_enabled', true)->get();
        if ($organizations->isEmpty()) {
            $this->markOk('Keine Organisation nutzt PayPal.');

            return;
        }

        foreach ($organizations as $organization) {
            $name = "\"{$organization->name}\"";

            try {
                $configured = $organization->hasPayPalConfigured();
            } catch (DecryptException) {
                $this->markError("{$name}: PayPal-Zugangsdaten können nicht entschlüsselt werden.", 'Wurde der APP_KEY geändert? Zugangsdaten in den PayPal-Einstellungen der Organisation neu eintragen.');
                continue;
            }

            if (!$configured) {
                $this->markWarning("{$name}: PayPal ist aktiviert, aber Client-ID/Secret fehlen oder sind ungültig – PayPal wird Kunden nicht angeboten.");
                continue;
            }

            $mode = (string) $organization->paypal_mode;
            if (!in_array($mode, ['sandbox', 'live'], true)) {
                $this->markError("{$name}: ungültiger PayPal-Modus \"{$mode}\".");
            } elseif ($mode === 'live' && empty($organization->paypal_webhook_id)) {
                $this->markError("{$name}: Live-Modus ohne Webhook-ID – Zahlungsbestätigungen per Webhook werden abgelehnt.", 'Webhook in PayPal anlegen (URL: ' . route('paypal.webhook') . ') und die Webhook-ID eintragen.');
            } elseif ($mode === 'sandbox' && $this->production) {
                $this->markWarning("{$name}: PayPal läuft im Sandbox-Modus – es fließt kein echtes Geld.");
            } else {
                $this->markOk("{$name}: PayPal ({$mode}) eingerichtet.");
            }
        }
    }

    protected function checkBilling(): void
    {
        $feeActive = (float) config('monetization.platform_fee_percentage') > 0
            || (float) config('monetization.platform_fee_fixed_amount') > 0;
        $featuredActive = (bool) config('monetization.featured_event_enabled');

        if (!in_array(config('monetization.platform_fee_type'), ['percentage', 'fixed'], true)) {
            $this->markError('PLATFORM_FEE_TYPE ist ungültig.', 'Erlaubt: percentage oder fixed.');
        }

        if (!$feeActive && !$featuredActive) {
            $this->markOk('Keine Plattformgebühren aktiv – Rechnungsdaten der Plattform werden nicht benötigt.');

            return;
        }

        $required = [
            'platform_company_name' => 'PLATFORM_COMPANY_NAME',
            'platform_company_address' => 'PLATFORM_COMPANY_ADDRESS',
            'platform_company_postal_code' => 'PLATFORM_COMPANY_POSTAL_CODE',
            'platform_company_city' => 'PLATFORM_COMPANY_CITY',
            'platform_company_email' => 'PLATFORM_COMPANY_EMAIL',
            'platform_bank_iban' => 'PLATFORM_BANK_IBAN',
        ];
        $missing = array_values(array_filter($required, fn ($env, $key) => blank(config("monetization.{$key}")), ARRAY_FILTER_USE_BOTH));
        if (blank(config('monetization.platform_tax_id')) && blank(config('monetization.platform_vat_id'))) {
            $missing[] = 'PLATFORM_TAX_ID oder PLATFORM_VAT_ID';
        }

        if ($missing) {
            $message = 'Plattformgebühren/Featured Events sind aktiv, aber Rechnungsdaten der Plattform fehlen: ' . implode(', ', $missing) . '.';
            $hint = 'Ohne diese Angaben sind Gebührenrechnungen an Veranstalter unvollständig (Pflichtangaben § 14 UStG).';
            $this->production ? $this->markError($message, $hint) : $this->markWarning($message, $hint);
        } else {
            $this->markOk('Rechnungsdaten der Plattform sind vollständig.');
        }

        $iban = (string) config('monetization.platform_bank_iban');
        if ($iban !== '' && !self::isValidIban($iban)) {
            $this->markError("PLATFORM_BANK_IBAN ({$iban}) ist keine gültige IBAN (Prüfziffer falsch).");
        }

        $email = (string) config('monetization.platform_company_email');
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->markError("PLATFORM_COMPANY_EMAIL ({$email}) ist keine gültige E-Mail-Adresse.");
        }

        if (config('monetization.currency') !== 'EUR') {
            $this->markWarning('Währung ist ' . config('monetization.currency') . ' – Rechnungen und PayPal sind auf EUR ausgelegt.');
        }
    }

    protected function checkPrivacy(): void
    {
        $booking = (int) config('privacy.booking_retention_months');
        $invoice = (int) config('privacy.invoice_retention_years');
        $waitlist = (int) config('privacy.waitlist_retention_months');

        $booking >= 1
            ? $this->markOk("Buchungs-Kontaktdaten werden {$booking} Monate nach der Veranstaltung anonymisiert.")
            : $this->markError('BOOKING_RETENTION_MONTHS muss mindestens 1 sein.');

        $invoice >= 10
            ? $this->markOk("Rechnungsdaten werden {$invoice} Jahre aufbewahrt.")
            : $this->markError("INVOICE_RETENTION_YEARS = {$invoice} unterschreitet die gesetzliche Aufbewahrungsfrist von 10 Jahren (§ 147 AO).", 'INVOICE_RETENTION_YEARS=10 setzen.');

        $waitlist >= 1
            ? $this->markOk("Wartelisten-Einträge werden {$waitlist} Monate nach der Veranstaltung gelöscht.")
            : $this->markError('WAITLIST_RETENTION_MONTHS muss mindestens 1 sein.');
    }

    protected function checkOrganizations(): void
    {
        if (!$this->requireDatabase()) {
            return;
        }

        // Nur Organisationen mit kommenden, veröffentlichten Veranstaltungen sind für Teilnehmende relevant
        $organizations = Organization::whereHas('events', fn ($q) => $q->listed())->get();
        if ($organizations->isEmpty()) {
            $this->markOk('Keine Organisation mit kommenden Veranstaltungen.');

            return;
        }

        $issues = 0;
        foreach ($organizations as $organization) {
            $name = "\"{$organization->name}\"";

            if (blank($organization->email)) {
                $this->markWarning("{$name}: keine Kontakt-E-Mail – Teilnehmende sehen in E-Mails keine Ansprechadresse.", 'In den Organisationseinstellungen eine E-Mail-Adresse hinterlegen.');
                $issues++;
            }

            $hasPaidTickets = $organization->events()->listed()
                ->whereHas('ticketTypes', fn ($q) => $q->where('price', '>', 0))->exists();

            if ($hasPaidTickets && !$organization->hasExternalInvoicing()) {
                if (!$organization->hasCompleteBillingData()) {
                    $this->markWarning("{$name}: Rechnungsdaten unvollständig – automatische Rechnungen an Teilnehmende sind fehlerhaft.", 'Organisation → Rechnungsdaten vervollständigen.');
                    $issues++;
                }
                if (!$organization->hasCompleteBankAccount()) {
                    $this->markWarning("{$name}: Bankverbindung unvollständig – Teilnehmende erhalten keine Überweisungsdaten.", 'Organisation → Bankverbindung vervollständigen.');
                    $issues++;
                }
            }
        }

        if ($issues === 0) {
            $this->markOk($organizations->count() . ' Organisation(en) mit kommenden Veranstaltungen vollständig eingerichtet.');
        }
    }

    protected function checkEvents(): void
    {
        if (!$this->requireDatabase()) {
            return;
        }

        $events = Event::listed()->where('is_cancelled', false)->with(['ticketTypes', 'organization'])->get();
        if ($events->isEmpty()) {
            $this->markOk('Keine kommenden veröffentlichten Veranstaltungen.');

            return;
        }

        $issues = 0;
        foreach ($events as $event) {
            $name = "\"{$event->title}\" ({$event->start_date->format('d.m.Y')})";

            if ($event->isExternal()) {
                if (!filter_var($event->external_booking_url, FILTER_VALIDATE_URL)) {
                    $this->markWarning("{$name}: externe Veranstaltung ohne gültigen Buchungslink.");
                    $issues++;
                }
                continue;
            }

            if ($event->ticketTypes->isEmpty() && $event->price_from === null) {
                $this->markWarning("{$name}: weder Ticketart noch Preis angelegt – die Veranstaltung ist nicht buchbar.", 'Im Event unter „Tickets“ eine Ticketart anlegen.');
                $issues++;
            }

            if ($event->requiresOnlineInfo() && !filter_var($event->online_url, FILTER_VALIDATE_URL)) {
                $this->markWarning("{$name}: Online-/Hybrid-Veranstaltung ohne gültigen Zugangslink – Teilnehmende erhalten keine Zugangsdaten.");
                $issues++;
            }

            if ($event->end_date && $event->end_date->lt($event->start_date)) {
                $this->markWarning("{$name}: Ende liegt vor dem Beginn.");
                $issues++;
            }
        }

        if ($issues === 0) {
            $this->markOk($events->count() . ' kommende Veranstaltung(en) ohne Auffälligkeiten.');
        }
    }

    // ─── Hilfsfunktionen ────────────────────────────────────────────────────

    public static function isValidIban(string $iban): bool
    {
        $iban = strtoupper(preg_replace('/\s+/', '', $iban));
        if (!preg_match('/^[A-Z]{2}\d{2}[A-Z0-9]{11,30}$/', $iban)) {
            return false;
        }

        $numeric = '';
        foreach (str_split(substr($iban, 4) . substr($iban, 0, 4)) as $char) {
            $numeric .= ctype_alpha($char) ? (string) (ord($char) - 55) : $char;
        }

        $remainder = 0;
        foreach (str_split($numeric, 7) as $chunk) {
            $remainder = (int) ($remainder . $chunk) % 97;
        }

        return $remainder === 1;
    }

    protected function databaseReachable(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    protected function requireDatabase(): bool
    {
        if (!$this->databaseAvailable) {
            $this->markSkipped('Übersprungen – keine Datenbankverbindung.');
        }

        return $this->databaseAvailable;
    }

    protected function markOk(string $message): void
    {
        $this->record('ok', $message);
    }

    protected function markWarning(string $message, ?string $hint = null): void
    {
        $this->record('warn', $message, $hint);
    }

    protected function markError(string $message, ?string $hint = null): void
    {
        $this->record('fail', $message, $hint);
    }

    protected function markSkipped(string $message): void
    {
        $this->record('skip', $message);
    }

    protected function record(string $level, string $message, ?string $hint = null): void
    {
        $this->results[] = ['section' => $this->section, 'level' => $level, 'message' => $message, 'hint' => $hint];
    }

    protected function report(): int
    {
        $symbols = [
            'ok' => '<fg=green>✓</>',
            'warn' => '<fg=yellow>!</>',
            'fail' => '<fg=red>✗</>',
            'skip' => '<fg=gray>–</>',
        ];

        foreach (collect($this->results)->groupBy('section') as $section => $items) {
            $this->newLine();
            $this->line('<options=bold>' . self::SECTIONS[$section] . '</>');
            foreach ($items as $item) {
                $this->line("  {$symbols[$item['level']]} {$item['message']}");
                if ($item['hint']) {
                    $this->line("    <fg=gray>→ {$item['hint']}</>");
                }
            }
        }

        $counts = collect($this->results)->countBy('level');
        $errors = $counts->get('fail', 0);
        $warnings = $counts->get('warn', 0);

        $this->newLine();
        $summary = sprintf('%d in Ordnung · %d Warnung(en) · %d Fehler', $counts->get('ok', 0), $warnings, $errors);
        if ($errors > 0) {
            $this->line("<fg=red;options=bold>✗ {$summary}</>");
        } elseif ($warnings > 0) {
            $this->line("<fg=yellow;options=bold>! {$summary}</>");
        } else {
            $this->line("<fg=green;options=bold>✓ {$summary}</>");
        }
        $this->newLine();

        return $errors > 0 || ($this->option('strict') && $warnings > 0) ? self::FAILURE : self::SUCCESS;
    }
}
