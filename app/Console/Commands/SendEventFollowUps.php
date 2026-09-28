<?php

namespace App\Console\Commands;

use App\Mail\EventFollowUpMail;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Nachbereitung: Nach Ende einer Veranstaltung erhalten alle Teilnehmenden einmalig
 * Dank, Teilnahmebescheinigung(en) (für eingecheckte Personen) und die Bitte um Feedback.
 */
class SendEventFollowUps extends Command
{
    protected $signature = 'events:send-follow-ups
                            {--delay=2 : Stunden nach Veranstaltungsende}
                            {--days=14 : Nur Veranstaltungen, die höchstens so viele Tage zurückliegen}
                            {--dry-run : Nur anzeigen, nichts versenden}';

    protected $description = 'Versendet nach Veranstaltungsende Bescheinigungen und die Bitte um Feedback';

    public function handle(): int
    {
        $endedBefore = now()->subHours(max(0, (int) $this->option('delay')));
        $endedAfter = now()->subDays(max(1, (int) $this->option('days')));

        $events = Event::query()
            ->where('is_cancelled', false)
            ->where('event_type', '!=', 'external')
            ->where('end_date', '<=', $endedBefore)
            ->where('end_date', '>=', $endedAfter)
            ->get()
            // Mehrfachtermine: erst nach dem letzten Termin
            ->filter(function (Event $event) use ($endedBefore) {
                if (!$event->hasMultipleDates()) {
                    return true;
                }
                $last = $event->dates()->where('is_cancelled', false)->get()->last();

                return !$last || ($last->end_date ?? $last->start_date)->lte($endedBefore);
            });

        $sent = 0;

        foreach ($events as $event) {
            $bookings = Booking::where('event_id', $event->id)
                ->readyForParticipation()
                ->whereNull('follow_up_sent_at')
                ->whereNull('anonymized_at')
                ->with(['items', 'event.organization'])
                ->get();

            foreach ($bookings as $booking) {
                if ($this->option('dry-run')) {
                    $this->line("[dry-run] {$booking->booking_number} ({$event->title})");
                    continue;
                }

                try {
                    Mail::to($booking->customer_email)->queue(new EventFollowUpMail($booking));
                    $sent++;

                    foreach ($booking->items as $item) {
                        if ($item->checked_in && ($email = $item->separateAttendeeEmail())) {
                            Mail::to($email)->queue(new EventFollowUpMail($booking, $item));
                            $sent++;
                        }
                    }

                    $booking->forceFill(['follow_up_sent_at' => now()])->saveQuietly();
                } catch (\Throwable $e) {
                    Log::error('Nachbereitungs-E-Mail fehlgeschlagen', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info("{$sent} Nachbereitungs-E-Mail(s) versendet.");

        return Command::SUCCESS;
    }
}
