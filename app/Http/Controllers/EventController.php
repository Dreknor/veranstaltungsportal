<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query()
            ->with(['category', 'user'])
            ->published();

        // Filter nach Kategorie
        if ($request->has('category')) {
            $query->where('event_category_id', $request->category);
        }

        // Filter nach Stadt
        if ($request->has('city')) {
            $query->where('venue_city', 'LIKE', '%' . $request->city . '%');
        }

        // Suche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('venue_name', 'LIKE', "%{$search}%");
            });
        }

        // Datum Filter
        if ($request->has('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        // Sortierung
        $sortBy = $request->get('sort', 'start_date');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $events = $query->paginate(12);
        $categories = EventCategory::where('is_active', true)->get();

        return view('events.index', compact('events', 'categories'));
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $events = Event::query()
            ->published()
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->with('category')
            ->get();

        return view('events.calendar', compact('events', 'month', 'year'));
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->with(['category', 'user', 'ticketTypes', 'reviews.user'])
            ->firstOrFail();

        // Prüfe ob Event privat ist und Access Code benötigt
        if ($event->is_private && !session()->has('event_access_' . $event->id)) {
            return redirect()->route('events.access', $event->slug);
        }

        // Prüfe ob Event veröffentlicht ist
        if (!$event->is_published && $event->user_id !== auth()->id()) {
            abort(404);
        }

        $relatedEvents = Event::published()
            ->where('event_category_id', $event->event_category_id)
            ->where('id', '!=', $event->id)
            ->limit(3)
            ->get();

        return view('events.show', compact('event', 'relatedEvents'));
    }

    public function access($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        if (!$event->is_private) {
            return redirect()->route('events.show', $slug);
        }

        return view('events.access', compact('event'));
    }

    public function verifyAccess(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $request->validate([
            'access_code' => 'required|string',
        ]);

        if ($request->access_code === $event->access_code) {
            session()->put('event_access_' . $event->id, true);
            return redirect()->route('events.show', $slug)
                ->with('success', 'Zugriff gewährt!');
        }

        return back()->withErrors(['access_code' => 'Ungültiger Access Code.']);
    }

    public function exportToCalendar($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Check if event is private and access is granted
        if ($event->is_private && !session()->has('event_access_' . $event->id) && $event->user_id !== auth()->id()) {
            abort(403, 'Kein Zugriff auf dieses Event');
        }

        $calendarService = app(\App\Services\CalendarService::class);
        $icalContent = $calendarService->generateEventIcal($event);
        $filename = $calendarService->getFilename($event);

        return response($icalContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}

