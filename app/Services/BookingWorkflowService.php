<?php

namespace App\Services;

use App\Mail\BookingCancellation;
use App\Mail\BookingConfirmation;
use App\Mail\BookingPendingApproval;
use App\Mail\BookingRejected;
use App\Mail\EventCancelledMail;
use App\Mail\PaymentConfirmed;
use App\Mail\TicketTypeChanged;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Event;
use App\Models\TicketType;
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
 *  8. Ticketart durch Veranstalter geändert    → "Ticketart geändert" inkl. neuer Tickets
 *     (aus kostenfrei wird kostenpflichtig      → zusätzlich "Buchungsbestätigung" mit Rechnung)
 */
class BookingWorkflowService
{
    /**
     * Zahlung (oder externe Fakturierung) verbuchen, Buchung bestätigen und Tickets/Zugangsdaten versenden.
     *
     * @return bool true, wenn Tickets/Zugangsdaten versendet wurden
     */
    /**
     * @param  array<string, mixed>  $attributes  zusätzliche Buchungsfelder (z. B. externe Rechnungsnummer)
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
    public function releaseTicketsBeforePayment(Booking $booking, bool $queue = false): bool
    {
        if (!in_array($booking->status, ['pending', 'confirmed']) || $booking->isFree() || $booking->isReadyForParticipation()) {
            return false;
        }

        $booking->update([
            'release_tickets_before_payment' => true,
            'status' => 'confirmed',
            'confirmed_at' => $booking->confirmed_at ?? now(),
        ]);

        return $this->deliverTickets($booking, $queue);
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
    public function notifyOrganizers(Booking $booking, \Illuminate\Notifications\Notification $notification): void
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

                return (bool) ($preferences['booking_notifications'] ?? true);
            })
            ->values();
    }

    /**
     * Darf der Veranstalter die Ticketart von Positionen dieser Buchung ändern?
     */
    public function canChangeTicketType(Booking $booking): bool
    {
        return in_array($booking->status, ['pending', 'pending_approval', 'confirmed'], true);
    }

    /**
     * Ist der Preis der Buchung festgeschrieben? Das ist nur der Fall, wenn bereits bezahlt wurde.
     * Kostenfreie Buchungen ("paid" mit 0 €) und extern fakturierte Buchungen gelten nicht als bezahlt.
     * Eine bereits vergebene Rechnungsnummer bleibt bei einer Preisänderung erhalten; die Person
     * erhält die korrigierte Rechnung mit der Änderungs-E-Mail.
     */
    public function isPriceLocked(Booking $booking): bool
    {
        return $booking->payment_status === 'paid' && !$booking->isFree();
    }

    /**
     * Hinweise, warum eine Ticketart regulär nicht gebucht werden könnte. Der Veranstalter darf
     * trotzdem zuweisen, muss die Hinweise aber ausdrücklich bestätigen.
     *
     * @return list<string>
     */
    public function ticketTypeWarnings(TicketType $ticketType, int $seats = 1): array
    {
        $warnings = [];

        if (!$ticketType->is_available) {
            $warnings[] = 'nicht freigeschaltet';
        } elseif (!$ticketType->isOnSale()) {
            $warnings[] = 'außerhalb des Verkaufszeitraums';
        }

        if ($ticketType->quantity !== null) {
            $free = (int) $ticketType->quantity - (int) $ticketType->quantity_sold
                - (int) ($ticketType->event?->reservedWaitlistSeats($ticketType->id) ?? 0);
            if ($free < $seats) {
                $warnings[] = $free > 0 ? "nur noch {$free} frei – Kontingent wird überbucht" : 'ausverkauft – Kontingent wird überbucht';
            }
        }

        return $warnings;
    }

