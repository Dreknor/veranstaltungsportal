<?php

namespace App\Mail;

use App\Models\EventWaitlist;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WaitlistTicketAvailable extends Mailable
{
    use Queueable, SerializesModels;

    public $waitlistEntry;

    public function __construct(EventWaitlist $waitlistEntry)
    {
        $this->waitlistEntry = $waitlistEntry;
    }

    public function build()
    {
        return $this->subject('Tickets verfügbar: ' . $this->waitlistEntry->event->title)
                    ->markdown('emails.waitlist.ticket-available');
    }
}
