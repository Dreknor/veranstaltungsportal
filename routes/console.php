<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Erinnerungen vor Veranstaltungsbeginn (24 h und 3 h vorher, inkl. Online-Zugangsdaten).
// Läuft stündlich; jede Buchung erhält jede Erinnerung höchstens einmal.
Schedule::command('events:send-reminders')
    ->hourly()
    ->timezone('Europe/Berlin')
    ->withoutOverlapping()
    ->description('Send event reminders 24h and 3h before events');

// Nachbereitung nach Veranstaltungsende: Teilnahmebescheinigungen + Bitte um Feedback
Schedule::command('events:send-follow-ups')
    ->hourly()
    ->timezone('Europe/Berlin')
    ->withoutOverlapping()
    ->description('Send certificates and feedback requests after events');

// Nicht abgeschlossene PayPal-Zahlungen nach 48 h freigeben
Schedule::command('bookings:release-abandoned')
    ->hourly()
    ->timezone('Europe/Berlin')
    ->description('Cancel abandoned PayPal bookings and release their tickets');

// DSGVO: personenbezogene Buchungsdaten nach Ablauf der Fristen anonymisieren (config/privacy.php)
Schedule::command('privacy:anonymize-bookings')
    ->dailyAt('04:00')
    ->timezone('Europe/Berlin')
    ->description('Anonymize personal booking data after retention periods');

// Abgelaufene Wartelisten-Reservierungen beenden und Nächste nachrücken lassen
// Clean expired waitlist entries every hour
Schedule::command('waitlist:clean-expired')
    ->hourly()
    ->timezone('Europe/Berlin')
    ->description('Mark expired waitlist entries as expired');

// Automatische Rechnungserstellung für beendete Events
Schedule::command('invoices:generate-event-invoices')
    ->dailyAt('03:00')
    ->timezone('Europe/Berlin')
    ->description('Generate platform fee invoices for ended events');

// Cleanup old notifications weekly
Schedule::command('notifications:cleanup --days=30')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone('Europe/Berlin')
    ->description('Delete old read notifications');

// Deaktiviere abgelaufene Featured Events täglich
Schedule::command('featured:disable-expired')
    ->dailyAt('00:00')
    ->timezone('Europe/Berlin')
    ->description('Disable expired featured events');

// Benachrichtige Veranstalter 3 Tage vor Ablauf des Featured-Status
Schedule::command('featured:notify-expiry')
    ->dailyAt('08:00')
    ->timezone('Europe/Berlin')
    ->description('Send expiry reminders for featured events (3 days before)');

// Benachrichtige Admins über ausstehende Featured-Zahlungen (> 7 Tage offen)
Schedule::command('featured:notify-pending-payments')
    ->dailyAt('08:30')
    ->timezone('Europe/Berlin')
    ->description('Notify admins about pending featured event payments older than 7 days');

// Queue Worker Mode Configuration (set in .env: QUEUE_WORKER_MODE)
// - 'cronjob': Queue jobs are processed every minute via cronjob (default, simple setup)
// - 'supervisor': Queue worker runs continuously as daemon (recommended for production)
if (env('QUEUE_WORKER_MODE', 'cronjob') === 'cronjob') {
    Schedule::command('queue:work --stop-when-empty --max-time=50')->everyMinute();
}
