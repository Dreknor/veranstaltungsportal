<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Services\TicketPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Persönliches Ticket bzw. Zugangsdaten für eine eingetragene teilnehmende Person
 * (z. B. wenn eine Schulleitung für das Kollegium gebucht hat).
 */
class AttendeeTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public BookingItem $item,
    ) {}

    public function envelope(): Envelope
    {
        $what = $this->booking->hasTicketDocument() ? 'Ihr Ticket' : 'Ihre Zugangsdaten';

        return new Envelope(
            subject: "{$what}: " . $this->booking->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.attendee-ticket',
            with: [
                'event' => $this->booking->event,
                'showOnlineAccess' => $this->booking->canAccessOnlineContent() && $this->booking->event->online_url,
            ],
        );
    }

    public function attachments(): array
    {
        if (!$this->booking->hasTicketDocument()) {
            return [];
        }

        return [
            Attachment::fromData(
                fn () => app(TicketPdfService::class)->generateIndividualTicket($this->item)->output(),
                "Ticket_{$this->item->ticket_number}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
