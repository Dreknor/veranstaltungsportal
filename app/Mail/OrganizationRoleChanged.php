<?php

namespace App\Mail;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrganizationRoleChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Organization $organization,
        public User $changedBy,
        public string $oldRole,
        public string $newRole
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Ihre Rolle wurde geändert - {$this->organization->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.organization-role-changed',
        );
    }
}

