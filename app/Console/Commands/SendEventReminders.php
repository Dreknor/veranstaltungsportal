<?php

namespace App\Console\Commands;

use App\Mail\EventReminderMail;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventDate;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Versendet Erinnerungen vor Veranstaltungsbeginn.
 *
 * Ablauf (stündlich per Scheduler):
 *  - Für jede bestätigte Buchung wird die "dringendste" fällige Erinnerung ermittelt
 *    (Standard: 24 h und 3 h vor Beginn) und genau einmal versendet.
 *  - Wer erst innerhalb eines Erinnerungsfensters gebucht hat, erhält diese Erinnerung nicht
 *    (die Buchungsbestätigung ist dann noch frisch), wohl aber die nächstkürzere.
 *  - Empfänger: Bucher (registriert oder Gast) sowie personalisierte Teilnehmende mit eigener E-Mail-Adresse.
 *  - Online-/Hybrid-Veranstaltungen: Die Erinnerung enthält die Zugangsdaten.
 *  - Veranstaltungen mit mehreren Terminen: Erinnerung vor jedem einzelnen Termin.
 */
class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders
                            {--hours=* : Erinnerungszeitpunkte in Stunden vor Beginn (Standard: 24 und 3)}
                            {--dry-run : Nur anzeigen, nichts versenden}';

    protected $description = 'Versendet Erinnerungen (inkl. Online-Zugangsdaten) vor Veranstaltungsbeginn';

    public function handle(): int
    {
        $offsets = collect($this->option('hours'))
            ->map(fn ($h) => (int) $h)
            ->filter(fn ($h) => $h > 0)
            ->unique()
            ->sortDesc()
            ->values();

        if ($offsets->isEmpty()) {
            $offsets = collect([24, 3]);
        }

        $dryRun = (bool) $this->option('dry-run');
        $maxOffset = $offsets->max();

        $windowEnd = now()->addHours($maxOffset);

        $events = Event::query()
            ->where('is_published', true)
            ->where('is_cancelled', false)
            ->where('event_type', '!=', 'external')
            ->where(function ($q) use ($windowEnd) {
                $q->whereBetween('start_date', [now(), $windowEnd])
                    ->orWhereHas('dates', fn ($d) => $d->where('is_cancelled', false)->whereBetween('start_date', [now(), $windowEnd]));
            })
            ->with('organization')
            ->get();

        $this->info("{$events->count()} Veranstaltung(en) innerhalb der nächsten {$maxOffset} Stunden.");

        $sent = 0;

        foreach ($events as $event) {
            $bookings = $event->bookings()
                ->whereIn('status', ['confirmed', 'completed'])
                ->with(['user', 'items'])
                ->get();

            // Einzeltermin: null (Hauptdatum). Mehrfachtermine: alle anstehenden Termine im Zeitfenster.
            $sessions = $event->hasMultipleDates()
                ? $event->dates()->where('is_cancelled', false)->whereBetween('start_date', [now(), $windowEnd])->get()
                : collect([null]);

            foreach ($sessions as $session) {
              foreach ($bookings as $booking) {
                $key = $this->dueReminderKey($event, $booking, $offsets->all(), $session);
                if ($key === null) {
                    continue;
                }

                if ($dryRun) {
                    $this->line("  [dry-run] {$key}-Erinnerung an {$booking->customer_email} ({$event->title})");
                    continue;
                }

                try {
                    $sent += $this->sendReminder($event, $booking, $session);
                    $this->markSent($booking, $key, $offsets->all(), $session);
                } catch (\Throwable $e) {
                    $this->error("  ✗ {$booking->booking_number}: {$e->getMessage()}");
                    Log::error('Event-Erinnerung fehlgeschlagen', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage(),
                    ]);
                }
              }
            }
        }

        $this->info("{$sent} Erinnerung(en) versendet.");

        return Command::SUCCESS;
    }

    /**
     * Ermittelt die fällige Erinnerung (kleinster Zeitpunkt, dessen Fenster bereits begonnen hat)
     * oder null, wenn nichts zu tun ist.
     */
    /**
     * @param  array<int, int>  $offsets
     */
    protected function dueReminderKey(Event $event, Booking $booking, array $offsets, ?EventDate $session = null): ?string
    {
        $start = $session !== null ? $session->start_date : $event->start_date;
        $prefix = $this->keyPrefix($session);

        $due = collect($offsets)
            ->filter(fn ($hours) => now()->gte($start->copy()->subHours($hours)))
            ->sort()
            ->first();

        if ($due === null) {
            return null;
        }

        // Bereits diese oder eine spätere (kürzere) Erinnerung erhalten?
        foreach ($offsets as $hours) {
            if ($hours <= $due && $booking->reminderWasSent($prefix . $hours)) {
                return null;
            }
        }

        // Erst innerhalb des Fensters gebucht → Bestätigung ist aktuell genug
        $bookedAt = $booking->confirmed_at ?? $booking->created_at;
        if ($bookedAt && $bookedAt->gt($start->copy()->subHours($due))) {
            return null;
        }

        return $prefix . $due;
    }

    /**
     * Schlüssel-Präfix je Termin, damit jede Erinnerung pro Termin genau einmal versendet wird.
     */
    protected function keyPrefix(?EventDate $session): string
    {
        return $session ? "d{$session->id}:" : '';
    }

    /**
     * @return int Anzahl versendeter E-Mails
     */
    protected function sendReminder(Event $event, Booking $booking, ?EventDate $session = null): int
    {
        $count = 0;

        if ($booking->user) {
            $preferences = $booking->user->notification_preferences ?? [];
            // Notification liefert In-App-Hinweis und (je nach Präferenz) E-Mail
            $booking->user->notify(new EventReminderNotification($event, $booking, $session));
            if (!isset($preferences['email_event_reminder']) || $preferences['email_event_reminder']) {
                $count++;
            }
        } else {
            Mail::to($booking->customer_email)->send(new EventReminderMail($event, $booking, null, false, $session));
            $count++;
        }

        // Personalisierte Teilnehmende mit abweichender E-Mail-Adresse ebenfalls erinnern
        $attendees = $booking->items
            ->filter(fn ($item) => $item->attendee_email
                && strcasecmp($item->attendee_email, $booking->customer_email) !== 0)
            ->unique(fn ($item) => mb_strtolower($item->attendee_email));

        foreach ($attendees as $item) {
            Mail::to($item->attendee_email)->send(
                new EventReminderMail($event, $booking, $item->attendee_name, true, $session)
            );
            $count++;
        }

        $this->line("  ✓ {$booking->booking_number} ({$count} Empfänger)");

        return $count;
    }

    /**
     * @param  array<int, int>  $offsets
     */
    protected function markSent(Booking $booking, string $key, array $offsets, ?EventDate $session = null): void
    {
        $prefix = $this->keyPrefix($session);
        $hoursSent = (int) substr($key, strlen($prefix));
        $sent = $booking->reminders_sent ?? [];
        $sent[$key] = now()->toIso8601String();

        // Längere Erinnerungen gelten als erledigt, damit sie nicht nachträglich verschickt werden
        foreach ($offsets as $hours) {
            if ($hours > $hoursSent && !isset($sent[$prefix . $hours])) {
                $sent[$prefix . $hours] = 'skipped';
            }
        }

        $booking->forceFill(['reminders_sent' => $sent])->saveQuietly();
    }
}
