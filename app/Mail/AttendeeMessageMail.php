<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Freie Nachricht des Veranstalters an Buchende bzw. Teilnehmende einer Veranstaltung.
 * Wird über die Queue versendet und im E-Mail-Verlauf der Buchung protokolliert.
 */
class AttendeeMessageMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $messageSubject,
        public string $messageBody,
        public ?string $recipientName = null,
    ) {}

    public function envelope(): Envelope
    {
        $organizerEmail = $this->booking->event->getOrganizerEmail();

        return new Envelope(
            subject: $this->messageSubject,
            replyTo: $organizerEmail ? [new Address($organizerEmail, $this->booking->event->getOrganizerName())] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.events.attendee-message',
            with: [
                'event' => $this->booking->event,
                'recipientName' => $this->recipientName ?? $this->booking->customer_name,
            ],
        );
    }
}
