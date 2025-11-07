<?php
namespace App\Mail;
use App\Models\Booking;
use App\Services\InvoiceService;
use App\Services\TicketPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct(
        public Booking $booking
    ) {}
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Buchungsbestätigung - ' . $this->booking->event->title,
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.confirmation',
        );
    }
    public function attachments(): array
    {
        $invoiceService = app(InvoiceService::class);
        $ticketPdfService = app(TicketPdfService::class);

        $invoiceNumber = $invoiceService->generateInvoiceNumber($this->booking);

        return [
            // Rechnung anhängen
            Attachment::fromData(
                fn () => $invoiceService->getInvoiceOutput($this->booking),
                "Rechnung_{$invoiceNumber}.pdf"
            )->withMime('application/pdf'),

            // Ticket-PDF anhängen
            Attachment::fromData(
                fn () => $ticketPdfService->getTicketContent($this->booking),
                "Ticket_{$this->booking->booking_number}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
