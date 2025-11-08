<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification implements ShouldQueue
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
            ->subject('Neue Buchung für ' . $this->booking->event->title)
            ->greeting('Hallo ' . $notifiable->fullName() . ',')
            ->line('Sie haben eine neue Buchung für Ihre Veranstaltung erhalten!')
            ->line('**Veranstaltung:** ' . $this->booking->event->title)
            ->line('**Buchungsnummer:** ' . $this->booking->booking_number)
            ->line('**Teilnehmer:** ' . $this->booking->first_name . ' ' . $this->booking->last_name)
            ->line('**E-Mail:** ' . $this->booking->email)
            ->line('**Anzahl Tickets:** ' . $this->booking->items->sum('quantity'))
            ->line('**Gesamtbetrag:** ' . number_format($this->booking->total_amount, 2, ',', '.') . ' €')
            ->action('Buchung ansehen', route('organizer.bookings.show', $this->booking))
            ->line('Vielen Dank für die Nutzung unserer Plattform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'event_id' => $this->booking->event->id,
            'event_title' => $this->booking->event->title,
            'customer_name' => $this->booking->first_name . ' ' . $this->booking->last_name,
            'total_amount' => $this->booking->total_amount,
            'message' => 'Neue Buchung für ' . $this->booking->event->title,
        ];
    }
}

