<x-layouts.app title="Mein Dashboard">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Willkommen zurück, {{ $user->name }}!</h1>
                <p class="text-gray-600 mt-2">Verwalten Sie Ihre Buchungen und Fortbildungen</p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Alle Buchungen</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_bookings'] }}</p>
                        </div>
                        <x-icon.ticket class="w-10 h-10 text-blue-500" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Anstehend</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['upcoming_events'] }}</p>
                        </div>
                        <x-icon.calendar class="w-10 h-10 text-green-500" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Abgeschlossen</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['past_events'] }}</p>
                        </div>
                        <x-icon.check class="w-10 h-10 text-purple-500" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Investiert</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_spent'], 2, ',', '.') }} €</p>
                        </div>
                        <x-icon.currency class="w-10 h-10 text-yellow-500" />
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Schnellaktionen</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

                    <a href="{{ route('favorites.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <x-icon.heart class="w-8 h-8 text-red-600 mr-3" />
                        <div>
                            <h3 class="font-semibold text-gray-900">Favoriten</h3>
                            <p class="text-sm text-gray-600">Gespeicherte Veranstaltungen</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Upcoming Events -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Anstehende Veranstaltungen</h2>
                        <a href="{{ route('user.events.upcoming') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Alle anzeigen →
                        </a>
                    </div>

                    @if($upcomingBookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingBookings as $booking)
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
    </div>
</x-layouts.app>

