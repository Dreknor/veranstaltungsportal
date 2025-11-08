<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $oldPaymentStatus;
    protected $newPaymentStatus;

    public function __construct(Booking $booking, string $oldPaymentStatus, string $newPaymentStatus)
    {
        $this->booking = $booking;
        $this->oldPaymentStatus = $oldPaymentStatus;
        $this->newPaymentStatus = $newPaymentStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $paymentStatusLabels = [
            'pending' => 'Ausstehend',
            'paid' => 'Bezahlt',
            'failed' => 'Fehlgeschlagen',
            'refunded' => 'Erstattet',
            'partially_refunded' => 'Teilweise erstattet',
        ];

        $subject = 'Zahlungsstatus geändert - ' . $this->booking->booking_number;
        $oldPaymentStatusLabel = $paymentStatusLabels[$this->oldPaymentStatus] ?? $this->oldPaymentStatus;
        $newPaymentStatusLabel = $paymentStatusLabels[$this->newPaymentStatus] ?? $this->newPaymentStatus;

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hallo ' . $this->booking->customer_name . ',')
            ->line('Der Zahlungsstatus Ihrer Buchung wurde geändert.')
            ->line('Event: ' . $this->booking->event->title)
            ->line('Buchungsnummer: ' . $this->booking->booking_number)
            ->line('Alter Zahlungsstatus: ' . $oldPaymentStatusLabel)
            ->line('Neuer Zahlungsstatus: ' . $newPaymentStatusLabel);

        // Zusätzliche Informationen basierend auf dem neuen Zahlungsstatus
        if ($this->newPaymentStatus === 'paid') {
            $mail->line('✓ Ihre Zahlung wurde erfolgreich verarbeitet.')
                ->line('Betrag: ' . number_format($this->booking->total, 2, ',', '.') . ' €');

            if ($this->booking->payment_method) {
                $mail->line('Zahlungsmethode: ' . ucfirst($this->booking->payment_method));
            }

            if ($this->booking->payment_transaction_id) {
                $mail->line('Transaktions-ID: ' . $this->booking->payment_transaction_id);
            }

            $mail->line('Vielen Dank für Ihre Zahlung!');
        } elseif ($this->newPaymentStatus === 'failed') {
            $mail->line('⚠ Ihre Zahlung konnte leider nicht verarbeitet werden.')
                ->line('Bitte überprüfen Sie Ihre Zahlungsinformationen oder versuchen Sie eine andere Zahlungsmethode.')
                ->line('Bei weiteren Fragen kontaktieren Sie uns bitte.');
        } elseif ($this->newPaymentStatus === 'refunded') {
            $mail->line('Ihre Zahlung wurde vollständig erstattet.')
                ->line('Erstattungsbetrag: ' . number_format($this->booking->total, 2, ',', '.') . ' €')
                ->line('Die Rückerstattung erfolgt auf Ihr ursprüngliches Zahlungsmittel.');
        } elseif ($this->newPaymentStatus === 'partially_refunded') {
            $mail->line('Ihre Zahlung wurde teilweise erstattet.')
                ->line('Die Rückerstattung erfolgt auf Ihr ursprüngliches Zahlungsmittel.');
        }

        $mail->action('Buchung ansehen', route('bookings.show', $this->booking->booking_number));

        if ($this->newPaymentStatus !== 'paid') {
            $mail->line('Bei Fragen stehen wir Ihnen gerne zur Verfügung.');
        }

        return $mail;
    }

    public function toArray($notifiable)
    {
        $paymentStatusLabels = [
            'pending' => 'Ausstehend',
            'paid' => 'Bezahlt',
            'failed' => 'Fehlgeschlagen',
            'refunded' => 'Erstattet',
            'partially_refunded' => 'Teilweise erstattet',
        ];

        $newPaymentStatusLabel = $paymentStatusLabels[$this->newPaymentStatus] ?? $this->newPaymentStatus;

        return [
            'title' => 'Zahlungsstatus geändert',
            'message' => 'Der Zahlungsstatus Ihrer Buchung "' . $this->booking->event->title . '" wurde zu "' . $newPaymentStatusLabel . '" geändert.',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'old_payment_status' => $this->oldPaymentStatus,
            'new_payment_status' => $this->newPaymentStatus,
            'total_amount' => $this->booking->total,
            'url' => route('bookings.show', $this->booking->booking_number),
        ];
    }
}

