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
        $organization = $this->booking->event?->organization;
        $isExternalInvoicing = $organization?->hasExternalInvoicing() ?? false;

        $attachments = [];

        // Rechnung nur bei kostenpflichtigen Buchungen UND automatischer Rechnungsstellung anhängen
        if (!$isFreeBooking && !$isExternalInvoicing) {
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
        } elseif ($isFreeBooking) {
            // Kostenfreie Buchung: sicherstellen, dass keine Rechnungsnummer gesetzt ist
            if (!empty($this->booking->invoice_number)) {
                $this->booking->forceFill([
                    'invoice_number' => null,
                    'invoice_date'   => null,
                ])->save();
            }
        }
        // Bei externer Rechnungsstellung: kein Rechnungs-PDF, keine Rechnungsnummer generieren

        // Ticket-PDF nur anhängen, wenn:
        // - die Veranstaltung ein Ticket vorsieht (Präsenz/Hybrid mit Ticketpflicht)
        // - die Buchung bestätigt und bezahlt ist (kostenfrei/extern fakturiert zählt als bezahlt)
        // - die Personalisierung (bei mehreren Tickets) abgeschlossen ist
        if ($this->booking->hasTicketDocument()
            && $this->booking->isReadyForParticipation()
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
