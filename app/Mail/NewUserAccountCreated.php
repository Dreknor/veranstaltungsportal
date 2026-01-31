<?php

namespace App\Mail;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $temporaryPassword,
        public Organization $organization,
        public User $invitedBy,
        public string $role,
        public string $cancellationToken
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Ihr neues Konto für {$this->organization->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-user-account-created',
        );
    }
}
