<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterMail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    /**
     * Display newsletter management page
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_subscribers' => User::where('newsletter_subscribed', true)->count(),
            'guest_subscribers' => DB::table('newsletter_subscribers')->count(),
            'total_users' => User::count(),
            'subscription_rate' => User::count() > 0
                ? round((User::where('newsletter_subscribed', true)->count() / User::count()) * 100, 2)
                : 0,
        ];

        // Get recent newsletters (from logs or database if you track them)
        $recentNewsletters = []; // Could be implemented with a newsletters_sent table

        // Get upcoming events for preview
        $upcomingEvents = Event::published()
            ->where('start_date', '>', now())
            ->where('start_date', '<', now()->addDays(30))
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        $featuredEvents = Event::published()
            ->where('is_featured', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        return view('admin.newsletter.index', compact('stats', 'upcomingEvents', 'featuredEvents', 'recentNewsletters'));
    }

    /**
     * Show newsletter compose/preview page
     */
    public function compose(Request $request)
    {
        $type = $request->get('type', 'weekly');

        // Get preview data
        $upcomingEvents = Event::published()
            ->where('start_date', '>', now())
            ->where('start_date', '<', now()->addDays(30))
            ->orderBy('start_date')
            ->limit(10)
            ->get();

        $featuredEvents = Event::published()
            ->where('is_featured', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Get a sample user for preview
        $sampleUser = User::where('newsletter_subscribed', true)->first()
            ?? User::first()
            ?? new User(['name' => 'Max Mustermann', 'email' => 'max@example.com']);

        $recommendations = $sampleUser->getRecommendedEvents(5);

        return view('admin.newsletter.compose', compact('type', 'upcomingEvents', 'featuredEvents', 'recommendations', 'sampleUser'));
    }

    /**
     * Preview newsletter email
     */
    public function preview(Request $request)
    {
        $type = $request->get('type', 'weekly');

        $upcomingEvents = Event::published()
            ->where('start_date', '>', now())
            ->where('start_date', '<', now()->addDays(30))
            ->orderBy('start_date')
            ->limit(10)
            ->get();

        $featuredEvents = Event::published()
            ->where('is_featured', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        $sampleUser = User::where('newsletter_subscribed', true)->first()
            ?? User::first()
            ?? new User(['name' => 'Max Mustermann', 'email' => 'max@example.com']);

        $recommendations = $sampleUser->getRecommendedEvents(5);

        // Return the email view directly for preview
        return view('emails.newsletter', [
            'subscriber' => $sampleUser,
            'upcomingEvents' => $upcomingEvents,
            'featuredEvents' => $featuredEvents,
            'recommendations' => $recommendations,
            'type' => $type,
        ]);
    }

    /**
     * Send newsletter
     */
    public function send(Request $request)
    {
        $request->validate([
            'type' => 'required|in:weekly,monthly',
            'send_to' => 'required|in:all,test',
        ]);

        $type = $request->type;
        $sendTo = $request->send_to;

        try {
            if ($sendTo === 'test') {
                // Send test newsletter to current admin
                $exitCode = Artisan::call('newsletter:send', [
                    '--type' => $type,
                    '--test' => true,
                ]);

                $message = 'Test-Newsletter wurde an alle Admins versendet!';
            } else {
                // Send to all subscribers
                $exitCode = Artisan::call('newsletter:send', [
                    '--type' => $type,
                ]);

                $subscriberCount = User::where('newsletter_subscribed', true)->count();
                $message = "Newsletter wurde an {$subscriberCount} Abonnenten versendet!";
            }

            // Get command output
            $output = Artisan::output();

            return redirect()->route('admin.newsletter.index')
                ->with('success', $message)
                ->with('output', $output);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Fehler beim Versand: ' . $e->getMessage());
        }
    }

    /**
     * Show subscribers list
     */
    public function subscribers()
    {
        $subscribers = User::where('newsletter_subscribed', true)
            ->orderBy('newsletter_subscribed_at', 'desc')
            ->paginate(50);

        $guestSubscribers = DB::table('newsletter_subscribers')
            ->orderBy('subscribed_at', 'desc')
            ->paginate(50, ['*'], 'guests_page');

        return view('admin.newsletter.subscribers', compact('subscribers', 'guestSubscribers'));
    }

    /**
     * Export subscribers
     */
    public function export()
    {
        $subscribers = User::where('newsletter_subscribed', true)
            ->select('email', 'first_name', 'last_name', 'newsletter_subscribed_at')
            ->get();

        $guestSubscribers = DB::table('newsletter_subscribers')
            ->select('email', 'subscribed_at')
            ->get();

        $filename = 'newsletter_subscribers_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($subscribers, $guestSubscribers) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, ['E-Mail', 'Vorname', 'Nachname', 'Typ', 'Abonniert seit'], ';');

            // Registered users
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->first_name ?? '',
                    $subscriber->last_name ?? '',
                    'Registriert',
                    $subscriber->newsletter_subscribed_at ? $subscriber->newsletter_subscribed_at->format('d.m.Y H:i') : '',
                ], ';');
            }

            // Guest subscribers
            foreach ($guestSubscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    '',
                    '',
                    'Gast',
                    $subscriber->subscribed_at ? date('d.m.Y H:i', strtotime($subscriber->subscribed_at)) : '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

