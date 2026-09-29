<?php

namespace App\Services;

use App\Mail\WaitlistTicketAvailable;
use App\Models\Event;
use App\Models\EventWaitlist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Warteliste mit befristeter Reservierung:
 * Werden Plätze frei, erhalten die nächsten Personen der Warteliste (in Reihenfolge der Anmeldung)
 * einen persönlichen Buchungslink. Die Plätze sind für sie RESERVIERT_STUNDEN lang reserviert und
 * für alle anderen nicht buchbar. Läuft die Reservierung ab, rückt automatisch die nächste Person nach.
 */
class WaitlistService
{
    public const RESERVATION_HOURS = 48;

    /**
     * Freie Plätze an die nächsten Wartenden vergeben.
     *
     * @return int Anzahl benachrichtigter Einträge
     */
    public function offerSeats(Event $event, int $seats): int
    {
        if ($seats <= 0 || $event->is_cancelled || $event->start_date->isPast()) {
            return 0;
        }

        $entries = EventWaitlist::where('event_id', $event->id)
            ->waiting()
            ->orderBy('created_at')
            ->get();

        $notified = 0;
        foreach ($entries as $entry) {
            if ($seats <= 0) {
                break;
            }
            // Reihenfolge wahren: passt der Nächste nicht, werden nur kleinere Anfragen berücksichtigt
            if ($entry->quantity > $seats) {
                continue;
            }

            $entry->markAsNotified();
            $seats -= $entry->quantity;
            $notified++;

            try {
                Mail::to($entry->email)->send(new WaitlistTicketAvailable($entry));
            } catch (\Throwable $e) {
                Log::error('Wartelisten-Benachrichtigung fehlgeschlagen', [
                    'waitlist_id' => $entry->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $notified;
    }

    /**
     * Abgelaufene Reservierungen beenden und die Plätze an die Nächsten weitergeben.
     *
     * @return int Anzahl abgelaufener Reservierungen
     */
    public function expireReservations(): int
    {
        $expired = EventWaitlist::where('status', 'notified')
            ->where('expires_at', '<=', now())
            ->with('event')
            ->get();

        foreach ($expired as $entry) {
            $entry->markAsExpired();
            if ($entry->event) {
                $this->offerSeats($entry->event, (int) $entry->quantity);
            }
        }

        return $expired->count();
    }

    /**
     * Gültige Reservierung zu einem Buchungslink finden.
     */
    public function findClaim(Event $event, ?string $token): ?EventWaitlist
    {
        if (!$token) {
            return null;
        }

        return EventWaitlist::where('event_id', $event->id)
            ->where('claim_token', $token)
            ->activeReservation()
            ->first();
    }
}