    /**
     * Ticketart einzelner Positionen durch den Veranstalter ändern (z. B. bei Fehlbuchung einer
     * Ticketart, die nur für bestimmte Personen gedacht ist).
     *
     *  - Kontingente werden umgebucht (alte Ticketart frei, neue belegt – ggf. überbucht).
     *  - Unbezahlte Buchungen erhalten den neuen Preis, die Summe wird neu berechnet; der Rabatt bleibt
     *    als fester Betrag erhalten und wird höchstens auf die neue Zwischensumme gedeckelt.
     *  - Bezahlte Buchungen (oder mit Rechnungsnummer) behalten den bisherigen Preis.
     *  - Ticketnummer und Check-in bleiben erhalten; Tickets werden mit der Änderungs-E-Mail neu versendet.
     *
     * @param  array<int, int|string>  $itemIds
     * @return array{changed: int, price_locked: bool, old_total: string, new_total: string, payment_requested: bool}
     */
    public function changeTicketType(Booking $booking, array $itemIds, TicketType $target, string $reason): array
    {
        $itemIds = array_map('intval', array_values($itemIds));

        $result = DB::transaction(function () use ($booking, $itemIds, $target, $reason) {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $booking->load(['event', 'items.ticketType']);

            $items = $booking->items
                ->whereIn('id', $itemIds)
                ->where('ticket_type_id', '!=', $target->id)
                ->values();

            $oldTotal = (string) $booking->total;
            $priceLocked = $this->isPriceLocked($booking);

            if ($items->isEmpty()) {
                return ['booking' => $booking, 'changes' => [], 'freed' => [], 'price_locked' => $priceLocked,
                    'old_total' => $oldTotal, 'payment_requested' => false];
            }

            $wasFree = $booking->isFree();
            $wasReady = $booking->isReadyForParticipation();

            // Kontingente sperren, damit parallele Buchungen nicht falsch zählen
            $ticketTypes = TicketType::whereIn('id', $items->pluck('ticket_type_id')->push($target->id)->unique())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $changes = [];
            $freed = [];
            foreach ($items as $item) {
                $oldType = $ticketTypes->get($item->ticket_type_id);
                $oldPrice = (string) $item->price;

                $item->ticketType()->associate($target);
                if (!$priceLocked) {
                    $item->price = $target->price;
                }
                $item->save();

                $quantity = (int) $item->quantity;
                if ($oldType) {
                    $oldType->quantity_sold = max(0, (int) $oldType->quantity_sold - $quantity);
                    $freed[$oldType->id] = ($freed[$oldType->id] ?? 0) + $quantity;
                }

                $change = [
                    'item_id' => $item->id,
                    'ticket_number' => $item->ticket_number,
                    'attendee' => $item->participantName(),
                    'old_ticket_type_id' => $oldType?->id,
                    'old_ticket_type' => $oldType->name ?? 'Ticket',
                    'new_ticket_type_id' => $target->id,
                    'new_ticket_type' => $target->name,
                    'old_price' => $oldPrice,
                    'new_price' => (string) $item->price,
                ];
                $changes[] = $change;

                AuditLog::log(
                    'booking.ticket_type_changed',
                    $booking,
                    ['item_id' => $item->id, 'ticket_number' => $item->ticket_number, 'ticket_type_id' => $change['old_ticket_type_id'], 'ticket_type' => $change['old_ticket_type'], 'price' => $oldPrice],
                    ['item_id' => $item->id, 'ticket_number' => $item->ticket_number, 'ticket_type_id' => $target->id, 'ticket_type' => $target->name, 'price' => $change['new_price']],
                    $reason,
                );
            }

            $target = $ticketTypes->get($target->id);
            $target->quantity_sold = (int) $target->quantity_sold + (int) $items->sum('quantity');
            $ticketTypes->each->save();

            $paymentRequested = false;
            if (!$priceLocked) {
                $subtotal = round($booking->items->sum(fn ($item) => (float) $item->price * (int) $item->quantity), 2);
                $discount = min((float) $booking->discount, $subtotal);
                $updates = [
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => round($subtotal - $discount, 2),
                ];

                if ($wasFree && $updates['total'] > 0) {
                    // Aus der kostenfreien Buchung wird eine kostenpflichtige: Zahlung per Rechnung anfordern.
                    // Bereits ausgegebene Tickets bleiben gültig (Rechnung folgt).
                    $updates['payment_status'] = 'pending';
                    $updates['payment_method'] = 'invoice';
                    $updates['release_tickets_before_payment'] = $wasReady ? true : null;
                    $paymentRequested = true;
                } elseif (!$wasFree && $updates['total'] == 0 && $booking->payment_status === 'pending') {
                    // Keine Zahlung mehr nötig: wie eine kostenfreie Buchung behandeln (ohne Rechnung)
                    $updates['payment_status'] = 'paid';
                    $updates['invoice_number'] = null;
                    $updates['invoice_date'] = null;
                    if ($booking->status === 'pending') {
                        $updates['status'] = 'confirmed';
                        $updates['confirmed_at'] = $booking->confirmed_at ?? now();
                    }
                }

                $booking->update($updates);
            }

            return ['booking' => $booking, 'changes' => $changes, 'freed' => $freed, 'price_locked' => $priceLocked,
                'old_total' => $oldTotal, 'payment_requested' => $paymentRequested];
        });

        $booking = $result['booking'];

        if ($result['changes'] !== []) {
            $this->offerFreedSeatsToWaitlist($booking->event, $result['freed']);
            $this->sendTicketTypeChangeMails($booking, $result['changes'], $reason, $result['price_locked'], $result['old_total'], $result['payment_requested']);
        }

        return [
            'changed' => count($result['changes']),
            'price_locked' => $result['price_locked'],
            'old_total' => $result['old_total'],
            'new_total' => (string) $booking->total,
            'payment_requested' => $result['payment_requested'],
        ];
    }

