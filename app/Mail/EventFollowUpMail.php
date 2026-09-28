<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Services\CertificateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Nachbereitung nach der Veranstaltung: Dank, Teilnahmebescheinigung(en) und Bitte um Feedback.
 *
 * - An den Bucher: Bescheinigungen aller eingecheckten Teilnehmenden + Feedback-Link
 * - An eingetragene Teilnehmende mit eigener Adresse ($item): nur die eigene Bescheinigung
 */
class EventFollowUpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public ?BookingItem $item = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Danke für Ihre Teilnahme: ' . $this->booking->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.events.follow-up',
            with: [
                'event' => $this->booking->event,
                'certificateItems' => $this->certificateItems(),
                'recipientName' => $this->item?->participantName() ?? $this->booking->customer_name,
                'isAttendee' => $this->item !== null,
                'canGiveFeedback' => $this->item === null && !$this->booking->review()->exists(),
            ],
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, BookingItem>
     */
    protected function certificateItems()
    {
        $items = $this->item ? collect([$this->item]) : $this->booking->items;

        return $items->filter(fn (BookingItem $item) => $item->checked_in)->values();
    }

    public function attachments(): array
    {
        $service = app(CertificateService::class);

        return $this->certificateItems()
            ->map(fn (BookingItem $item) => Attachment::fromData(
                fn () => $service->generateIndividualCertificate($item)->output(),
                'Teilnahmebescheinigung_' . \Illuminate\Support\Str::slug($item->participantName()) . '.pdf'
            )->withMime('application/pdf'))
            ->all();
    }
}
