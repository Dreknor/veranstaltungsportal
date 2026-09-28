<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\BookingWorkflowService;
use Illuminate\Console\Command;

/**
 * Gibt Plätze aus abgebrochenen PayPal-Zahlungen wieder frei.
 *
 * PayPal-Buchungen werden vor der Zahlung angelegt und blockieren Plätze. Wird die Zahlung
 * nicht innerhalb der Frist abgeschlossen (oder beginnt die Veranstaltung), wird die Buchung
 * automatisch storniert und der Kunde informiert.
 */
class ReleaseAbandonedBookings extends Command
{
    protected $signature = 'bookings:release-abandoned
                            {--hours=48 : Frist in Stunden, nach der unbezahlte PayPal-Buchungen freigegeben werden}
                            {--dry-run : Nur anzeigen, nichts ändern}';

    protected $description = 'Storniert nicht abgeschlossene PayPal-Buchungen und gibt die Plätze frei';

    public function handle(BookingWorkflowService $workflow): int
    {
        $hours = max(1, (int) $this->option('hours'));

        $bookings = Booking::query()
            ->where('status', 'pending')
            ->where('payment_status', 'pending')
            ->where('payment_method', 'paypal')
            ->where('total', '>', 0)
            ->where(function ($query) use ($hours) {
                $query->where('created_at', '<', now()->subHours($hours))
                    ->orWhereHas('event', fn ($q) => $q->where('start_date', '<', now()));
            })
            ->with(['event.organization', 'items.ticketType'])
            ->get();

        foreach ($bookings as $booking) {
            if ($this->option('dry-run')) {
                $this->line("[dry-run] {$booking->booking_number} ({$booking->customer_email})");
                continue;
            }

            $workflow->cancel(
                $booking,
                'system',
                'Die PayPal-Zahlung wurde nicht abgeschlossen. Die reservierten Plätze wurden wieder freigegeben.'
            );
            $this->line("✓ {$booking->booking_number} freigegeben");
        }

        $this->info("{$bookings->count()} Buchung(en) bearbeitet.");

        return Command::SUCCESS;
    }
}
