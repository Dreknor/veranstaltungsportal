<x-layouts.app title="Mein Dashboard">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Willkommen zurück, {{ $user->name }}!</h1>
                <p class="text-gray-600 mt-2">Verwalten Sie Ihre Buchungen und Fortbildungen</p>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Schnellaktionen</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <a href="{{ route('events.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <x-icon.search class="w-8 h-8 text-blue-600 mr-3" />
                        <div>
                            <h3 class="font-semibold text-gray-900">Fortbildungen finden</h3>
                            <p class="text-sm text-gray-600">Neue Veranstaltungen entdecken</p>
                        </div>
                    </a>

                    <a href="{{ route('user.bookings') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <x-icon.ticket class="w-8 h-8 text-green-600 mr-3" />
                        <div>
                            <h3 class="font-semibold text-gray-900">Meine Buchungen</h3>
                            <p class="text-sm text-gray-600">Alle Buchungen ansehen</p>
                        </div>
                    </a>

                    <a href="{{ route('connections.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition relative">
                        <svg class="w-8 h-8 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold text-gray-900">Netzwerk</h3>
                            <p class="text-sm text-gray-600">Mit Pädagogen vernetzen</p>
                        </div>
                        @if($user->getPendingRequestsCount() > 0)
                            <span class="absolute top-2 right-2 px-2 py-1 text-xs font-bold rounded-full bg-green-500 text-white">
                                {{ $user->getPendingRequestsCount() }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('favorites.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <x-icon.heart class="w-8 h-8 text-red-600 mr-3" />
                        <div>
                            <h3 class="font-semibold text-gray-900">Favoriten</h3>
                            <p class="text-sm text-gray-600">Gespeicherte Veranstaltungen</p>
                        </div>
                    </a>

                    <a href="{{ route('user.statistics') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <x-icon.chart class="w-8 h-8 text-purple-600 mr-3" />
                        <div>
                            <h3 class="font-semibold text-gray-900">Statistiken</h3>
                            <p class="text-sm text-gray-600">Meine Aktivitäten</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Notifications Widget -->
                <div class="lg:col-span-1 bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">
                            <x-icon.bell class="inline w-5 h-5 mr-2" />
                            Benachrichtigungen
                        </h2>
                        @if($unreadNotificationsCount > 0)
                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-500 text-white">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </div>

                    @if($notifications->count() > 0)
                        <div class="space-y-3">
                            @foreach($notifications as $notification)
                                <div class="border-l-4 {{ $notification->read_at ? 'border-gray-300' : 'border-blue-500 bg-blue-50' }} rounded-r p-3">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $notification->data['title'] ?? 'Neue Benachrichtigung' }}
                                            </p>
                                            <p class="text-xs text-gray-600 mt-1">
                                                {{ $notification->data['message'] ?? '' }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        @if(!$notification->read_at)
                                            <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-xs">
                                                    Gelesen
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('notifications.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Alle Benachrichtigungen →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-icon.bell class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                            <p class="text-gray-500 text-sm">Keine Benachrichtigungen</p>
                        </div>
                    @endif
                </div>

                <!-- Upcoming Events - now 2 columns -->
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Anstehende Veranstaltungen</h2>
                        <a href="{{ route('user.events.upcoming') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Alle anzeigen →
                        </a>
                    </div>

                    @if($upcomingBookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingBookings as $booking)
                                @if($booking->event)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ $booking->event->title }}</h3>
                                            <div class="flex items-center text-sm text-gray-600 mt-2">
                                                <x-icon.calendar class="w-4 h-4 mr-1" />
                                                {{ $booking->event->start_date->format('d.m.Y H:i') }} Uhr
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600 mt-1">
                                                <x-icon.location class="w-4 h-4 mr-1" />
                                                {{ $booking->event->venue_city }}
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            {{ $booking->status }}
                                        </span>
                                    </div>
                                    <div class="mt-4 flex gap-2">
                                        <a href="{{ route('bookings.show', $booking->booking_number) }}"
                                           class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                            Details ansehen
                                        </a>
                                        <a href="{{ route('bookings.ticket', $booking->booking_number) }}"
                                           class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                                            Ticket herunterladen
                                        </a>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <x-icon.calendar class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <p class="text-gray-500">Keine anstehenden Veranstaltungen</p>
                            <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                                Fortbildungen entdecken
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
                <!-- Recent Bookings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Letzte Buchungen</h2>
                        <a href="{{ route('user.bookings') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Alle anzeigen →
                        </a>
                    </div>

                    @if($bookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($bookings->take(5) as $booking)
                                @if($booking->event)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ $booking->event->title }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Gebucht am {{ $booking->created_at->format('d.m.Y') }}
                                            </p>
                                            <p class="text-sm font-medium text-gray-900 mt-2">
                                                {{ number_format($booking->total, 2, ',', '.') }} €
                                            </p>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                                {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                                   ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $booking->status }}
                                            </span>
                                            <a href="{{ route('bookings.show', $booking->booking_number) }}"
                                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                Details →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <x-icon.ticket class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <p class="text-gray-500">Noch keine Buchungen</p>
                            <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                                Jetzt buchen
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recommended Events Section -->
        @if(isset($recommendedEvents) && $recommendedEvents->count() > 0)
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">💡 Empfohlene Fortbildungen</h2>
                    <a href="{{ route('settings.interests.edit') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Interessen anpassen
                    </a>
                </div>
                <p class="text-gray-600 mb-6">Basierend auf Ihren Interessen haben wir diese Veranstaltungen für Sie ausgewählt:</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recommendedEvents as $event)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <a href="{{ route('events.show', $event) }}">
                            @if($event->featured_image)
                                <img src="{{ Storage::url($event->featured_image) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                    <x-icon.calendar class="w-16 h-16 text-white opacity-50" />
                                </div>
                            @endif
                        </a>

                        <div class="p-4">
                            <div class="flex items-center mb-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                      style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                    {{ $event->category->name }}
                                </span>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">
                                <a href="{{ route('events.show', $event) }}" class="hover:text-blue-600">
                                    {{ $event->title }}
                                </a>
                            </h3>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <i class="far fa-calendar mr-2"></i>
                                {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mb-3">
                                <i class="far fa-map-marker-alt mr-2"></i>
                                {{ $event->isOnline() ? 'Online-Veranstaltung' : $event->venue_city }}
                            </div>
                            <a href="{{ route('events.show', $event->slug) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Mehr erfahren
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</x-layouts.app>

