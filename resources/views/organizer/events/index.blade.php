<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Meine Events</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Verwalten Sie Ihre Veranstaltungen</p>
            </div>
            <a href="{{ route('organizer.events.create') }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Neues Event erstellen
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Events Grid -->
        @if($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <!-- Event Image -->
                        @if($event->image)
                            <div class="h-48 overflow-hidden">
                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" 
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif

                        <!-- Event Details -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $event->category->name }}
                                </span>
                                @if($event->is_published)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Veröffentlicht
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Entwurf
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ Str::limit($event->title, 50) }}
                            </h3>

                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $event->start_date->format('d.m.Y H:i') }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $event->venue_city }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                    </svg>
                                    {{ $event->bookings->count() }} Buchungen
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('events.show', $event->slug) }}" target="_blank"
                                   class="flex-1 px-3 py-2 text-center bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-200 dark:hover:bg-gray-600 text-sm">
                                    Anzeigen
                                </a>
                                <a href="{{ route('organizer.events.edit', $event) }}"
                                   class="flex-1 px-3 py-2 text-center bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                    Bearbeiten
                                </a>
                                <form action="{{ route('organizer.events.destroy', $event) }}" method="POST" 
                                      class="flex-1" onsubmit="return confirm('Event wirklich löschen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                        Löschen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($events->hasPages())
                <div class="mt-8">
                    {{ $events->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Keine Events vorhanden</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Erstellen Sie Ihr erstes Event, um loszulegen.
                </p>
                <div class="mt-6">
                    <a href="{{ route('organizer.events.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Neues Event erstellen
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
