<?php

namespace App\Mail;

use App\Models\FeaturedEventFee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeaturedExpiryReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public FeaturedEventFee $featuredEventFee;

    /**
     * Create a new message instance.
     */
    public function __construct(FeaturedEventFee $featuredEventFee)
    {
        $this->featuredEventFee = $featuredEventFee;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Featured-Status läuft bald ab: ' . $this->featuredEventFee->event->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.featured.expiry-reminder',
            with: [
                'fee' => $this->featuredEventFee,
                'event' => $this->featuredEventFee->event,
                'user' => $this->featuredEventFee->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

}

