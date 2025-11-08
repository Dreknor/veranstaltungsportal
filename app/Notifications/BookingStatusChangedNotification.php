<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Booking $booking, string $oldStatus, string $newStatus)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $statusLabels = [
            'pending' => 'Ausstehend',
            'confirmed' => 'Bestätigt',
            'cancelled' => 'Storniert',
            'completed' => 'Abgeschlossen',
        ];

        $subject = 'Buchungsstatus geändert - ' . $this->booking->booking_number;
        $oldStatusLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hallo ' . $this->booking->customer_name . ',')
            ->line('Der Status Ihrer Buchung wurde geändert.')
            ->line('Event: ' . $this->booking->event->title)
            ->line('Buchungsnummer: ' . $this->booking->booking_number)
            ->line('Alter Status: ' . $oldStatusLabel)
            ->line('Neuer Status: ' . $newStatusLabel);

        // Zusätzliche Informationen basierend auf dem neuen Status
        if ($this->newStatus === 'confirmed') {
            $mail->line('Ihre Buchung wurde erfolgreich bestätigt.')
                ->line('Event-Datum: ' . $this->booking->event->start_date->format('d.m.Y H:i'));
        } elseif ($this->newStatus === 'cancelled') {
            $mail->line('Ihre Buchung wurde storniert.')
                ->line('Falls Sie dies nicht veranlasst haben, kontaktieren Sie uns bitte.');
        } elseif ($this->newStatus === 'completed') {
            $mail->line('Ihre Buchung wurde als abgeschlossen markiert.')
                ->line('Vielen Dank für Ihre Teilnahme!');
        }

        $mail->action('Buchung ansehen', route('bookings.show', $this->booking->booking_number))
            ->line('Bei Fragen stehen wir Ihnen gerne zur Verfügung.');

        return $mail;
    }

    public function toArray($notifiable)
    {
        $statusLabels = [
            'pending' => 'Ausstehend',
            'confirmed' => 'Bestätigt',
            'cancelled' => 'Storniert',
            'completed' => 'Abgeschlossen',
        ];

        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        return [
            'title' => 'Buchungsstatus geändert',
            'message' => 'Der Status Ihrer Buchung "' . $this->booking->event->title . '" wurde zu "' . $newStatusLabel . '" geändert.',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'url' => route('bookings.show', $this->booking->booking_number),
        ];
    }
}
