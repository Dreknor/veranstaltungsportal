<?php

namespace App\Mail;

use App\Models\Booking;
use App\Services\TicketPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking
    ) {}

    public function envelope(): Envelope
    {
        $what = $this->booking->hasTicketDocument() ? 'Ihre Tickets' : 'Ihre Zugangsdaten';
        $prefix = ((float) $this->booking->total > 0 && $this->booking->payment_status === 'paid')
            ? 'Zahlung bestätigt'
            : 'Buchung bestätigt';

        return new Envelope(
            subject: "{$prefix} - {$what} für " . $this->booking->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.payment-confirmed',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        // Nur Tickets anhängen, wenn sie personalisiert sind (bei mehreren Tickets) oder nur ein Ticket vorhanden ist
        if ($this->booking->hasTicketDocument() && $this->booking->canSendTickets()) {
            $ticketPdfService = app(TicketPdfService::class);

            // Generiere individuelle Tickets für alle BookingItems
            $attachments[] = Attachment::fromData(
                fn () => $ticketPdfService->getAllIndividualTicketsContent($this->booking),
                "Tickets_{$this->booking->booking_number}.pdf"
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}

