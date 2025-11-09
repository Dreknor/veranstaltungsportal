<?php

namespace App\Mail;

use App\Models\EventWaitlist;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WaitlistConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $waitlistEntry;

    public function __construct(EventWaitlist $waitlistEntry)
    {
        $this->waitlistEntry = $waitlistEntry;
    }

    public function build()
    {
        return $this->subject('Wartelisten-Bestätigung: ' . $this->waitlistEntry->event->title)
                    ->markdown('emails.waitlist.confirmation');
    }
}

