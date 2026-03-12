<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingApprovalRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Neue Anmeldung wartet auf Bestätigung – ' . $this->booking->event->title)
            ->greeting('Neue Anmeldung!')
            ->line($this->booking->customer_name . ' hat sich für „' . $this->booking->event->title . '" angemeldet.')
            ->line('Die Anmeldung wartet auf Ihre Bestätigung.')
            ->action('Anmeldung prüfen', route('organizer.bookings.show', $this->booking))
            ->line('Bitte bestätigen oder lehnen Sie die Anmeldung zeitnah ab.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Anmeldung wartet auf Bestätigung',
            'message' => $this->booking->customer_name . ' hat sich für „' . $this->booking->event->title . '" angemeldet und wartet auf Bestätigung.',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'event_id' => $this->booking->event->id,
            'event_title' => $this->booking->event->title,
            'customer_name' => $this->booking->customer_name,
            'type' => 'booking_approval_required',
        ];
    }
}

