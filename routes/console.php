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

// Cleanup old notifications weekly
Schedule::command('notifications:cleanup --days=30')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone('Europe/Berlin')
    ->description('Delete old read notifications');

