<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Erinnerung vor Veranstaltungsbeginn.
 *
 * Enthält bei Online-/Hybrid-Veranstaltungen die Zugangsdaten (sofern die Buchung bestätigt und bezahlt ist),
 * damit Teilnehmende den Link kurz vor Beginn griffbereit haben.
 */
class EventReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  string|null  $recipientName  Name des Empfängers (z. B. personalisierter Teilnehmer)
     * @param  bool  $isAttendee  true, wenn der Empfänger nicht der Bucher ist (kein Zugriff auf die Buchungsverwaltung)
     */
    public function __construct(
        public Event $event,
        public Booking $booking,
        public ?string $recipientName = null,
        public bool $isAttendee = false,
        public ?\App\Models\EventDate $session = null,
    ) {}

    public function envelope(): Envelope
    {
        $start = $this->session?->start_date ?? $this->event->start_date;
        $when = $start->isToday() ? 'heute' : 'am ' . $start->format('d.m.Y');

        return new Envelope(
            subject: "Erinnerung: {$this->event->title} beginnt {$when} um {$start->format('H:i')} Uhr",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.event-reminder',
            with: [
                'event' => $this->event,
                'booking' => $this->booking,
                'recipientName' => $this->recipientName ?? $this->booking->customer_name,
                'isAttendee' => $this->isAttendee,
                'session' => $this->session,
                'showOnlineAccess' => $this->booking->canAccessOnlineContent() && $this->event->online_url,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
