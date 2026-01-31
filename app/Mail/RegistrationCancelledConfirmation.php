<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationCancelledConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Registrierung erfolgreich rückgängig gemacht",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.registration-cancelled-confirmation',
        );
    }
}