    /**
     * Frei gewordene Plätze einer Ticketart der Warteliste anbieten (wie bei einer Stornierung),
     * sofern die Ticketart und die Veranstaltung tatsächlich wieder Platz haben.
     *
     * @param  array<int, int>  $freed  Ticketart-ID => Anzahl frei gewordener Plätze
     */
    protected function offerFreedSeatsToWaitlist(?Event $event, array $freed): void
    {
        if (!$event || $event->is_cancelled || $event->start_date->isPast()) {
            return;
        }

        foreach ($freed as $ticketTypeId => $seats) {
            $ticketType = TicketType::with('event')->find($ticketTypeId);
            if (!$ticketType) {
                continue;
            }

            $seats = min($seats, $ticketType->availableQuantity());
            if ($seats > 0) {
                app(WaitlistService::class)->offerSeats($event, $seats);
            }
        }
    }

    /**
     * @param  list<array<string, mixed>>  $changes
     */
    protected function sendTicketTypeChangeMails(Booking $booking, array $changes, string $reason, bool $priceLocked, string $oldTotal, bool $paymentRequested): void
    {
        $booking->refresh()->load(['event.organization', 'items.ticketType']);

        $this->send($booking, new TicketTypeChanged($booking, $changes, $reason, $priceLocked, $oldTotal));

        // Aus kostenfrei wurde kostenpflichtig: Rechnung und Zahlungshinweise wie bei einer neuen Buchung
        if ($paymentRequested) {
            $this->send($booking, new BookingConfirmation($booking));
        }

        if (!$booking->canSendTickets()) {
            return;
        }

        $changedIds = array_column($changes, 'item_id');
        foreach ($booking->items->whereIn('id', $changedIds) as $item) {
            $email = $item->separateAttendeeEmail();
            if (!$email) {
                continue;
            }

            try {
                Mail::to($email)->send(new \App\Mail\AttendeeTicketMail($booking, $item));
                $item->forceFill(['ticket_sent_to' => $email, 'ticket_sent_at' => now()])->saveQuietly();
            } catch (\Throwable $e) {
                Log::error('Teilnehmer-Ticket konnte nicht versendet werden', [
                    'booking_number' => $booking->booking_number,
                    'item_id' => $item->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
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

    protected function notifyCustomer(Booking $booking, \Illuminate\Notifications\Notification $notification): void
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
