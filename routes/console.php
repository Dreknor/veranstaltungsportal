<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule event reminders
Schedule::command('events:send-reminders --hours=24')
    ->dailyAt('09:00')
    ->timezone('Europe/Berlin')
    ->description('Send event reminders 24 hours before events');

// Optional: Additional reminder 3 hours before event
Schedule::command('events:send-reminders --hours=3')
    ->hourly()
    ->timezone('Europe/Berlin')
    ->description('Send last-minute event reminders');

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
