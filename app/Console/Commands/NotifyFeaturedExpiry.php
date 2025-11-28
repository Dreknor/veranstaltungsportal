<?php

namespace App\Console\Commands;

use App\Mail\FeaturedExpiryReminder;
use App\Models\FeaturedEventFee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotifyFeaturedExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'featured:notify-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails 3 days before featured event period expires';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threeDaysFromNow = Carbon::now()->addDays(3)->startOfDay();
        $threeDaysFromNowEnd = Carbon::now()->addDays(3)->endOfDay();

        // Find featured fees expiring in 3 days that are paid and haven't been notified yet
        $expiringFees = FeaturedEventFee::where('payment_status', 'paid')
            ->whereBetween('featured_end_date', [$threeDaysFromNow, $threeDaysFromNowEnd])
            ->whereNull('expiry_notified_at') // Only notify once
            ->with(['event', 'user'])
            ->get();

        if ($expiringFees->isEmpty()) {
            $this->info('No featured events expiring in 3 days.');
            return 0;
        }

        $count = 0;
        foreach ($expiringFees as $fee) {
            try {
                // Send email to the user who created the featured request
                Mail::to($fee->user->email)->send(new FeaturedExpiryReminder($fee));

                // Mark as notified
                $fee->update(['expiry_notified_at' => now()]);

                $count++;
                $this->info("Sent expiry reminder for event: {$fee->event->title}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for event {$fee->event->title}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$count} featured event expiry reminders.");
        return 0;
    }
}

