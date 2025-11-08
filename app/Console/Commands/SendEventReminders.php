<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders {--hours=24 : Hours before event to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications to users with upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');

        $this->info("Searching for events starting in {$hours} hours...");

        // Find events that start in approximately X hours
        $targetTime = now()->addHours($hours);
        $startWindow = $targetTime->copy()->subMinutes(30);
        $endWindow = $targetTime->copy()->addMinutes(30);

        $events = Event::query()
            ->where('is_published', true)
            ->whereBetween('start_date', [$startWindow, $endWindow])
            ->with('bookings.user')
            ->get();

        $this->info("Found {$events->count()} events");

        $sentCount = 0;

        foreach ($events as $event) {
            $this->info("Processing event: {$event->title}");

            foreach ($event->bookings()->where('status', 'confirmed')->get() as $booking) {
                if (!$booking->user) {
                    continue;
                }

                // Check if user wants email reminders
                $preferences = $booking->user->notification_preferences ?? [];
                if (isset($preferences['email_event_reminder']) && !$preferences['email_event_reminder']) {
                    continue;
                }

                try {
                    $booking->user->notify(new EventReminderNotification($event, $booking));
                    $sentCount++;
                    $this->line("  ✓ Sent reminder to {$booking->customer_email}");
                } catch (\Exception $e) {
                    $this->error("  ✗ Failed to send to {$booking->customer_email}: {$e->getMessage()}");
                    Log::error("Event reminder failed for booking {$booking->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Sent {$sentCount} event reminders");

        return Command::SUCCESS;
    }
}
