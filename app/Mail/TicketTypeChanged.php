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

/**
 * Der Veranstalter hat die Ticketart einzelner Positionen geändert (z. B. Fehlbuchung).
 * Enthält die Änderungen, die Begründung und – sofern freigegeben – die neu ausgestellten Tickets.
 */
class TicketTypeChanged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  list<array<string, mixed>>  $changes
     */
    public function __construct(
        public Booking $booking,
        public array $changes,
        public string $reason,
        public bool $priceLocked,
        public string $oldTotal,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticketart geändert - '.$this->booking->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.ticket-type-changed',
            with: [
                'totalChanged' => $this->totalChanged(),
                'invoiceAttached' => $this->attachesInvoice(),
            ],
        );
    }

    protected function totalChanged(): bool
    {
        return ! $this->priceLocked && (float) $this->oldTotal !== (float) $this->booking->total;
    }

    /**
     * Korrigierte Rechnung anhängen, wenn sich der Betrag einer bereits fakturierten, offenen Buchung geändert hat.
     * (Aus kostenfrei wird kostenpflichtig: Die Rechnung kommt mit der separaten Buchungsbestätigung.)
     */
    protected function attachesInvoice(): bool
    {
        return $this->totalChanged()
            && (float) $this->oldTotal > 0
            && ! $this->booking->isFree()
            && $this->booking->payment_status === 'pending'
            && filled($this->booking->invoice_number)
            && ! ($this->booking->event?->organization?->hasExternalInvoicing() ?? false);
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->attachesInvoice()) {
            $attachments[] = Attachment::fromData(
                fn () => app(InvoiceService::class)->getInvoiceOutput($this->booking),
                "Rechnung_{$this->booking->invoice_number}.pdf"
            )->withMime('application/pdf');
        }

        if ($this->booking->hasTicketDocument() && $this->booking->canSendTickets()) {
            $attachments[] = Attachment::fromData(
                fn () => app(TicketPdfService::class)->getAllIndividualTicketsContent($this->booking),
                "Tickets_{$this->booking->booking_number}.pdf"
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
