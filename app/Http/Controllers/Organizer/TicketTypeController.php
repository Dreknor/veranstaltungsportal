<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{

    public function index(Event $event)
    {
        $this->authorize('update', $event);

        $ticketTypes = $event->ticketTypes()->get();

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
            'quantity' => 'nullable|integer|min:1',
            'max_per_order' => 'nullable|integer|min:1',
            'min_per_order' => 'nullable|integer|min:1',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after:sale_start',
        ]);

        $validated['is_available'] = $request->boolean('is_available');
        $validated['event_id'] = $event->id;
        $validated['quantity_sold'] = 0;

        $ticketType = TicketType::create($validated);

        // Check if request came from event edit page (inline form)
        if ($request->input('redirect_to_edit')) {
            return redirect()
                ->route('organizer.events.edit', $event)
                ->with('success', 'Ticket-Typ erfolgreich erstellt!');
        }

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
            'quantity' => 'nullable|integer|min:1',
            'max_per_order' => 'nullable|integer|min:1',
            'min_per_order' => 'nullable|integer|min:1',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after:sale_start',
        ]);

        $validated['is_available'] = $request->boolean('is_available');

        // Preisänderung verhindern, wenn aktive (nicht-stornierte) Buchungen existieren
        if ($ticketType->hasActiveSales() && (float)$validated['price'] !== (float)$ticketType->price) {
            return back()
                ->withInput()
                ->withErrors(['price' => 'Der Preis kann nicht geändert werden, da noch aktive Buchungen für diesen Ticket-Typ vorhanden sind. Bitte stornieren Sie zuerst alle Buchungen dieses Typs.']);
        }

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

        // Löschen nur wenn keine aktiven (nicht-stornierten) Buchungen vorhanden
        if ($ticketType->hasActiveSales()) {
            return redirect()
                ->route('organizer.events.ticket-types.index', $event)
                ->with('error', 'Dieser Ticket-Typ kann nicht gelöscht werden, da noch aktive Buchungen vorhanden sind. Bitte stornieren Sie zuerst alle Buchungen dieses Typs.');
        }

        $ticketType->delete();

        return redirect()
            ->route('organizer.events.ticket-types.index', $event)
            ->with('success', 'Ticket-Typ erfolgreich gelöscht!');
    }

    public function reorder(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        // Sort order feature not yet implemented (requires migration to add sort_order column)
        return response()->json(['success' => false, 'message' => 'Sort order feature requires database migration']);
    }
}

