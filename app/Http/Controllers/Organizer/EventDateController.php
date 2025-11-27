<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventDate;
use Illuminate\Http\Request;

class EventDateController extends Controller
{
    /**
     * Store a new event date
     */
    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        if (!$event->has_multiple_dates) {
            return back()->with('error', 'Dieses Event hat keine mehreren Termine aktiviert.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue_name' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string',
            'venue_city' => 'nullable|string|max:255',
            'venue_postal_code' => 'nullable|string|max:20',
            'venue_country' => 'nullable|string|max:100',
            'venue_latitude' => 'nullable|numeric',
            'venue_longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $validated['event_id'] = $event->id;

        EventDate::create($validated);

        return back()->with('success', 'Termin erfolgreich hinzugefügt!');
    }

    /**
     * Update an event date
     */
    public function update(Request $request, Event $event, EventDate $eventDate)
    {
        $this->authorize('update', $event);

        if ($eventDate->event_id !== $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue_name' => 'nullable|string|max:255',
            'venue_address' => 'nullable|string',
            'venue_city' => 'nullable|string|max:255',
            'venue_postal_code' => 'nullable|string|max:20',
            'venue_country' => 'nullable|string|max:100',
            'venue_latitude' => 'nullable|numeric',
            'venue_longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'is_cancelled' => 'boolean',
            'cancellation_reason' => 'nullable|string|required_if:is_cancelled,1',
        ]);

        $eventDate->update($validated);

        return back()->with('success', 'Termin erfolgreich aktualisiert!');
    }

    /**
     * Delete an event date
     */
    public function destroy(Event $event, EventDate $eventDate)
    {
        $this->authorize('update', $event);

        if ($eventDate->event_id !== $event->id) {
            abort(404);
        }

        // Prevent deletion if it's the last date
        if ($event->dates()->count() <= 1) {
            return back()->with('error', 'Sie können den letzten Termin nicht löschen. Deaktivieren Sie stattdessen "Mehrere Termine".');
        }

        $eventDate->delete();

        return back()->with('success', 'Termin erfolgreich gelöscht!');
    }

    /**
     * Cancel an event date
     */
    public function cancel(Request $request, Event $event, EventDate $eventDate)
    {
        $this->authorize('update', $event);

        if ($eventDate->event_id !== $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        $eventDate->update([
            'is_cancelled' => true,
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        // TODO: Notify participants about the cancellation

        return back()->with('success', 'Termin erfolgreich abgesagt!');
    }

    /**
     * Reactivate a cancelled event date
     */
    public function reactivate(Event $event, EventDate $eventDate)
    {
        $this->authorize('update', $event);

        if ($eventDate->event_id !== $event->id) {
            abort(404);
        }

        $eventDate->update([
            'is_cancelled' => false,
            'cancellation_reason' => null,
        ]);

        return back()->with('success', 'Termin erfolgreich reaktiviert!');
    }
}

