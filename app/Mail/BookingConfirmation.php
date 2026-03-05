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
        $isFreeBooking = $this->booking->total == 0;

        $attachments = [];

        // Rechnung nur bei kostenpflichtigen Buchungen anhängen
        if (!$isFreeBooking) {
            // Sicherstellen, dass eine veranstalterspezifische Rechnungsnummer existiert (einmalig je Buchung)
            if (empty($this->booking->invoice_number)) {
                $invoiceNumberService = app(InvoiceNumberService::class);
                $organizer = $this->booking->event->getUser();

                if ($organizer) {
                    $newInvoiceNumber = $invoiceNumberService->generateBookingInvoiceNumber($organizer);
                    $this->booking->forceFill([
                        'invoice_number' => $newInvoiceNumber,
                        'invoice_date' => now(),
                    ])->save();
                }
            }

            $invoiceNumber = $this->booking->invoice_number;
            $invoiceService = app(InvoiceService::class);

            $attachments[] = Attachment::fromData(
                fn () => $invoiceService->getInvoiceOutput($this->booking),
                "Rechnung_{$invoiceNumber}.pdf"
            )->withMime('application/pdf');
        } else {
            // Kostenfreie Buchung: sicherstellen, dass keine Rechnungsnummer gesetzt ist
            if (!empty($this->booking->invoice_number)) {
                $this->booking->forceFill([
                    'invoice_number' => null,
                    'invoice_date'   => null,
                ])->save();
            }
        }

        // Ticket-PDF nur anhängen, wenn:
        // - Event Tickets erfordert (requires_ticket)
        // - Buchung bezahlt ist ODER kostenlose Buchung
        // - Event NICHT rein online ist
        // - Personalisierung abgeschlossen ist
        if ($this->booking->event->requires_ticket
            && ($this->booking->payment_status === 'paid' || $isFreeBooking)
            && !$this->booking->event->isOnline()
            && $this->booking->canSendTickets()) {
            $ticketPdfService = app(TicketPdfService::class);
            $attachments[] = Attachment::fromData(
                fn () => $ticketPdfService->getAllIndividualTicketsContent($this->booking),
                "Tickets_{$this->booking->booking_number}.pdf"
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
