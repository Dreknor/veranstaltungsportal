<?php

namespace App\Mail;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrganizationInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Organization $organization,
        public User $invitedBy,
        public string $role
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Einladung zur Organisation: {$this->organization->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.organization-invitation',
        );
    }
}


