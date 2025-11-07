<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Event $event)
    {
        $this->authorize('update', $event);

        $ticketTypes = $event->ticketTypes()->orderBy('sort_order')->get();

        return view('organizer.ticket-types.index', compact('event', 'ticketTypes'));
    }

    public function create(Event $event)
    {
        $this->authorize('update', $event);

        return view('organizer.ticket-types.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'nullable|integer|min:1',
            'max_per_booking' => 'nullable|integer|min:1',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after:sale_start',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['event_id'] = $event->id;

        // Auto-increment sort_order if not provided
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = $event->ticketTypes()->max('sort_order') + 1;
        }

        $ticketType = TicketType::create($validated);

        return redirect()
            ->route('organizer.events.ticket-types.index', $event)
            ->with('success', 'Ticket-Typ erfolgreich erstellt!');
    }

    public function edit(Event $event, TicketType $ticketType)
    {
        $this->authorize('update', $event);

        if ($ticketType->event_id !== $event->id) {
            abort(404);
        }

        return view('organizer.ticket-types.edit', compact('event', 'ticketType'));
    }

    public function update(Request $request, Event $event, TicketType $ticketType)
    {
        $this->authorize('update', $event);

        if ($ticketType->event_id !== $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'nullable|integer|min:1',
            'max_per_booking' => 'nullable|integer|min:1',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after:sale_start',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $ticketType->update($validated);

        return redirect()
            ->route('organizer.events.ticket-types.index', $event)
            ->with('success', 'Ticket-Typ erfolgreich aktualisiert!');
    }

    public function destroy(Event $event, TicketType $ticketType)
    {
        $this->authorize('update', $event);

        if ($ticketType->event_id !== $event->id) {
            abort(404);
        }

        // Check if ticket type has bookings
        if ($ticketType->bookingItems()->count() > 0) {
            return redirect()
                ->route('organizer.events.ticket-types.index', $event)
                ->with('error', 'Ticket-Typ kann nicht gelöscht werden, da bereits Buchungen vorhanden sind.');
        }

        $ticketType->delete();

        return redirect()
            ->route('organizer.events.ticket-types.index', $event)
            ->with('success', 'Ticket-Typ erfolgreich gelöscht!');
    }

    public function reorder(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|exists:ticket_types,id',
        ]);

        foreach ($validated['order'] as $index => $ticketTypeId) {
            TicketType::where('id', $ticketTypeId)
                ->where('event_id', $event->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}

