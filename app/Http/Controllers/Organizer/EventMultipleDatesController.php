<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventMultipleDatesController extends Controller
{
    /**
     * Toggle has_multiple_dates for an event
     */
    public function toggle(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'has_multiple_dates' => 'required|boolean',
        ]);

        $wasMultipleDates = $event->has_multiple_dates;
        $isMultipleDatesNow = $validated['has_multiple_dates'];

        // Update only the has_multiple_dates field
        $event->has_multiple_dates = $isMultipleDatesNow;
        $event->save();

        // Create first EventDate when activating
        if ($isMultipleDatesNow && !$wasMultipleDates) {
            \App\Models\EventDate::create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
                'venue_name' => $event->venue_name,
                'venue_address' => $event->venue_address,
                'venue_city' => $event->venue_city,
                'venue_postal_code' => $event->venue_postal_code,
                'venue_country' => $event->venue_country,
                'venue_latitude' => $event->venue_latitude,
                'venue_longitude' => $event->venue_longitude,
                'notes' => 'Erster Termin (aus Hauptevent übernommen)',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mehrere Termine aktiviert. Der erste Termin wurde automatisch erstellt.',
                'reload' => true, // Signal to reload page
            ]);
        }

        // Delete all EventDates when deactivating (with confirmation from frontend)
        if (!$isMultipleDatesNow && $wasMultipleDates) {
            $event->dates()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mehrere Termine deaktiviert. Alle Termine wurden gelöscht.',
                'reload' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Einstellung gespeichert.',
        ]);
    }
}

