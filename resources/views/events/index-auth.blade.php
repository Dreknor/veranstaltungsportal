<x-layouts.app title="Veranstaltungen">
    <div class="min-h-screen bg-gray-50">
        <!-- Header mit Suche und Filter -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Veranstaltungen entdecken</h1>
                    <a href="{{ route('favorites.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        <x-icon.heart class="w-5 h-5 inline-block mr-1" />
                        Meine Favoriten
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
                                <div class="relative aspect-w-16 aspect-h-9 bg-gray-200">
                                    @if($event->featured_image)
                                        <img src="{{ Storage::url($event->featured_image) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                            <x-icon.calendar class="w-16 h-16 text-white opacity-50" />
                                        </div>
                                    @endif

                                    <!-- Featured Badge -->
                                    @if($event->is_featured)
                                        <span class="absolute top-2 left-2 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                            Featured
                                        </span>
                                    @endif

                                    <!-- Favorite Button -->
                                    <div class="absolute top-2 right-2">
                                        <button type="button"
                                                onclick="toggleFavorite({{ $event->id }}, this)"
                                                data-favorited="{{ auth()->user()->hasFavorited($event) ? 'true' : 'false' }}"
                                                class="p-2 rounded-full {{ auth()->user()->hasFavorited($event) ? 'bg-red-500' : 'bg-white' }} shadow-lg hover:scale-110 transition">
                                            <x-icon.heart class="w-5 h-5 {{ auth()->user()->hasFavorited($event) ? 'text-white' : 'text-gray-400' }}" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Event Details -->
                                <div class="p-5">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                            {{ $event->category->name }}
                                        </span>
                                    </div>

                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                        <a href="{{ route('events.show', $event->slug) }}" class="hover:text-blue-600">
                                            {{ $event->title }}
                                        </a>
                                    </h3>

                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <x-icon.calendar class="w-4 h-4 mr-2" />
                                            {{ $event->start_date->format('d.m.Y H:i') }} Uhr
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

                                    <div class="flex gap-2">
                                        <a href="{{ route('events.show', $event->slug) }}"
                                           class="flex-1 text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                            Details ansehen
                                        </a>
                                        @if($event->hasAvailableTickets())
                                            <a href="{{ route('bookings.create', $event) }}"
                                               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                                <x-icon.ticket class="w-5 h-5" />
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleFavorite(eventId, button) {
            const icon = button.querySelector('svg');

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
                if (data.favorited) {
                    button.className = 'p-2 rounded-full bg-red-500 shadow-lg hover:scale-110 transition';
                    icon.className = 'w-5 h-5 text-white';
                    button.setAttribute('data-favorited', 'true');
                } else {
                    button.className = 'p-2 rounded-full bg-white shadow-lg hover:scale-110 transition';
                    icon.className = 'w-5 h-5 text-gray-400';
                    button.setAttribute('data-favorited', 'false');
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

