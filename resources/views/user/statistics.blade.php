<x-layouts.app title="Meine Statistiken">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Meine Fortbildungs-Statistiken</h1>
                <p class="text-gray-600 mt-2">Überblick über Ihre Weiterbildungsaktivitäten</p>
            </div>

            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Gesamt Buchungen</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_bookings'] }}</p>
                        </div>
                        <x-icon.ticket class="w-12 h-12 text-blue-500 opacity-50" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Teilgenommen</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_events_attended'] }}</p>
                        </div>
                        <x-icon.check class="w-12 h-12 text-green-500 opacity-50" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Gesamt Stunden</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_hours'] }}</p>
                        </div>
                        <x-icon.clock class="w-12 h-12 text-purple-500 opacity-50" />
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Investiert</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_spent'], 2, ',', '.') }} €</p>
                        </div>
                        <x-icon.currency class="w-12 h-12 text-yellow-500 opacity-50" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- This Year Stats -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Dieses Jahr ({{ now()->year }})</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b">
                            <span class="text-gray-600">Buchungen</span>
                            <span class="text-2xl font-bold text-gray-900">{{ $stats['bookings_this_year'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b">
                            <span class="text-gray-600">Teilgenommene Events</span>
                            <span class="text-2xl font-bold text-gray-900">{{ $stats['events_this_year'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b">
                            <span class="text-gray-600">Anstehende Events</span>
                            <span class="text-2xl font-bold text-gray-900">{{ $stats['upcoming_events'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-600">Abgegebene Bewertungen</span>
                            <span class="text-2xl font-bold text-gray-900">{{ $stats['reviews_count'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Financial Stats -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Finanzübersicht</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b">
                            <span class="text-gray-600">Gesamt investiert</span>
                            <span class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_spent'], 2, ',', '.') }} €</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b">
                            <span class="text-gray-600">Durchschnitt pro Buchung</span>
                            <span class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_booking_value'], 2, ',', '.') }} €</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b">
                            <span class="text-gray-600">Bestätigte Buchungen</span>
                            <span class="text-2xl font-bold text-green-600">{{ $stats['confirmed_bookings'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-600">Stornierte Buchungen</span>
                            <span class="text-2xl font-bold text-red-600">{{ $stats['cancelled_bookings'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Breakdown -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Events nach Kategorie</h2>
                @if($stats['events_by_category']->count() > 0)
                    <div class="space-y-3">
                        @foreach($stats['events_by_category'] as $category => $count)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $category }}</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $count }} Events</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full"
                                         style="width: {{ ($count / $stats['events_by_category']->sum()) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Noch keine Kategorien vorhanden</p>
                @endif
            </div>

            <!-- Monthly Activity Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Buchungen pro Monat ({{ now()->year }})</h2>
                <div class="grid grid-cols-12 gap-2 items-end h-64">
                    @foreach(range(1, 12) as $month)
                        @php
                            $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                            $bookingCount = $stats['bookings_by_month']->get($monthKey, 0);
                            $maxBookings = $stats['bookings_by_month']->max() ?: 1;
                            $heightPercent = ($bookingCount / $maxBookings) * 100;
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-full bg-blue-500 rounded-t hover:bg-blue-600 transition relative group"
                                 style="height: {{ $heightPercent }}%">
                                <div class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                    {{ $bookingCount }} Buchung{{ $bookingCount != 1 ? 'en' : '' }}
                                </div>
                            </div>
                            <span class="text-xs text-gray-600 mt-2">{{ \Carbon\Carbon::create()->month($month)->format('M') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex gap-4">
                <a href="{{ route('user.bookings') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Alle Buchungen ansehen
                </a>
                <a href="{{ route('user.events.past') }}" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Abgeschlossene Events
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>

