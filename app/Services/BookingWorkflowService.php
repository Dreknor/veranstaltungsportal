<?php

namespace App\Services;

use App\Mail\BookingCancellation;
use App\Mail\BookingConfirmation;
use App\Mail\BookingPendingApproval;
use App\Mail\BookingRejected;
use App\Mail\EventCancelledMail;
use App\Mail\PaymentConfirmed;
use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Notifications\EventCancelledNotification;
use App\Notifications\PaymentStatusChangedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

/**
 * Zentrale Stelle für alle Statusübergänge einer Buchung und die daraus folgende Kommunikation.
 *
 * Kommunikationsregeln (Kunde):
 *  1. Buchung eingegangen
 *     - kostenfrei + automatische Bestätigung → "Buchungsbestätigung" inkl. Tickets/Zugangsdaten
 *     - kostenfrei + manuelle Freigabe        → "Anmeldung eingegangen" (ohne Zugangsdaten)
 *     - kostenpflichtig (Rechnung)             → "Buchungsbestätigung" mit Rechnung + Zahlungsinfos
 *     - kostenpflichtig (PayPal)               → keine Mail bis zur Zahlung
 *  2. Freigabe durch Veranstalter              → "Buchungsbestätigung" inkl. Tickets/Zugangsdaten
 *  3. Zahlung eingegangen / extern fakturiert  → "Zahlung bestätigt" inkl. Tickets/Zugangsdaten
 *     Vorab-Freigabe (Rechnung folgt später)   → "Buchung bestätigt" inkl. Tickets/Zugangsdaten
 *  4. Personalisierung abgeschlossen           → "Zahlung bestätigt" (Tickets mit Namen)
 *     + jede eingetragene Person mit eigener E-Mail-Adresse erhält ihr eigenes Ticket/ihren Zugang
 *  5. 24 h und 3 h vor Beginn                  → "Erinnerung" inkl. Zugangsdaten (Online/Hybrid)
 *  6. Änderung von Termin/Ort/Zugangsdaten     → "Veranstaltung aktualisiert"
 *  7. Storno / Ablehnung / Absage              → jeweils genau eine passende E-Mail
 */
class BookingWorkflowService
{
    /**
     * Zahlung (oder externe Fakturierung) verbuchen, Buchung bestätigen und Tickets/Zugangsdaten versenden.
     *
     * @return bool true, wenn Tickets/Zugangsdaten versendet wurden
     */
    public function confirmPayment(Booking $booking, string $paymentStatus = 'paid', array $attributes = [], bool $queue = false): bool
    {
        // Wurden Tickets schon vorher freigegeben (bezahlt oder Vorab-Freigabe), nicht erneut senden
        $wasComplete = $booking->isReadyForParticipation();

        $updates = array_merge(['payment_status' => $paymentStatus], $attributes);

        // Eine offene Buchung wird mit Zahlungseingang automatisch bestätigt.
        // Buchungen, die auf Freigabe warten, bleiben in der Freigabe.
        if ($booking->status === 'pending') {
            $updates['status'] = 'confirmed';
            $updates['confirmed_at'] = $booking->confirmed_at ?? now();
        }

        $booking->update($updates);

        if ($wasComplete) {
            return false;
        }

        return $this->deliverTickets($booking, $queue);
    }

    /**
     * Tickets/Zugangsdaten vor Zahlung bzw. Rechnungsstellung freigeben (Rechnung folgt, ggf. nach der Veranstaltung).
     *
     * @return bool true, wenn Tickets/Zugangsdaten versendet wurden
     */
    public function releaseTicketsBeforePayment(Booking $booking): bool
    {
        if (!in_array($booking->status, ['pending', 'confirmed']) || $booking->isFree() || $booking->isReadyForParticipation()) {
            return false;
        }

        $booking->update([
            'release_tickets_before_payment' => true,
            'status' => 'confirmed',
            'confirmed_at' => $booking->confirmed_at ?? now(),
        ]);

        return $this->deliverTickets($booking);
    }

    /**
     * Tickets bzw. Zugangsdaten zustellen ("Zahlung bestätigt"), sofern die Buchung freigegeben ist.
     * Bei ausstehender Personalisierung enthält die E-Mail stattdessen die Aufforderung dazu.
     * Eingetragene Teilnehmende mit eigener E-Mail-Adresse erhalten zusätzlich ihr eigenes Ticket.
     */
    public function deliverTickets(Booking $booking, bool $queue = false): bool
    {
        $booking->refresh()->load(['event.organization', 'items.ticketType']);

        if (!$booking->isReadyForParticipation()) {
            return false;
        }

        $sent = $this->send($booking, new PaymentConfirmed($booking), $queue);
        $this->deliverAttendeeTickets($booking, $queue);

        return $sent;
    }

