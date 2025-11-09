<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Buchung storniert für ' . $this->booking->event->title)
            ->greeting('Hallo ' . $notifiable->fullName() . ',')
            ->line('Eine Buchung für Ihre Veranstaltung wurde storniert.')
            ->line('**Veranstaltung:** ' . $this->booking->event->title)
            ->line('**Buchungsnummer:** ' . $this->booking->booking_number)
            ->line('**Teilnehmer:** ' . $this->booking->customer_name)
            ->line('**Anzahl Tickets:** ' . $this->booking->items->sum('quantity'))
            ->line('**Erstattungsbetrag:** ' . number_format($this->booking->total, 2, ',', '.') . ' €')
            ->action('Buchungsdetails ansehen', route('organizer.bookings.show', $this->booking))
            ->line('Bitte prüfen Sie, ob eine Rückerstattung erforderlich ist.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Buchung storniert',
            'message' => $this->booking->customer_name . ' hat die Buchung für "' . $this->booking->event->title . '" storniert (' . $this->booking->items->sum('quantity') . ' Ticket(s))',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'event_id' => $this->booking->event->id,
            'event_title' => $this->booking->event->title,
            'customer_name' => $this->booking->customer_name,
            'total_amount' => $this->booking->total,
            'url' => route('organizer.bookings.show', $this->booking),
        ];
    }
}

