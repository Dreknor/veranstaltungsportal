<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Buchungsbestätigung - ' . $this->booking->event->title)
            ->greeting('Hallo ' . $this->booking->customer_name . ',')
            ->line('Ihre Buchung wurde erfolgreich bestätigt.')
            ->line('Event: ' . $this->booking->event->title)
            ->line('Buchungsnummer: ' . $this->booking->booking_number)
            ->line('Datum: ' . $this->booking->event->start_date->format('d.m.Y H:i'))
            ->action('Buchung ansehen', route('bookings.show', $this->booking->booking_number))
            ->line('Vielen Dank für Ihre Buchung!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Buchung bestätigt',
            'message' => 'Ihre Buchung für "' . $this->booking->event->title . '" wurde bestätigt.',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'url' => route('bookings.show', $this->booking->booking_number),
        ];
    }
}