    /**
     * Jede eingetragene Person mit eigener E-Mail-Adresse erhält ihr Ticket bzw. ihre Zugangsdaten direkt.
     * Bereits versendete Tickets gehen nur erneut raus, wenn sich die E-Mail-Adresse geändert hat.
     *
     * @return int Anzahl versendeter E-Mails
     */
    public function deliverAttendeeTickets(Booking $booking, bool $queue = false, bool $force = false): int
    {
        if (!$booking->canSendTickets()) {
            return 0;
        }

        $count = 0;
        foreach ($booking->items as $item) {
            $email = $item->separateAttendeeEmail();
            if (!$email || (!$force && $item->ticket_sent_to && strcasecmp($item->ticket_sent_to, $email) === 0)) {
                continue;
            }

            try {
                $mail = Mail::to($email);
                $mailable = new \App\Mail\AttendeeTicketMail($booking, $item);
                $queue ? $mail->queue($mailable) : $mail->send($mailable);
                $item->forceFill(['ticket_sent_to' => $email, 'ticket_sent_at' => now()])->saveQuietly();
                $count++;
            } catch (\Throwable $e) {
                Log::error('Teilnehmer-Ticket konnte nicht versendet werden', [
                    'booking_number' => $booking->booking_number,
                    'item_id' => $item->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }

    /**
     * Kostenfreie Anmeldung durch den Veranstalter freigeben.
     */
    public function approve(Booking $booking, bool $queue = false): bool
    {
        if ($booking->status !== 'pending_approval') {
            return false;
        }

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
        $booking->refresh()->load(['event.organization', 'items.ticketType']);

        $sent = $this->send($booking, new BookingConfirmation($booking), $queue);
        $this->deliverAttendeeTickets($booking, $queue);

        return $sent;
    }

    /**
     * Anmeldung ablehnen (nur solange sie auf Freigabe wartet).
     */
    public function reject(Booking $booking, ?string $reason = null): bool
    {
        if ($booking->status !== 'pending_approval') {
            return false;
        }

        $this->markCancelled($booking, 'organizer', $reason);

        return $this->send($booking, new BookingRejected($booking, $reason));
    }

    /**
     * Buchung stornieren (durch Kunde oder Veranstalter).
     *
     * @param  string  $by  'customer' | 'organizer'
     */
    public function cancel(Booking $booking, string $by = 'customer', ?string $reason = null, bool $notifyCustomer = true): bool
    {
        if (!$booking->isActive() || $booking->status === 'completed') {
            return false;
        }

        $this->markCancelled($booking, $by, $reason);

        if ($notifyCustomer) {
            $this->send($booking, new BookingCancellation($booking));
        }

        if ($by === 'customer') {
            $this->notifyOrganizers($booking, new \App\Notifications\BookingCancelledNotification($booking));
        }

        return true;
    }

    /**
     * Veranstaltung absagen: alle aktiven Buchungen stornieren und jeden Kunden genau einmal informieren.
     *
     * @return int Anzahl informierter Buchungen
     */
    public function cancelAllForEvent(Event $event): int
    {
        $bookings = $event->bookings()
            ->whereIn('status', ['pending', 'pending_approval', 'confirmed'])
            ->with(['user', 'items.ticketType'])
            ->get();

        foreach ($bookings as $booking) {
            $wasPaid = $booking->payment_status === 'paid' && !$booking->isFree();

            DB::transaction(function () use ($booking, $event) {
                // Leise aktualisieren: keine zusätzliche "Status geändert"-Mail und keine Wartelisten-Benachrichtigung
                $booking->forceFill([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancelled_by' => 'event',
                    'cancellation_reason' => $event->cancellation_reason,
                ])->saveQuietly();
                $this->releaseSeats($booking);
            });

            $booking->setRelation('event', $event);
            $this->send($booking, new EventCancelledMail($event, $booking), true);

            // In-App-Hinweis für registrierte Nutzer (ohne zusätzliche E-Mail)
            $booking->user?->notify(new EventCancelledNotification($event, $booking, ['database']));

            if ($wasPaid) {
                Log::info('Absage: Erstattung durch Veranstalter erforderlich', [
                    'booking_number' => $booking->booking_number,
                    'total' => $booking->total,
                ]);
            }
        }

        return $bookings->count();
    }

    /**
     * Zahlungsstatus ändern (Veranstalter). Bezahlt/Extern lösen die Ticket-Zustellung aus,
     * Erstattung und Fehlschlag werden dem Kunden per E-Mail mitgeteilt.
     *
     * @return string Rückmeldung für den Veranstalter
     */
    public function changePaymentStatus(Booking $booking, string $paymentStatus): string
    {
        if (in_array($paymentStatus, ['paid', 'extern'])) {
            $attributes = $paymentStatus === 'extern'
                ? ['externally_invoiced' => true, 'externally_invoiced_at' => $booking->externally_invoiced_at ?? now()]
                : [];

            $sent = $this->confirmPayment($booking, $paymentStatus, $attributes);

            if ($sent) {
                return 'Zahlung verbucht. Die Buchung ist bestätigt und Tickets bzw. Zugangsdaten wurden per E-Mail versendet.';
            }
            if ($booking->status === 'pending_approval') {
                return 'Zahlung verbucht. Die Anmeldung wartet noch auf Ihre Freigabe – erst dann werden Tickets versendet.';
            }

            return 'Zahlungsstatus aktualisiert.';
        }

        $old = $booking->payment_status;
        $booking->update(['payment_status' => $paymentStatus]);

        if (in_array($paymentStatus, ['refunded', 'failed']) && $old !== $paymentStatus) {
            $this->notifyCustomer($booking, new PaymentStatusChangedNotification($booking, $old, $paymentStatus, ['mail']));

            return 'Zahlungsstatus aktualisiert. Der Kunde wurde per E-Mail informiert.';
        }

        return 'Zahlungsstatus aktualisiert.';
    }

    /**
     * Die zum aktuellen Stand passende E-Mail erneut versenden.
     *
     * @return string|null Bezeichnung der versendeten E-Mail oder null, wenn nichts versendet werden kann
     */
    public function resendCurrentState(Booking $booking): ?string
    {
        $booking->loadMissing(['event.organization', 'items.ticketType']);

        $mailable = match (true) {
            $booking->status === 'pending_approval' => new BookingPendingApproval($booking),
            $booking->isReadyForParticipation() && !$booking->isFree() => new PaymentConfirmed($booking),
            in_array($booking->status, ['pending', 'confirmed', 'completed']) => new BookingConfirmation($booking),
            default => null,
        };

        if (!$mailable || !$this->send($booking, $mailable)) {
            return null;
        }

        if ($booking->isReadyForParticipation()) {
            $this->deliverAttendeeTickets($booking, false, true);
        }

        return \App\Models\BookingEmailLog::TYPE_LABELS[class_basename($mailable)] ?? class_basename($mailable);
    }

    /**
     * Veranstalter-Benachrichtigung an alle Owner/Admins der Organisation (mit aktivierten Buchungsbenachrichtigungen).
     */
    public function notifyOrganizers(Booking $booking, $notification): void
    {
        foreach ($this->organizerRecipients($booking->event) as $user) {
            try {
                $user->notify($notification);
            } catch (\Throwable $e) {
                Log::error('Veranstalter-Benachrichtigung fehlgeschlagen', [
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @return Collection<int, User>
     */
    public function organizerRecipients(?Event $event): Collection
    {
        $organization = $event?->organization;
        if (!$organization) {
            return collect();
        }

        $recipients = $organization->users()
            ->wherePivotIn('role', ['owner', 'admin'])
            ->get();

        if ($recipients->isEmpty() && $event->user) {
            $recipients = collect([$event->user]);
        }

        return $recipients
            ->unique('id')
            ->filter(function (User $user) {
                $preferences = $user->notification_preferences ?? [];

                return !is_array($preferences) || ($preferences['booking_notifications'] ?? true);
            })
            ->values();
    }

    /**
     * Gebuchte Kontingente (Plätze) wieder freigeben.
     */
    public function releaseSeats(Booking $booking): void
    {
        $booking->loadMissing('items.ticketType');

        foreach ($booking->items->groupBy('ticket_type_id') as $items) {
            $ticketType = $items->first()->ticketType;
            if (!$ticketType) {
                continue;
            }

            $quantity = (int) $items->sum('quantity');
            $ticketType->quantity_sold = max(0, (int) $ticketType->quantity_sold - $quantity);
            $ticketType->save();
        }
    }

    protected function markCancelled(Booking $booking, string $by, ?string $reason): void
    {
        DB::transaction(function () use ($booking, $by, $reason) {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $by,
                'cancellation_reason' => $reason,
            ]);
            $this->releaseSeats($booking);
        });
    }

    /**
     * E-Mail an den Kunden senden, Fehler protokollieren statt den Ablauf abzubrechen.
     */
    protected function send(Booking $booking, \Illuminate\Mail\Mailable $mailable, bool $queue = false): bool
    {
        try {
            // Massenaktionen (Sammelfreigabe, Absage, Sammel-Fakturierung) laufen über die Queue,
            // damit der Seitenaufruf nicht auf den Versand vieler E-Mails wartet.
            $queue
                ? Mail::to($booking->customer_email)->queue($mailable)
                : Mail::to($booking->customer_email)->send($mailable);

            return true;
        } catch (\Throwable $e) {
            Log::error('Buchungs-E-Mail konnte nicht versendet werden', [
                'booking_number' => $booking->booking_number,
                'mail' => class_basename($mailable),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function notifyCustomer(Booking $booking, $notification): void
    {
        try {
            if ($booking->user) {
                $booking->user->notify($notification);
            } else {
                Notification::route('mail', $booking->customer_email)->notify($notification);
            }
        } catch (\Throwable $e) {
            Log::error('Kunden-Benachrichtigung fehlgeschlagen', [
                'booking_number' => $booking->booking_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
