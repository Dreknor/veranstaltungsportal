<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventWaitlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WaitlistController extends Controller
{
    /**
     * Join waitlist for an event
     */
    public function join(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'quantity' => 'required|integer|min:1|max:10',
            'ticket_type_id' => 'nullable|exists:ticket_types,id',
        ]);

        // Check if already on waitlist
        $existingEntry = EventWaitlist::where('event_id', $event->id)
            ->where('email', $validated['email'])
            ->whereIn('status', ['waiting', 'notified'])
            ->first();

        if ($existingEntry) {
            return back()->with('error', 'Sie stehen bereits auf der Warteliste für diese Veranstaltung.');
        }

        $validated['event_id'] = $event->id;
        $validated['user_id'] = auth()->id();

        $waitlistEntry = EventWaitlist::create($validated);

        // Send confirmation email
        // Mail::to($validated['email'])->send(new WaitlistConfirmation($waitlistEntry));

        return back()->with('success', 'Sie wurden erfolgreich zur Warteliste hinzugefügt. Wir benachrichtigen Sie, sobald Tickets verfügbar sind.');
    }

    /**
     * Leave waitlist
     */
    public function leave(Request $request, Event $event)
    {
        $email = $request->user() ? $request->user()->email : $request->input('email');

        $deleted = EventWaitlist::where('event_id', $event->id)
            ->where('email', $email)
            ->whereIn('status', ['waiting', 'notified'])
            ->delete();

        if ($deleted) {
            return back()->with('success', 'Sie wurden von der Warteliste entfernt.');
        }

        return back()->with('error', 'Sie stehen nicht auf der Warteliste für diese Veranstaltung.');
    }

    /**
     * Show waitlist for organizer
     */
    public function index(Event $event)
    {
        $this->authorize('view', $event);

        $waitlist = EventWaitlist::where('event_id', $event->id)
            ->with(['user', 'ticketType'])
            ->orderBy('created_at')
            ->paginate(50);

        $statistics = [
            'total' => EventWaitlist::where('event_id', $event->id)->count(),
            'waiting' => EventWaitlist::where('event_id', $event->id)->waiting()->count(),
            'notified' => EventWaitlist::where('event_id', $event->id)->notified()->count(),
            'converted' => EventWaitlist::where('event_id', $event->id)->where('status', 'converted')->count(),
            'total_requested_tickets' => EventWaitlist::where('event_id', $event->id)->waiting()->sum('quantity'),
        ];

        return view('organizer.waitlist.index', compact('event', 'waitlist', 'statistics'));
    }

    /**
     * Notify next person on waitlist
     */
    public function notifyNext(Event $event, Request $request)
    {
        $this->authorize('update', $event);

        $quantity = $request->input('quantity', 1);

        $nextEntries = EventWaitlist::where('event_id', $event->id)
            ->waiting()
            ->notExpired()
            ->where('quantity', '<=', $quantity)
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        $notifiedCount = 0;

        foreach ($nextEntries as $entry) {
            if ($quantity >= $entry->quantity) {
                $entry->markAsNotified();

                // Send notification email
                // Mail::to($entry->email)->send(new WaitlistTicketAvailable($entry));

                $quantity -= $entry->quantity;
                $notifiedCount++;
            }
        }

        if ($notifiedCount > 0) {
            return back()->with('success', "{$notifiedCount} Person(en) wurden benachrichtigt.");
        }

        return back()->with('info', 'Keine passenden Wartelisten-Einträge gefunden.');
    }

    /**
     * Remove from waitlist
     */
    public function remove(Event $event, EventWaitlist $waitlist)
    {
        $this->authorize('update', $event);

        $waitlist->delete();

        return back()->with('success', 'Eintrag von der Warteliste entfernt.');
    }
}

