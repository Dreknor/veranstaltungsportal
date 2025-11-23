<?php

namespace App\Jobs;

use App\Mail\EventReminderMail;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEventReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->booking->customer_email)
            ->send(new EventReminderMail($this->booking->event, $this->booking));
    }
}

