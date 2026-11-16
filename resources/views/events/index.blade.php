<x-layouts.public title="Veranstaltungen - Fort- und Weiterbildungen entdecken"
    description="Entdecken Sie Fort- und Weiterbildungen für evangelische Schulen und Bildungseinrichtungen. Filter nach Kategorie, Ort und Datum.">
    @push('meta')
        @php
            $breadcrumbs = [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Veranstaltungen', 'url' => route('events.index')],
            ];
            $description =
                'Entdecken Sie Fort- und Weiterbildungen für evangelische Schulen und Bildungseinrichtungen. Filter nach Kategorie, Ort und Datum.';
            // Canonical immer auf Seite 1 (ohne ?page=N) setzen
            $canonicalUrl = route('events.index');
            $isFilteredPage = request()->hasAny(['search', 'category', 'city', 'date_from', 'date_to', 'type', 'page']);
        @endphp
        <x-meta-tags :title="'Veranstaltungen - Fort- und Weiterbildungen entdecken'" :description="$description" :breadcrumbs="$breadcrumbs" :canonical="$canonicalUrl" />
        @if ($isFilteredPage)
            <meta name="robots" content="noindex, follow">
        @endif
        <link rel="alternate" type="application/atom+xml" title="{{ config('app.name') }} – Veranstaltungen Feed"
            href="{{ route('feed.atom') }}">
    @endpush

    @php
        $filterCount = collect(['date_from', 'date_to'])
            ->filter(fn($key) => request()->filled($key))
            ->count();
        $activeCategory = request()->filled('category')
            ? $categories->firstWhere('id', (int) request('category'))
            : null;
        $activeFilters = array_filter([
            $activeCategory ? 'Kategorie: ' . $activeCategory->name : null,
            request()->filled('city') ? 'Ort: ' . request('city') : null,
            request()->filled('date_from')
                ? 'ab ' .
                    rescue(
                        fn() => \Carbon\Carbon::parse(request('date_from'))->format('d.m.Y'),
                        request('date_from'),
                        false,
                    )
                : null,
            request()->filled('date_to')
                ? 'bis ' .
                    rescue(
                        fn() => \Carbon\Carbon::parse(request('date_to'))->format('d.m.Y'),
                        request('date_to'),
                        false,
                    )
                : null,
        ]);
        $total = $items->total();
    @endphp

    <div class="mx-auto max-w-page px-page pt-8 pb-16">
        <x-ui.page-header title="Veranstaltungen entdecken">
            <x-slot:actions>
                <x-ui.button variant="secondary" :href="route('events.calendar')">
                    <x-slot:icon><x-icon.calendar /></x-slot:icon>
                    Zur Kalenderansicht
                </x-ui.button>
                <x-ui.button variant="link" :href="route('login')">
                    <x-slot:icon><x-icon.user /></x-slot:icon>
                    Anmelden für mehr Funktionen
                </x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.search-bar :action="route('events.index')" :filter-count="$filterCount">
            <x-ui.search-cell label="Kategorie" name="category" wide :options="['' => 'Alle Kategorien'] + $categories->pluck('name', 'id')->all()" />
            <x-ui.search-cell label="Ort" name="city">
                <input id="city" name="city" type="text" value="{{ request('city') }}" placeholder="Beliebig"
                    autocomplete="address-level2" class="ui-search-control">
            </x-ui.search-cell>
            <x-slot:filters>
                <fieldset class="m-0 min-w-0 border-0 p-0">
                    <legend class="mb-4 p-0 text-h3 text-ink">Zeitraum</legend>
                    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.field label="Von" name="date_from" type="date" :value="request('date_from')"
                            :error="false" />
                        <x-ui.field label="Bis" name="date_to" type="date" :value="request('date_to')"
                            :error="false" />
                    </div>
                </fieldset>
            </x-slot:filters>
            @if ($activeFilters || request()->filled('search'))
                <x-slot:active>
                    {{ implode(' · ', $activeFilters) }}@if ($activeFilters)
                        ·
                    @endif
                    <a href="{{ route('events.index') }}"
                        class="font-medium text-ink underline underline-offset-[3px]">Filter zurücksetzen</a>
                </x-slot:active>
            @endif
        </x-ui.search-bar>

        @if ($total > 0)
            <p class="mt-8 mb-4 text-base text-ink-soft">
                @if ($total === 1)
                    1 Veranstaltung gefunden.
                @else
                    <span class="tabular-nums">{{ $total }}</span> Veranstaltungen gefunden.
                @endif
            </p>
        @endif

        @if ($items->isEmpty())
            <x-ui.empty class="mt-8" title="Keine Veranstaltungen gefunden" :action-href="route('events.index')"
                action-label="Filter zurücksetzen">
                Passen Sie Ihre Suchkriterien an oder schauen Sie später wieder vorbei.
            </x-ui.empty>
        @else
            <x-ui.row-list>
                @foreach ($items as $itemData)
                    @if ($itemData['type'] === 'event')
                        <x-ui.event-row :event="$itemData['item']" heading="h2" />
                    @else
                        @php
                            $series = $itemData['item'];
                            $firstEvent = $series->events->first();
                            // Eine eigene Seite für Reihen gibt es nicht – Link auf den ersten Termin der Reihe
                            $seriesUrl = $firstEvent ? route('events.show', $firstEvent->slug) : route('events.index');
                        @endphp
                        <x-ui.row>
                            @if ($firstEvent)
                                <x-slot:lead>
                                    <x-ui.date-block :date="$firstEvent->start_date" />
                                </x-slot:lead>
                            @endif
                            <span
                                class="text-small text-muted">Veranstaltungsreihe{{ $series->category ? ' · ' . $series->category->name : '' }}</span>
                            <h2 class="m-0 text-h3 text-ink">
                                <a href="{{ $seriesUrl }}"
                                    class="text-ink no-underline underline-offset-[3px] hover:underline">{{ $series->title }}</a>
                            </h2>
                            <x-slot:meta>
                                <span class="text-ui font-medium tabular-nums">{{ $series->events->count() }}
                                    Termine</span>
                                @if ($firstEvent && $firstEvent->venue_city)
                                    <span class="text-small text-muted">{{ $firstEvent->venue_city }}</span>
                                @endif
                            </x-slot:meta>
                            <x-slot:action>
                                <x-ui.button variant="secondary" :href="$seriesUrl" full>Reihe ansehen</x-ui.button>
                            </x-slot:action>
                        </x-ui.row>
                    @endif
                @endforeach
            </x-ui.row-list>

            <x-ui.pagination :paginator="$items" />

            <!-- ItemList Schema for Events -->
            @if ($items->isNotEmpty())
                @php
                    $itemListSchema = [
                        '@context' => 'https://schema.org',
                        '@type' => 'ItemList',
                        'itemListElement' => [],
                    ];

                    foreach ($items as $index => $itemData) {
                        if ($itemData['type'] === 'event') {
                            $event = $itemData['item'];
                            $itemListSchema['itemListElement'][] = [
                                '@type' => 'ListItem',
                                'position' => $index + 1,
                                'url' => route('events.show', $event->slug),
                                'name' => $event->title,
                            ];
                        }
                    }
                @endphp
                <script type="application/ld+json">
                {!! json_encode($itemListSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
                </script>
            @endif
        @endif
    </div>
</x-layouts.public>
