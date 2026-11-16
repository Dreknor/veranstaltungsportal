<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * Teilnahmezertifikat als PDF-Anhang an die buchende Person.
 */
class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ihr Teilnahmezertifikat: '.$this->booking->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificates.certificate',
            with: [
                'event' => $this->booking->event,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $path = $this->booking->certificate_path;

        if (! $path || ! Storage::exists($path)) {
            return [];
        }

        return [
            Attachment::fromStorage($path)
                ->as("Teilnahmezertifikat_{$this->booking->booking_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
