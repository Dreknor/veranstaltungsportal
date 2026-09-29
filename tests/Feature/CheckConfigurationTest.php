<?php

use App\Console\Commands\CheckConfiguration;
use App\Models\Organization;
use Illuminate\Support\Facades\Cache;

test('fehlender APP_KEY ist ein Fehler', function () {
    config(['app.key' => 'base64:PLEASE_RUN_php_artisan_key_generate']);

    $this->artisan('app:check-config', ['--only' => 'app'])
        ->expectsOutputToContain('APP_KEY ist nicht gesetzt.')
        ->assertExitCode(1);
});

test('Produktionsregeln: Debug-Modus, lokale URL und Log-Mailer sind Fehler', function () {
    config([
        'app.debug' => true,
        'app.url' => 'http://localhost',
        'mail.default' => 'log',
    ]);

    $this->artisan('app:check-config', ['--only' => 'app,mail', '--production' => true])
        ->expectsOutputToContain('APP_DEBUG ist in Produktion aktiv')
        ->expectsOutputToContain('APP_URL zeigt auf eine lokale Adresse')
        ->expectsOutputToContain('E-Mails werden nicht versendet')
        ->assertExitCode(1);
});

test('Platzhalter aus .env.example bei den SMTP-Zugangsdaten werden erkannt', function () {
    config([
        'mail.default' => 'smtp',
        'mail.mailers.smtp.host' => 'smtp.example.org',
        'mail.mailers.smtp.port' => 587,
        'mail.mailers.smtp.username' => 'your-mailtrap-username',
        'mail.mailers.smtp.password' => 'your-mailtrap-password',
    ]);

    $this->artisan('app:check-config', ['--only' => 'mail'])
        ->expectsOutputToContain('Platzhalter')
        ->assertExitCode(1);
});

test('PayPal im Live-Modus ohne Webhook-ID ist ein Fehler', function () {
    Organization::factory()->create([
        'name' => 'Schulstiftung Live',
        'paypal_enabled' => true,
        'paypal_client_id' => 'live-client-id',
        'paypal_client_secret' => 'live-client-secret',
        'paypal_mode' => 'live',
        'paypal_webhook_id' => null,
    ]);

    $this->artisan('app:check-config', ['--only' => 'paypal'])
        ->expectsOutputToContain('"Schulstiftung Live": Live-Modus ohne Webhook-ID')
        ->assertExitCode(1);
});

test('unvollständige SSO-Konfiguration wird gemeldet', function () {
    config(['services.keycloak' => ['client_id' => 'portal', 'client_secret' => 'geheim', 'redirect' => null, 'base_url' => null, 'realms' => null]]);

    $this->artisan('app:check-config', ['--only' => 'sso'])
        ->expectsOutputToContain('Keycloak: unvollständig')
        ->assertExitCode(1);
});

test('Scheduler-Lebenszeichen wird ausgewertet', function () {
    Cache::forever('scheduler:last_run', now()->subMinute()->toIso8601String());
    $this->artisan('app:check-config', ['--only' => 'queue'])
        ->expectsOutputToContain('Scheduler läuft');

    Cache::forever('scheduler:last_run', now()->subHour()->toIso8601String());
    $this->artisan('app:check-config', ['--only' => 'queue', '--production' => true])
        ->expectsOutputToContain('Der Scheduler lief zuletzt')
        ->assertExitCode(1);
});

test('Warnungen führen nur mit --strict zu einem Fehlercode', function () {
    config(['app.locale' => 'en', 'app.key' => 'base64:' . base64_encode(random_bytes(32)), 'app.url' => 'https://portal.example.de']);

    $this->artisan('app:check-config', ['--only' => 'app'])->assertExitCode(0);
    $this->artisan('app:check-config', ['--only' => 'app', '--strict' => true])->assertExitCode(1);
});

test('unbekannte Bereiche werden abgelehnt', function () {
    $this->artisan('app:check-config', ['--only' => 'gibtsnicht'])
        ->expectsOutputToContain('Unbekannte Bereiche: gibtsnicht')
        ->assertExitCode(2);
});

test('IBAN-Prüfziffer wird korrekt berechnet', function () {
    expect(CheckConfiguration::isValidIban('DE89 3704 0044 0532 0130 00'))->toBeTrue()
        ->and(CheckConfiguration::isValidIban('DE89370400440532013001'))->toBeFalse()
        ->and(CheckConfiguration::isValidIban('0000000'))->toBeFalse();
});

test('der Scheduler schreibt ein Lebenszeichen', function () {
    Cache::forget('scheduler:last_run');

    $event = collect(app(\Illuminate\Console\Scheduling\Schedule::class)->events())
        ->first(fn ($e) => $e->description === 'scheduler-heartbeat');
    expect($event)->not->toBeNull();

    $event->run(app());
    expect(Cache::get('scheduler:last_run'))->not->toBeNull();
});
