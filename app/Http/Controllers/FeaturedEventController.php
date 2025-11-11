<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\FeaturedEventFee;
use App\Services\FeaturedEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FeaturedEventController extends Controller
{
    protected FeaturedEventService $featuredEventService;

    public function __construct(FeaturedEventService $featuredEventService)
    {
        $this->middleware('auth');
        $this->featuredEventService = $featuredEventService;
    }

    /**
     * Show the form to feature an event
     */
    public function create(Event $event)
    {
        $user = Auth::user();

        if (!$this->featuredEventService->canFeatureEvent($event, $user)) {
            return redirect()->back()->with('error', 'Sie können dieses Event nicht als Featured markieren.');
        }

        $pricing = $this->featuredEventService->getPricingInfo();
        $activeFee = $event->activeFeaturedFee();

        return view('events.featured.create', compact('event', 'pricing', 'activeFee'));
    }

    /**
     * Store a new featured event request
     */
    public function store(Request $request, Event $event)
    {
        $user = Auth::user();

        if (!$this->featuredEventService->canFeatureEvent($event, $user)) {
            return redirect()->back()->with('error', 'Sie können dieses Event nicht als Featured markieren.');
        }

        $validated = $request->validate([
            'duration_type' => 'required|in:daily,weekly,monthly,custom',
            'custom_days' => 'nullable|integer|min:1|max:' . config('monetization.featured_event_max_duration_days'),
            'start_date' => 'required|date|after_or_equal:today',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $customDays = $validated['duration_type'] === 'custom' ? $validated['custom_days'] : null;

        try {
            $featuredFee = $this->featuredEventService->createFeaturedRequest(
                $event,
                $user,
                $validated['duration_type'],
                $startDate,
                $customDays
            );

            return redirect()
                ->route('featured-events.payment', $featuredFee)
                ->with('success', 'Featured Event Antrag erstellt. Bitte schließen Sie die Zahlung ab.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Fehler beim Erstellen des Antrags: ' . $e->getMessage());
        }
    }

    /**
     * Show payment page for featured event fee
     */
    public function payment(FeaturedEventFee $featuredEventFee)
    {
        $user = Auth::user();

        if ($featuredEventFee->user_id !== $user->id) {
            abort(403);
        }

        return view('events.featured.payment', compact('featuredEventFee'));
    }

    /**
     * Process payment for featured event fee
     */
    public function processPayment(Request $request, FeaturedEventFee $featuredEventFee)
    {
        $user = Auth::user();

        if ($featuredEventFee->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:stripe,paypal,invoice,bank_transfer',
            'payment_reference' => 'nullable|string',
        ]);

        try {
            // Hier würde die eigentliche Zahlungsverarbeitung stattfinden
            // Für jetzt markieren wir es einfach als bezahlt
            $this->featuredEventService->markAsPaid(
                $featuredEventFee,
                $validated['payment_method'],
                $validated['payment_reference'] ?? null
            );

            return redirect()
                ->route('organizer.events.show', $featuredEventFee->event)
                ->with('success', 'Zahlung erfolgreich! Ihr Event ist jetzt als Featured markiert.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Fehler bei der Zahlungsverarbeitung: ' . $e->getMessage());
        }
    }

    /**
     * Show featured event history for organizer
     */
    public function history()
    {
        $user = Auth::user();
        $featuredHistory = $this->featuredEventService->getUserFeaturedHistory($user);

        return view('events.featured.history', compact('featuredHistory'));
    }

    /**
     * Extend featured period
     */
    public function extend(Event $event)
    {
        $user = Auth::user();

        if (!$this->featuredEventService->canFeatureEvent($event, $user)) {
            return redirect()->back()->with('error', 'Sie können dieses Event nicht verlängern.');
        }

        $pricing = $this->featuredEventService->getPricingInfo();
        $activeFee = $event->activeFeaturedFee();

        return view('events.featured.extend', compact('event', 'pricing', 'activeFee'));
    }

    /**
     * Process extension
     */
    public function processExtension(Request $request, Event $event)
    {
        $user = Auth::user();

        if (!$this->featuredEventService->canFeatureEvent($event, $user)) {
            return redirect()->back()->with('error', 'Sie können dieses Event nicht verlängern.');
        }

        $validated = $request->validate([
            'duration_type' => 'required|in:daily,weekly,monthly,custom',
            'custom_days' => 'nullable|integer|min:1|max:' . config('monetization.featured_event_max_duration_days'),
        ]);

        $customDays = $validated['duration_type'] === 'custom' ? $validated['custom_days'] : null;

        try {
            $featuredFee = $this->featuredEventService->extendFeaturedPeriod(
                $event,
                $user,
                $validated['duration_type'],
                $customDays
            );

            return redirect()
                ->route('featured-events.payment', $featuredFee)
                ->with('success', 'Verlängerung erstellt. Bitte schließen Sie die Zahlung ab.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Fehler beim Verlängern: ' . $e->getMessage());
        }
    }

    /**
     * Cancel featured status
     */
    public function cancel(Event $event)
    {
        $user = Auth::user();

        if ($event->user_id !== $user->id) {
            abort(403);
        }

        $event->update(['is_featured' => false]);

        return redirect()
            ->back()
            ->with('success', 'Featured Status wurde deaktiviert.');
    }
}

