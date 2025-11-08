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
        return new Envelope(
            subject: 'Zahlung bestätigt - Ihre Tickets für ' . $this->booking->event->title,
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
        $ticketPdfService = app(TicketPdfService::class);

        return [
            // Ticket-PDF anhängen
            Attachment::fromData(
                fn () => $ticketPdfService->getTicketContent($this->booking),
                "Ticket_{$this->booking->booking_number}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}

