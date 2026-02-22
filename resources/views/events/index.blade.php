<x-layouts.public title="Veranstaltungen - Fort- und Weiterbildungen entdecken">
    @push('meta')
        @php
            $breadcrumbs = [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Veranstaltungen', 'url' => route('events.index')],
            ];
            $description = 'Entdecken Sie Fort- und Weiterbildungen für evangelische Schulen und Bildungseinrichtungen. Filter nach Kategorie, Ort und Datum.';
            // Canonical immer auf Seite 1 (ohne ?page=N) setzen
            $canonicalUrl = route('events.index');
            $isFilteredPage = request()->hasAny(['search', 'category', 'city', 'date_from', 'date_to', 'type', 'page']);
        @endphp
        <x-meta-tags
            :title="'Veranstaltungen - Fort- und Weiterbildungen entdecken'"
            :description="$description"
            :breadcrumbs="$breadcrumbs"
            :canonical="$canonicalUrl"
        />
        @if($isFilteredPage)
            <meta name="robots" content="noindex, follow">
        @endif
    @endpush

    <div class="min-h-screen bg-gray-50">
        <!-- Header mit Suche und Filter -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Veranstaltungen entdecken</h1>
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        <x-icon.user class="w-5 h-5 inline-block mr-1" />
                        Anmelden für mehr Funktionen
                    </a>
                </div>

                <form method="GET" action="{{ route('events.index') }}" class="space-y-4">
                    <!-- Suchleiste -->
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Nach Events suchen..."
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <x-icon.search class="w-5 h-5 inline-block mr-2" />
                            Suchen
                        </button>
                    </div>

                    <!-- Filter -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategorie</label>
                            <select name="category" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Alle Kategorien</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stadt</label>
                            <input type="text" name="city" value="{{ request('city') }}"
                                   placeholder="Stadt"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Von</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bis</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex gap-2">
                            <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Filter zurücksetzen</a>
                        </div>
                        <a href="{{ route('events.calendar') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <x-icon.calendar class="w-4 h-4 inline-block" />
                            Zur Kalenderansicht
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Events Liste -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($items->isEmpty())
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <x-icon.calendar class="w-16 h-16 mx-auto" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Keine Veranstaltungen gefunden</h3>
                    <p class="text-gray-600">Passen Sie Ihre Suchkriterien an oder schauen Sie später wieder vorbei.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($items as $itemData)
                        @if($itemData['type'] === 'event')
                            @php $event = $itemData['item']; @endphp
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                <!-- Event Bild -->
                                <div class="aspect-w-16 aspect-h-9 bg-gray-200 relative">
                                    @if($event->featured_image)
                                        <img src="{{ Storage::url($event->featured_image) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                            <x-icon.calendar class="w-16 h-16 text-white opacity-50" />
                                        </div>
                                    @endif

                                    @if($event->is_featured)
                                        <span class="absolute top-2 right-2 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                            Featured
                                        </span>
                                    @endif
                                </div>

                                <!-- Event Details -->
                                <div class="p-5">
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                            {{ $event->category->name }}
                                        </span>
                                        <x-event-type-badge :event="$event" />
                                    </div>

                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                        <a href="{{ route('events.show', $event->slug) }}" class="hover:text-blue-600">
                                            {{ $event->title }}
                                        </a>
                                    </h3>

                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <x-icon.calendar class="w-4 h-4 mr-2" />
                                            @if($event->start_date->isSameDay($event->end_date))
                                                {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                                            @else
                                                {{ $event->start_date->format('d.m.Y') }} - {{ $event->end_date->format('d.m.Y') }}
                                            @endif
                                        </div>

                                        <div class="flex items-center text-sm text-gray-600">
                                            <x-icon.location class="w-4 h-4 mr-2" />
                                            {{ $event->venue_city }}
                                        </div>

                                        @php
                                            $minPrice = $event->getMinimumPrice();
                                        @endphp
                                        @if($minPrice)
                                            <div class="flex items-center text-sm text-gray-600">
                                                <x-icon.ticket class="w-4 h-4 mr-2" />
                                                ab {{ number_format($minPrice, 2, ',', '.') }} €
                                            </div>
                                        @endif
                                    </div>

                                    <a href="{{ route('events.show', $event->slug) }}"
                                       class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        Details ansehen
                                    </a>
                                </div>
                            </div>
                        @else
                            @php $series = $itemData['item']; @endphp
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition border-2 border-purple-200">
                                <!-- Series Bild -->
                                <div class="aspect-w-16 aspect-h-9 bg-gray-200 relative">
                                    <div class="w-full h-48 bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                                        <div class="text-center text-white">
                                            <x-icon.calendar class="w-12 h-12 mx-auto mb-2 opacity-75" />
                                            <div class="text-sm font-semibold">Veranstaltungsreihe</div>
                                        </div>
                                    </div>

                                    <span class="absolute top-2 left-2 bg-purple-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                        {{ $series->events->count() }} Termine
                                    </span>
                                </div>

                                <!-- Series Details -->
                                <div class="p-5">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              style="background-color: {{ $series->category->color }}20; color: {{ $series->category->color }}">
                                            {{ $series->category->name }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Reihe
                                        </span>
                                    </div>

                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                        <a href="{{ route('series.show', $series->id) }}" class="hover:text-purple-600">
                                            {{ $series->title }}
                                        </a>
                                    </h3>

                                    <div class="space-y-2 mb-4">
                                        @if($series->events->first())
                                            <div class="flex items-center text-sm text-gray-600">
                                                <x-icon.calendar class="w-4 h-4 mr-2" />
                                                Start: {{ $series->events->first()->start_date->format('d.m.Y') }}
                                            </div>
                                        @endif

                                        <div class="flex items-center text-sm text-gray-600">
                                            <x-icon.list class="w-4 h-4 mr-2" />
                                            {{ $series->events->count() }} Termine
                                        </div>

                                        @if($series->events->first() && $series->events->first()->venue_city)
                                            <div class="flex items-center text-sm text-gray-600">
                                                <x-icon.location class="w-4 h-4 mr-2" />
                                                {{ $series->events->first()->venue_city }}
                                            </div>
                                        @endif
                                    </div>

                                    <a href="{{ route('series.show', $series->id) }}"
                                       class="block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                        Reihe ansehen
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $items->links() }}
                </div>

                <!-- ItemList Schema for Events -->
                @if($items->isNotEmpty())
                @php
                    $itemListSchema = [
                        '@context' => 'https://schema.org',
                        '@type' => 'ItemList',
                        'itemListElement' => []
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
    </div>
</x-layouts.public>

