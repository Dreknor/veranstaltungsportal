<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSeries;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // Einzelne Events (keine Serie-Teile)
        $eventsQuery = Event::query()
            ->with(['category', 'organization.users'])
            ->published()
            ->where(function($q) {
                $q->where('is_series_part', false)
                  ->orWhereNull('is_series_part');
            });

        // Aktive Veranstaltungsreihen
        $seriesQuery = EventSeries::query()
            ->with(['category', 'organization.users', 'events'])
            ->where('is_active', true);

        // Filter nach Kategorie
        if ($request->filled('category')) {
            $eventsQuery->where('event_category_id', $request->category);
            $seriesQuery->where('event_category_id', $request->category);
        }

        // Filter nach Stadt (nur für Events)
        if ($request->filled('city')) {
            $eventsQuery->where('venue_city', 'LIKE', '%' . $request->city . '%');
        }

        // Suche
        if ($request->filled('search')) {
            $search = $request->search;
            $eventsQuery->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('venue_name', 'LIKE', "%{$search}%");
            });
            $seriesQuery->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Datum Filter (nur für Events)
        if ($request->filled('date_from')) {
            $eventsQuery->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $eventsQuery->where('start_date', '<=', $request->date_to);
        }

        // Events und Serien abrufen
        $events = $eventsQuery->get();
        $series = $seriesQuery->get();

        // Kombiniere Events und Serien für die Anzeige
        // Erstelle eine Collection mit beiden Typen
        $allItems = collect([]);

        foreach ($events as $event) {
            $allItems->push([
                'type' => 'event',
                'item' => $event,
                'date' => $event->start_date,
            ]);
        }

        foreach ($series as $seriesItem) {
            // Nehme das Datum des ersten Events in der Serie
            $firstEvent = $seriesItem->events->first();
            $allItems->push([
                'type' => 'series',
                'item' => $seriesItem,
                'date' => $firstEvent ? $firstEvent->start_date : now(),
            ]);
        }

        // Sortierung
        $sortBy = $request->get('sort', 'date');
        $sortOrder = $request->get('order', 'asc');

        $allItems = $sortOrder === 'asc'
            ? $allItems->sortBy('date')
            : $allItems->sortByDesc('date');

        // Manuelle Paginierung
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $items = $allItems->forPage($currentPage, $perPage)->values();

        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categories = EventCategory::where('is_active', true)->get();

        // Unterscheide zwischen angemeldeten und nicht-angemeldeten Benutzern
        $view = auth()->check() ? 'events.index-auth' : 'events.index';

        return view($view, [
            'items' => $paginatedItems,
            'categories' => $categories,
        ]);
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $events = Event::query()
            ->published()
            ->where(function($q) {
                $q->where('is_series_part', false)
                  ->orWhereNull('is_series_part');
            }) // Keine Serien-Termine im Kalender
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->with(['category', 'organization.users'])
            ->get();

        // Unterscheide zwischen angemeldeten und nicht-angemeldeten Benutzern
        $view = auth()->check() ? 'events.calendar-auth' : 'events.calendar';

        return view($view, compact('events', 'month', 'year'));
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->with(['category', 'organization.users', 'series', 'ticketTypes', 'reviews.user'])
            ->firstOrFail();

        // Increment view count
        $event->incrementViews();

        // Wenn Event Teil einer Serie ist, zur Serie umleiten
        if ($event->isPartOfSeries() && $event->series) {
            return redirect()->route('series.show', $event->series->id)
                ->with('info', 'Dieses Event ist Teil einer Veranstaltungsreihe. Bitte buchen Sie die gesamte Reihe.');
        }

        // Prüfe ob Event privat ist und Access Code benötigt
        if ($event->is_private && !session()->has('event_access_' . $event->id)) {
            return redirect()->route('events.access', $event->slug);
        }

        // Prüfe ob Event veröffentlicht ist
        if (!$event->is_published) {
            // Allow owners of the organization to view unpublished events
            if (!auth()->check() || !$event->organization->users()->where('user_id', auth()->id())->wherePivot('role', 'owner')->exists()) {
                abort(404);
            }
        }

        $relatedEvents = Event::published()
            ->where(function($q) {
                $q->where('is_series_part', false)
                  ->orWhereNull('is_series_part');
            }) // Keine Serien-Teile
            ->where('event_category_id', $event->event_category_id)
            ->where('id', '!=', $event->id)
            ->limit(3)
            ->get();

        // Social Share URLs
        $socialShareService = app(\App\Services\SocialShareService::class);
        $shareUrls = $socialShareService->getAllShareUrls($event);
        $shareableLink = $socialShareService->getShareableLink($event);

        // Unterscheide zwischen angemeldeten und nicht-angemeldeten Benutzern
        $view = auth()->check() ? 'events.show-auth' : 'events.show';

        return view($view, compact('event', 'relatedEvents', 'shareUrls', 'shareableLink'));
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
        return $calendarService->downloadEventIcal($event);
    }
}

