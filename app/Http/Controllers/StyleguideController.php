<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * Interne Styleguide-Seite: zeigt jeden Baustein aus resources/views/components/ui/ in jedem Zustand.
 * Für die Listenzeile werden echte Veranstaltungen verwendet, möglichst mit allen Zuständen
 * (buchbar, Warteliste, mehrtägig, abgesagt, beendet).
 */
class StyleguideController extends Controller
{
    public function __invoke(): View
    {
        /** @var Collection<int, Event> $all */
        $all = Event::with(['category', 'ticketTypes'])
            ->where('is_published', true)
            ->orderBy('start_date')
            ->get();

        $upcoming = $all->filter(fn (Event $event): bool => ! $event->is_cancelled && $event->start_date->isFuture());

        /** @var Collection<int, Event> $events */
        $events = collect()
            ->merge($upcoming->take(3))
            ->push($upcoming->first(fn (Event $event): bool => ! $event->hasAvailableTickets()))
            ->push($upcoming->first(fn (Event $event): bool => $event->end_date !== null && ! $event->end_date->isSameDay($event->start_date)))
            ->push($all->first(fn (Event $event): bool => $event->is_cancelled))
            ->push($all->first(fn (Event $event): bool => ($event->end_date ?? $event->start_date)->isPast()))
            ->filter()
            ->unique('id')
            ->values();

        $paginated = Event::where('is_published', true)
            ->orderBy('start_date')
            ->paginate(3, ['*'], 'seite');

        $booking = Booking::with('event.category')
            ->whereHas('event')
            ->latest()
            ->first();

        return view('styleguide', [
            'events' => $events,
            'paginated' => $paginated,
            'booking' => $booking,
        ]);
    }
}
