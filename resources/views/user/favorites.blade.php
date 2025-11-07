<x-layouts.app title="Meine Favoriten">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Meine Favoriten</h1>
                <p class="text-gray-600 mt-2">Ihre gespeicherten Fortbildungen und Veranstaltungen</p>
            </div>

            @if($favoriteEvents->count() > 0)
                <!-- Events Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($favoriteEvents as $event)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                            @if($event->featured_image)
                                <div class="h-48 overflow-hidden">
                                    <img src="{{ Storage::url($event->featured_image) }}"
                                         alt="{{ $event->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                </div>
                            @else
                                <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <x-icon.academic class="w-20 h-20 text-white opacity-50" />
                                </div>
                            @endif

                            <div class="p-6">
                                <!-- Category Badge -->
                                @if($event->category)
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-3"
                                          style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                        {{ $event->category->name }}
                                    </span>
                                @endif

                                <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2">
                                    {{ $event->title }}
                                </h3>

                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    {{ $event->description }}
                                </p>

                                <!-- Event Info -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <x-icon.calendar class="w-4 h-4 mr-2" />
                                        {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                                    </div>

                                    <div class="flex items-center text-sm text-gray-600">
                                        <x-icon.location class="w-4 h-4 mr-2" />
                                        {{ $event->venue_city }}
                                    </div>

                                    @if($event->price_from)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <x-icon.currency class="w-4 h-4 mr-2" />
                                            ab {{ number_format($event->price_from, 2, ',', '.') }} €
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('events.show', $event->slug) }}"
                                       class="flex-1 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                        Details ansehen
                                    </a>

                                    <button onclick="toggleFavorite({{ $event->id }})"
                                            class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
                                        <x-icon.heart class="w-5 h-5" fill="currentColor" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $favoriteEvents->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <x-icon.heart class="w-24 h-24 text-gray-300 mx-auto mb-4" />
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Noch keine Favoriten</h2>
                    <p class="text-gray-600 mb-6">
                        Entdecken Sie spannende Fortbildungen und speichern Sie Ihre Favoriten
                    </p>
                    <a href="{{ route('events.index') }}"
                       class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Fortbildungen entdecken
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleFavorite(eventId) {
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
                if (data.favorited === false) {
                    // Reload page to update the list
                    window.location.reload();
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

