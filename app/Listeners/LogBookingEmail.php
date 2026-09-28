<?php

namespace App\Listeners;

use App\Models\Booking;
use App\Models\BookingEmailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

/**
 * Schreibt jede versendete E-Mail mit Buchungsbezug in den E-Mail-Verlauf der Buchung.
 *
 * Erkennung:
 * - Mailables mit öffentlicher $booking-Eigenschaft (landen in den View-Daten)
 * - Notifications/Mails mit Header "X-Booking-Id"
 */
class LogBookingEmail
{
    public function handle(MessageSent $event): void
    {
        try {
            $bookingId = $this->resolveBookingId($event);
            if (!$bookingId) {
                return;
            }

            $headers = $event->message->getHeaders();
            $type = $headers->has('X-Mail-Type')
                ? $headers->get('X-Mail-Type')->getBodyAsString()
                : $this->resolveType($event);

            $recipients = collect($event->message->getTo())
                ->map(fn ($address) => $address->getAddress())
                ->implode(', ');

            BookingEmailLog::create([
                'booking_id' => $bookingId,
                'type' => $type,
                'subject' => mb_substr((string) $event->message->getSubject(), 0, 255),
                'recipient' => mb_substr($recipients, 0, 255),
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Das Protokollieren darf den Versand niemals stören
            Log::warning('E-Mail-Verlauf konnte nicht geschrieben werden', ['error' => $e->getMessage()]);
        }
    }

    protected function resolveBookingId(MessageSent $event): ?int
    {
        $headers = $event->message->getHeaders();
        if ($headers->has('X-Booking-Id')) {
            return (int) $headers->get('X-Booking-Id')->getBodyAsString() ?: null;
        }

        $booking = $event->data['booking'] ?? null;

        return $booking instanceof Booking ? $booking->id : null;
    }

    protected function resolveType(MessageSent $event): string
    {
        $class = $event->data['__laravel_mailable'] ?? $event->data['__laravel_notification'] ?? null;

        return $class ? class_basename($class) : 'E-Mail';
    }
}
