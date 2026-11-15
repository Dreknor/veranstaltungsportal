<x-layouts.app title="Veranstaltungen">
    @push('meta')
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

    <x-ui.page-header title="Veranstaltungen entdecken">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('events.calendar')">
                <x-slot:icon><x-icon.calendar /></x-slot:icon>
                Zur Kalenderansicht
            </x-ui.button>
            <x-ui.button variant="link" :href="route('favorites.index')">
                <x-slot:icon><x-icon.heart /></x-slot:icon>
                Meine Favoriten
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
                    <x-ui.field label="Von" name="date_from" type="date" :value="request('date_from')" :error="false" />
                    <x-ui.field label="Bis" name="date_to" type="date" :value="request('date_to')" :error="false" />
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
                    @php
                        $event = $itemData['item'];
                        $isFavorite = auth()->user()->hasFavorited($event);
                    @endphp
                    <x-ui.event-row :event="$event" heading="h2">
                        <button type="button" onclick="toggleFavorite({{ $event->id }}, this)"
                            data-favorited="{{ $isFavorite ? 'true' : 'false' }}"
                            aria-pressed="{{ $isFavorite ? 'true' : 'false' }}"
                            class="group inline-flex min-h-11 cursor-pointer items-center gap-2 self-start rounded-control border-0 bg-transparent p-0 text-small font-medium text-ink-soft underline-offset-[3px] hover:underline">
                            <x-icon.heart class="h-4 w-4 group-aria-pressed:fill-current" />
                            <span class="group-aria-pressed:hidden">Merken</span>
                            <span class="hidden group-aria-pressed:inline">Gemerkt</span>
                            <span class="sr-only">: {{ $event->title }}</span>
                        </button>
                    </x-ui.event-row>
                @endif
            @endforeach
        </x-ui.row-list>

        <x-ui.pagination :paginator="$items" />
    @endif

    @push('scripts')
        <script>
            function toggleFavorite(eventId, button) {
                fetch(`/events/${eventId}/favorite`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Darstellung (gefülltes Herz, „Gemerkt") folgt aria-pressed
                        if (data.favorited) {
                            button.setAttribute('data-favorited', 'true');
                            button.setAttribute('aria-pressed', 'true');
                        } else {
                            button.setAttribute('data-favorited', 'false');
                            button.setAttribute('aria-pressed', 'false');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
                    });
            }
        </script>
    @endpush
</x-layouts.app>
