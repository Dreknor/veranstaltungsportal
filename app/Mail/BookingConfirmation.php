<?php
namespace App\Mail;
use App\Models\Booking;
use App\Services\InvoiceService;
use App\Services\InvoiceNumberService;
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
        // Sicherstellen, dass eine veranstalterspezifische Rechnungsnummer existiert (einmalig je Buchung)
        if (empty($this->booking->invoice_number)) {
            $invoiceNumberService = app(InvoiceNumberService::class);
            $organizer = $this->booking->event->user; // Veranstalter
            $newInvoiceNumber = $invoiceNumberService->generateBookingInvoiceNumber($organizer);
            $this->booking->forceFill([
                'invoice_number' => $newInvoiceNumber,
                'invoice_date' => now(),
            ])->save();
        }

        $invoiceNumber = $this->booking->invoice_number;

        $invoiceService = app(InvoiceService::class);

        $attachments = [
            // Rechnung anhängen (immer)
            Attachment::fromData(
                fn () => $invoiceService->getInvoiceOutput($this->booking),
                "Rechnung_{$invoiceNumber}.pdf"
            )->withMime('application/pdf'),
        ];

        // Ticket-PDF nur anhängen, wenn Buchung bezahlt ist UND Event NICHT rein online ist
        if ($this->booking->payment_status === 'paid' && !$this->booking->event->isOnline()) {
            $ticketPdfService = app(TicketPdfService::class);
            $attachments[] = Attachment::fromData(
                fn () => $ticketPdfService->getTicketContent($this->booking),
                "Ticket_{$this->booking->booking_number}.pdf"
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
