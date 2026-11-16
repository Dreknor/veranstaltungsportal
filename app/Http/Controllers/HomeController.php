<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** Anzahl der Kategorien auf der Startseite */
    public const CATEGORY_LIMIT = 8;

    /** Anzahl der Zeilen unter „Kommende Veranstaltungen" */
    public const UPCOMING_LIMIT = 6;

    public function __invoke(): View
    {
        // Empfohlene (bezahlt hervorgehobene) Veranstaltungen
        $featuredEvents = Event::listed()
            ->featured()
            ->with(['category', 'ticketTypes'])
            ->orderBy('start_date')
            ->limit(3)
            ->get();

        // Kommende Veranstaltungen ohne die bereits empfohlenen; die Anzahl bleibt gleich
        $upcomingEvents = Event::listed()
            ->whereNotIn('id', $featuredEvents->modelKeys())
            ->with(['category', 'ticketTypes'])
            ->orderBy('start_date')
            ->limit(self::UPCOMING_LIMIT)
            ->get();

        // Nur die Kategorien mit den meisten kommenden, veröffentlichten Veranstaltungen
        // (Zählregel wie die Trefferliste hinter dem Kategorie-Link)
        $categories = EventCategory::where('is_active', true)
            ->withCount(['events as listed_events_count' => fn ($q) => $q->listed()])
            ->orderByDesc('listed_events_count')
            ->orderBy('name')
            ->limit(self::CATEGORY_LIMIT)
            ->get();

        // Alle aktiven Kategorien für den Filter „Kategorie" in der Suchzeile
        $allCategories = EventCategory::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');

        // Schwerpunkt: Link auf die Kategorie „Hauptfach Mensch", sonst auf die Suche danach
        $focusCategoryId = $allCategories->search('Hauptfach Mensch');
        $focusHref = $focusCategoryId !== false
            ? route('events.index', ['category' => $focusCategoryId])
            : route('events.index', ['search' => 'Hauptfach Mensch']);

        return view('welcome', compact('featuredEvents', 'upcomingEvents', 'categories', 'allCategories', 'focusHref'));
    }
}
