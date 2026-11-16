<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use App\Mail\NewsletterMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNewsletterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:send
                            {--type=weekly : Newsletter type (weekly, monthly)}
                            {--test : Send test newsletter to admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send newsletter to subscribed users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $isTest = $this->option('test');

        $this->info("Newsletter ({$type}) wird vorbereitet ...");

        // Get upcoming events
        $upcomingEvents = Event::published()
            ->where('start_date', '>', now())
            ->where('start_date', '<', now()->addDays(30))
            ->orderBy('start_date')
            ->limit(10)
            ->get();

        if ($upcomingEvents->isEmpty()) {
            $this->warn('Keine kommenden Veranstaltungen gefunden. Der Newsletter wurde nicht versendet.');
            return 0;
        }

        // Get featured events
        $featuredEvents = Event::published()
            ->where('is_featured', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Get subscribers
        if ($isTest) {
            $subscribers = User::role('admin')->get();
            $this->info('Test-Newsletter geht an die Administratoren ...');
        } else {
            $subscribers = User::where('newsletter_subscribed', true)->get();
            $this->info("{$subscribers->count()} registrierte Abonnenten gefunden");
        }

        $sent = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
            try {
                // Get personalized recommendations
                $recommendations = $subscriber->getRecommendedEvents(5);

                Mail::to($subscriber->email)->send(new NewsletterMail(
                    $subscriber,
                    $upcomingEvents,
                    $featuredEvents,
                    $recommendations,
                    $type
                ));

                $sent++;
                $this->line("Gesendet an: {$subscriber->email}");
            } catch (\Exception $e) {
                $failed++;
                $this->error("Versand an {$subscriber->email} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Newsletter-Versand abgeschlossen.");
        $this->table(
            ['Status', 'Anzahl'],
            [
                ['Gesendet', $sent],
                ['Fehlgeschlagen', $failed],
                ['Gesamt', $sent + $failed],
            ]
        );

        return 0;
    }
}

