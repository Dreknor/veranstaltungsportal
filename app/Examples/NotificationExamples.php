<?php

/**
 * Beispiele für die Verwendung der Buchungs- und Zahlungsstatus-Benachrichtigungen
 *
 * Diese Datei zeigt verschiedene Szenarien, in denen die automatischen Benachrichtigungen
 * ausgelöst werden. Sie ist nur zur Dokumentation gedacht und sollte nicht in die
 * Produktionsumgebung eingefügt werden.
 */

namespace App\Examples;

use App\Models\Booking;
use App\Notifications\BookingStatusChangedNotification;
use App\Notifications\PaymentStatusChangedNotification;
use Illuminate\Support\Facades\Notification;

class NotificationExamples
{
    /**
     * Beispiel 1: Buchung bestätigen
     * Die Benachrichtigung wird automatisch gesendet
     */
    public function confirmBooking(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Status ändern - Benachrichtigung wird automatisch gesendet
        $booking->status = 'confirmed';
        $booking->confirmed_at = now();
        $booking->save();

        // Der BookingObserver erkennt die Änderung und sendet die Benachrichtigung
    }

    /**
     * Beispiel 2: Zahlung als bezahlt markieren
     * Die Benachrichtigung wird automatisch gesendet
     */
    public function markAsPaid(int $bookingId, string $transactionId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Zahlungsstatus ändern - Benachrichtigung wird automatisch gesendet
        $booking->payment_status = 'paid';
        $booking->payment_transaction_id = $transactionId;
        $booking->save();

        // Der BookingObserver erkennt die Änderung und sendet die Benachrichtigung
    }

    /**
     * Beispiel 3: Buchung stornieren
     * Die Benachrichtigung wird automatisch gesendet
     */
    public function cancelBooking(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Status ändern - Benachrichtigung wird automatisch gesendet
        $booking->status = 'cancelled';
        $booking->cancelled_at = now();
        $booking->save();
    }

    /**
     * Beispiel 4: Zahlung erstatten
     * Die Benachrichtigung wird automatisch gesendet
     */
    public function refundPayment(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Zahlungsstatus ändern - Benachrichtigung wird automatisch gesendet
        $booking->payment_status = 'refunded';
        $booking->save();
    }

    /**
     * Beispiel 5: Mehrere Änderungen gleichzeitig
     * Beide Benachrichtigungen werden gesendet
     */
    public function confirmAndMarkAsPaid(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        // Beide Änderungen in einem Speichervorgang
        $booking->status = 'confirmed';
        $booking->payment_status = 'paid';
        $booking->confirmed_at = now();
        $booking->save();

        // Der Observer sendet beide Benachrichtigungen:
        // - BookingStatusChangedNotification
        // - PaymentStatusChangedNotification
    }

    /**
     * Beispiel 6: Zahlung fehlgeschlagen
     * Die Benachrichtigung wird automatisch gesendet
     */
    public function markPaymentAsFailed(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->payment_status = 'failed';
        $booking->save();

        // Kunde erhält eine Benachrichtigung mit Hinweisen zur Fehlerbehebung
    }

    /**
     * Beispiel 7: Manuelle Benachrichtigung senden (falls nötig)
     * Normalerweise nicht erforderlich, da der Observer dies automatisch macht
     */
    public function sendManualNotification(int $bookingId)
    {
        $booking = Booking::with(['user', 'event'])->findOrFail($bookingId);

        // Manuelle Benachrichtigung an registrierten Benutzer
        if ($booking->user) {
            $booking->user->notify(
                new BookingStatusChangedNotification($booking, 'pending', 'confirmed')
            );
        }

        // Manuelle Benachrichtigung an Gast-E-Mail
        Notification::route('mail', $booking->customer_email)
            ->notify(new BookingStatusChangedNotification($booking, 'pending', 'confirmed'));
    }

    /**
     * Beispiel 8: Benachrichtigungen für Bulk-Operationen
     */
    public function confirmMultipleBookings(array $bookingIds)
    {
        foreach ($bookingIds as $bookingId) {
            $booking = Booking::findOrFail($bookingId);

            // Jede Änderung sendet eine individuelle Benachrichtigung
            $booking->status = 'confirmed';
            $booking->confirmed_at = now();
            $booking->save();
        }
    }

    /**
     * Beispiel 9: Event abgeschlossen markieren
     */
    public function completeBooking(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->status = 'completed';
        $booking->save();

        // Kunde erhält eine Dankesnachricht
    }

    /**
     * Beispiel 10: Teilweise Rückerstattung
     */
    public function partialRefund(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->payment_status = 'partially_refunded';
        $booking->save();

        // Kunde wird über die teilweise Rückerstattung informiert
    }

    /**
     * Beispiel 11: Benachrichtigungen im Admin-Panel anzeigen
     */
    public function getUserNotifications()
    {
        $user = auth()->user();

        // Alle ungelesenen Benachrichtigungen
        $unreadNotifications = $user->unreadNotifications;

        // Alle Benachrichtigungen
        $allNotifications = $user->notifications;

        // Nur Buchungsstatus-Benachrichtigungen
        $bookingStatusNotifications = $user->notifications()
            ->where('type', BookingStatusChangedNotification::class)
            ->get();

        // Nur Zahlungsstatus-Benachrichtigungen
        $paymentStatusNotifications = $user->notifications()
            ->where('type', PaymentStatusChangedNotification::class)
            ->get();

        // Benachrichtigung als gelesen markieren
        $notification = $user->unreadNotifications->first();
        if ($notification) {
            $notification->markAsRead();
        }

        // Alle als gelesen markieren
        $user->unreadNotifications->markAsRead();

        return [
            'unread' => $unreadNotifications,
            'all' => $allNotifications,
            'booking_status' => $bookingStatusNotifications,
            'payment_status' => $paymentStatusNotifications,
        ];
    }

    /**
     * Beispiel 12: Webhook-Handler für Zahlungsanbieter
     * z.B. Stripe, PayPal, etc.
     */
    public function handlePaymentWebhook(array $webhookData)
    {
        // Beispiel: Stripe Webhook
        $transactionId = $webhookData['transaction_id'];
        $status = $webhookData['status'];

        $booking = Booking::where('payment_transaction_id', $transactionId)->first();

        if ($booking) {
            // Zahlungsstatus basierend auf Webhook aktualisieren
            if ($status === 'succeeded') {
                $booking->payment_status = 'paid';
            } elseif ($status === 'failed') {
                $booking->payment_status = 'failed';
            }

            $booking->save();

            // Benachrichtigung wird automatisch gesendet
        }
    }
}

