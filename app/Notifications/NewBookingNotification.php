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
            ->view('emails.bookings.new-booking-notification', [
                'booking' => $this->booking,
                'organizer' => $notifiable,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Neue Buchung',
            'message' => $this->booking->customer_name . ' hat ' . $this->booking->items->sum('quantity') . ' Ticket(s) für "' . $this->booking->event->title . '" gebucht',
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

