{{--
    Listenzeile einer Veranstaltung – der prägende Baustein des Portals (B3).
    Gehört in <x-ui.row-list>. Alles wird aus dem Modell ermittelt:
    Datumsblock, Kategorie, Titel mit Link, "Format · Ort", Zeit, Preis, Platzstatus, Aktion.

    Slots zum Überschreiben (z. B. "Meine Buchungen", Veranstalter-Bereich):
      action – Inhalt der Aktionsspalte (statt "Buchen" / "Auf die Warteliste")
      meta   – Inhalt der Spalte "Format · Ort / Zeit"
      aside  – Inhalt der Spalte "Preis / Status"
      Standard-Slot – zusätzliche Zeile(n) unter dem Titel (z. B. Buchungsnummer)
    organizer-rating: Veranstalter mit Gesamtbewertung unter dem Titel (öffentliche Listen; erst ab
      ReviewSummary::MIN_COUNT Bewertungen, sonst nichts). Für Listen 'organization' vorladen.
    Ist ein Slot gesetzt, werden die zugehörigen Werte nicht berechnet (keine Abfragen).

    Für Listen bitte vorladen: Event::with(['category', 'ticketTypes']).
--}}
@props([
    'event',
    'href' => null, // Ziel des Titels; Standard: Detailseite
    'heading' => 'h3', // Element des Titels (h2 | h3 | p)
    'showAction' => true, // false: keine Aktionsspalte
    'as' => 'li',
    'action' => null,
    'meta' => null,
    'aside' => null,
    'organizerRating' => false,
])

@php
    use App\Support\Ui;
    use Illuminate\View\ComponentSlot;

    $filled = fn($part) => $part instanceof ComponentSlot ? $part->isNotEmpty() : filled($part);

    $start = $event->start_date;
    $end = $event->end_date;
    $multiDay = $end && !$end->isSameDay($start);
    $href ??= route('events.show', $event->slug);
    $category = $event->category?->name;

    // Zustand der Veranstaltung – nur berechnen, wenn er gebraucht wird
    $needsState = ($showAction && !$filled($action)) || !$filled($aside);
    $state = null;
    $free = null;

    if ($needsState) {
        if ($event->is_cancelled) {
            $state = 'cancelled';
        } elseif (($end ?? $start)->isPast()) {
            $state = 'past';
        } elseif ($event->isExternal()) {
            $state = 'external';
        } elseif ($event->hasAvailableTickets()) {
            $state = 'bookable';
            if ($event->max_attendees) {
                $free = $event->availableTickets();
            } else {
                $onSale = $event->ticketTypes->filter(fn($t) => $t->isOnSale());
                $free =
                    $onSale->isNotEmpty() && $onSale->every(fn($t) => $t->quantity !== null)
                        ? $onSale->sum(fn($t) => $t->availableQuantity())
                        : null;
            }
        } else {
            $soldOut =
                ($event->max_attendees && $event->availableTickets() <= 0) ||
                ($event->ticketTypes->isNotEmpty() &&
                    $event->ticketTypes->every(fn($t) => $t->availableQuantity() === 0));
            $state = $soldOut ? 'waitlist' : 'closed';
        }
    }

    // Preis: niedrigster buchbarer Preis; "ab", wenn es mehrere Preise gibt
    $priceText = null;
    if (!$filled($aside) && !$event->isExternal()) {
        $price = $event->getMinimumPrice() ?? $event->ticketTypes->min('price');
        if ($price !== null) {
            $several = $event->ticketTypes->pluck('price')->map(fn($p) => (float) $p)->unique()->count() > 1;
            $priceText = ($several && (float) $price > 0 ? 'ab ' : '') . Ui::price($price);
        }
    }

    $timeText = match (true) {
        (bool) $event->has_multiple_dates => 'mehrere Termine',
        $multiDay => Ui::days($start, $end),
        default => Ui::time($start, $end),
    };
@endphp

<x-ui.row :as="$as" {{ $attributes }}>
    <x-slot:lead>
        <x-ui.date-block :date="$start" :end="$end" />
    </x-slot:lead>

    @if ($category)
        <span class="text-small text-muted">{{ $category }}</span>
    @endif
    <{{ $heading }} class="m-0 text-h3 text-ink">
        <a href="{{ $href }}"
            class="text-ink no-underline underline-offset-[3px] hover:underline">{{ $event->title }}</a>
        </{{ $heading }}>
        @if ($organizerRating && $event->organization)
            <x-ui.rating :summary="$event->organization->reviewSummary()" :prefix="$event->organization->name"
                size="small" hide-if-few />
        @endif
        {{ $slot }}

        <x-slot:meta>
            @if ($filled($meta))
                {{ $meta }}
            @else
                <span class="text-ui font-medium">{{ Ui::formatAndPlace($event) }}</span>
                <span class="text-small text-muted tabular-nums">{{ $timeText }}</span>
            @endif
        </x-slot:meta>

        <x-slot:aside>
            @if ($filled($aside))
                {{ $aside }}
            @else
                @if ($priceText)
                    <span class="text-base font-semibold whitespace-nowrap">{{ $priceText }}</span>
                @endif
                @switch($state)
                    @case('bookable')
                        <x-ui.status tone="free">{{ $free === null ? 'Plätze frei' : 'noch ' . $free . ' frei' }}</x-ui.status>
                    @break

                    @case('waitlist')
                        <x-ui.status tone="waitlist">Warteliste</x-ui.status>
                    @break

                    @case('cancelled')
                        <x-ui.status tone="danger">Abgesagt</x-ui.status>
                    @break

                    @case('past')
                        <x-ui.status tone="neutral">Beendet</x-ui.status>
                    @break

                    @case('external')
                        <x-ui.status tone="neutral">Anmeldung extern</x-ui.status>
                    @break

                    @default
                        <x-ui.status tone="neutral">derzeit nicht buchbar</x-ui.status>
                @endswitch
            @endif
        </x-slot:aside>

        @if ($showAction)
            <x-slot:action>
                @if ($filled($action))
                    {{ $action }}
                @else
                    @switch($state)
                        @case('bookable')
                            <x-ui.button :href="route('bookings.create', $event)" full>Buchen</x-ui.button>
                        @break

                        @case('waitlist')
                            <x-ui.button variant="secondary" :href="route('events.show', $event->slug) . '#warteliste'" full>Auf die Warteliste</x-ui.button>
                        @break

                        @case('external')
                            @if ($event->external_booking_url)
                                <x-ui.button variant="secondary" :href="$event->external_booking_url" target="_blank" rel="noopener noreferrer"
                                    full>
                                    {{ $event->external_booking_button_text ?: 'Zur Anmeldung' }}
                                    <x-icon.external-link class="h-4 w-4 shrink-0" />
                                    <span class="sr-only">(öffnet eine externe Seite)</span>
                                </x-ui.button>
                            @else
                                <x-ui.button variant="secondary" :href="$href" full>Details</x-ui.button>
                            @endif
                        @break

                        @default
                            <x-ui.button variant="secondary" :href="$href" full>Details</x-ui.button>
                    @endswitch
                @endif
            </x-slot:action>
        @endif
</x-ui.row>
