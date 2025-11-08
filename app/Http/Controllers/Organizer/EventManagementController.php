<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventManagementController extends Controller
{

    public function index()
    {
        $events = Event::where('user_id', auth()->id())
            ->with(['category', 'bookings'])
            ->latest()
            ->paginate(15);

        return view('organizer.events.index', compact('events'));
    }

    public function create()
    {
        $categories = EventCategory::where('is_active', true)->get();
        return view('organizer.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_category_id' => 'required|exists:event_categories,id',
            'event_type' => 'required|in:physical,online,hybrid',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue_name' => 'required_if:event_type,physical,hybrid|nullable|string|max:255',
            'venue_address' => 'required_if:event_type,physical,hybrid|nullable|string',
            'venue_city' => 'required_if:event_type,physical,hybrid|nullable|string|max:255',
            'venue_postal_code' => 'required_if:event_type,physical,hybrid|nullable|string|max:20',
            'venue_country' => 'required_if:event_type,physical,hybrid|nullable|string|max:100',
            'venue_latitude' => 'nullable|numeric',
            'venue_longitude' => 'nullable|numeric',
            'directions' => 'nullable|string',
            'online_url' => 'required_if:event_type,online,hybrid|nullable|url',
            'online_access_code' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
            'livestream_url' => 'nullable|url',
            'price_from' => 'nullable|numeric|min:0',
            'max_attendees' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_private' => 'boolean',
            'access_code' => 'nullable|string|required_if:is_private,1',
            'organizer_info' => 'nullable|string',
            'organizer_email' => 'nullable|email',
            'organizer_phone' => 'nullable|string',
            'organizer_website' => 'nullable|url',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);

        // Handle Image Upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');
            $validated['featured_image'] = $path;
        }

        $event = Event::create($validated);

        return redirect()->route('organizer.events.edit', $event)
            ->with('success', 'Event erfolgreich erstellt!');
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $categories = EventCategory::where('is_active', true)->get();
        $ticketTypes = $event->ticketTypes;

        return view('organizer.events.edit', compact('event', 'categories', 'ticketTypes'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_category_id' => 'required|exists:event_categories,id',
            'event_type' => 'required|in:physical,online,hybrid',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue_name' => 'required_if:event_type,physical,hybrid|nullable|string|max:255',
            'venue_address' => 'required_if:event_type,physical,hybrid|nullable|string',
            'venue_city' => 'required_if:event_type,physical,hybrid|nullable|string|max:255',
            'venue_postal_code' => 'required_if:event_type,physical,hybrid|nullable|string|max:20',
            'venue_country' => 'required_if:event_type,physical,hybrid|nullable|string|max:100',
            'venue_latitude' => 'nullable|numeric',
            'venue_longitude' => 'nullable|numeric',
            'directions' => 'nullable|string',
            'online_url' => 'required_if:event_type,online,hybrid|nullable|url',
            'online_access_code' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
            'livestream_url' => 'nullable|url',
            'price_from' => 'nullable|numeric|min:0',
            'max_attendees' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_private' => 'boolean',
            'access_code' => 'nullable|string|required_if:is_private,1',
            'organizer_info' => 'nullable|string',
            'organizer_email' => 'nullable|email',
            'organizer_phone' => 'nullable|string',
            'organizer_website' => 'nullable|url',
        ]);

        // Handle Image Upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');
            $validated['featured_image'] = $path;
        }

        $event->update($validated);

        return redirect()->route('organizer.events.edit', $event)
            ->with('success', 'Event erfolgreich aktualisiert!');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event erfolgreich gelöscht!');
    }

    public function duplicate(Event $event)
    {
        $this->authorize('view', $event);

        // Dupliziere Event
        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Kopie)';
        $newEvent->slug = Str::slug($newEvent->title) . '-' . Str::random(6);
        $newEvent->is_published = false;
        $newEvent->is_featured = false;
        $newEvent->created_at = now();
        $newEvent->updated_at = now();
        $newEvent->save();

        // Dupliziere Ticket-Typen
        foreach ($event->ticketTypes as $ticketType) {
            $newTicketType = $ticketType->replicate();
            $newTicketType->event_id = $newEvent->id;
            $newTicketType->quantity_sold = 0;
            $newTicketType->save();
        }

        // Dupliziere Rabattcodes
        foreach ($event->discountCodes as $discountCode) {
            $newDiscountCode = $discountCode->replicate();
            $newDiscountCode->event_id = $newEvent->id;
            $newDiscountCode->usage_count = 0;
            $newDiscountCode->save();
        }

        return redirect()->route('organizer.events.edit', $newEvent)
            ->with('success', 'Event erfolgreich dupliziert! Bitte aktualisieren Sie die Daten.');
    }

    public function addTicketType(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after:sale_start',
            'min_per_order' => 'required|integer|min:1',
            'max_per_order' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
        ]);

        $validated['event_id'] = $event->id;
        TicketType::create($validated);

        return back()->with('success', 'Ticket-Typ erfolgreich hinzugefügt!');
    }

    public function updateTicketType(Request $request, Event $event, TicketType $ticketType)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after:sale_start',
            'min_per_order' => 'required|integer|min:1',
            'max_per_order' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
        ]);

        $ticketType->update($validated);

        return back()->with('success', 'Ticket-Typ erfolgreich aktualisiert!');
    }

    public function deleteTicketType(Event $event, TicketType $ticketType)
    {
        $this->authorize('update', $event);

        if ($ticketType->quantity_sold > 0) {
            return back()->with('error', 'Ticket-Typ kann nicht gelöscht werden, da bereits Tickets verkauft wurden.');
        }

        $ticketType->delete();

        return back()->with('success', 'Ticket-Typ erfolgreich gelöscht!');
    }
}

