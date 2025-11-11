<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Meine Events</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Verwalten Sie Einzelveranstaltungen und Veranstaltungsreihen</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('organizer.series.index') }}"
                   class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 flex items-center">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Eventreihen
                </a>
                <a href="{{ route('organizer.series.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex items-center">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Eventreihe erstellen
                </a>
                <a href="{{ route('organizer.events.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Einzelnes Event
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('organizer.events.index') }}"
                   class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                    Alle Events
                </a>
                <a href="{{ route('organizer.series.index') }}"
                   class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    Eventreihen
                </a>
            </nav>
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
                                <div class="flex gap-2">
                                    @if($event->is_cancelled)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-500 text-white">
                                            Abgesagt
                                        </span>
                                    @elseif($event->is_published)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Veröffentlicht
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Entwurf
                                        </span>
                                    @endif
                                </div>
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
                                @if($event->hasAttendees())
                                    <a href="{{ route('organizer.check-in.index', $event) }}"
                                       class="flex-1 px-3 py-2 text-center bg-purple-600 text-white rounded hover:bg-purple-700 text-sm">
                                        <i class="fas fa-qrcode mr-1"></i> Check-In
                                    </a>
                                @endif
                                @if(!$event->is_cancelled)
                                    <form action="{{ route('organizer.events.duplicate', $event) }}" method="POST"
                                          class="flex-1">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                                            Duplizieren
                                        </button>
                                    </form>

                                    @if($event->hasAttendees())
                                        <!-- Absagen Button wenn Teilnehmer vorhanden -->
                                        <button type="button" onclick="showCancelModal{{ $event->id }}()"
                                                class="flex-1 px-3 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm">
                                            Absagen
                                        </button>
                                    @else
                                        <!-- Löschen Button wenn keine Teilnehmer vorhanden -->
                                        <form action="{{ route('organizer.events.destroy', $event) }}" method="POST"
                                              class="flex-1" onsubmit="return confirm('Event wirklich löschen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-full px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                                                Löschen
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Modal für Event-Absage -->
                    @if($event->hasAttendees() && !$event->is_cancelled)
                        <div id="cancelModal{{ $event->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                <div class="mt-3">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Event absagen</h3>
                                    <p class="text-sm text-gray-600 mb-4">
                                        Dieses Event hat {{ $event->getAttendeesCount() }} Teilnehmer.
                                        Alle Teilnehmer werden per E-Mail über die Absage informiert.
                                    </p>
                                    <form action="{{ route('organizer.events.cancel', $event) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="cancellation_reason{{ $event->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                Grund der Absage (optional)
                                            </label>
                                            <textarea id="cancellation_reason{{ $event->id }}" name="cancellation_reason" rows="3"
                                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                      placeholder="Teilen Sie den Teilnehmern mit, warum das Event abgesagt wird..."></textarea>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" onclick="hideCancelModal{{ $event->id }}()"
                                                    class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                                Abbrechen
                                            </button>
                                            <button type="submit"
                                                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                Event absagen
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <script>
                            function showCancelModal{{ $event->id }}() {
                                document.getElementById('cancelModal{{ $event->id }}').classList.remove('hidden');
                            }
                            function hideCancelModal{{ $event->id }}() {
                                document.getElementById('cancelModal{{ $event->id }}').classList.add('hidden');
                            }
                        </script>
                    @endif
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
