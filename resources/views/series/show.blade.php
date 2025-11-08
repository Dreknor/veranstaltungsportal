<x-layouts.app title="{{ $series->title }}">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Series Header -->
            <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full mb-3">
                            Veranstaltungsreihe
                        </span>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $series->title }}</h1>
                        @if($series->category)
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                {{ $series->category->name }}
                            </span>
                        @endif
                    </div>
                </div>

                @if($series->description)
                    <div class="prose max-w-none mt-6">
                        <p class="text-gray-700 text-lg">{{ $series->description }}</p>
                    </div>
                @endif

                <!-- Key Info -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center text-gray-700">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span><strong>{{ $series->events->count() }} Termine</strong></span>
                    </div>
                    @if($series->events->first())
                    <div class="flex items-center text-gray-700">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $series->events->first()->start_date->format('H:i') }} - {{ $series->events->first()->end_date->format('H:i') }} Uhr</span>
                    </div>
                    <div class="flex items-center text-gray-700">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $series->template_data['venue_city'] ?? 'N/A' }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Important Notice -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Wichtig:</strong> Dies ist eine Veranstaltungsreihe mit mehreren zusammenhängenden Terminen.
                            Die Buchung umfasst <strong>alle {{ $series->events->count() }} Termine</strong>.
                            Einzelne Termine können nicht separat gebucht werden.
                        </p>
                    </div>
                </div>
            </div>

            <!-- All Dates/Sessions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Alle Termine dieser Veranstaltungsreihe</h2>

                <div class="space-y-3">
                    @foreach($series->events->sortBy('series_position') as $event)
                        <div class="flex items-center p-4 border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0 w-16 text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $event->series_position }}</div>
                                <div class="text-xs text-gray-500">Termin</div>
                            </div>
                            <div class="flex-1 ml-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $event->start_date->format('d.m.Y') }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }} Uhr
                                    ({{ $event->start_date->diffInMinutes($event->end_date) }} Minuten)
                                </div>
                            </div>
                            <div class="text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Venue Information -->
            @if(isset($series->template_data['venue_name']))
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Veranstaltungsort</h2>
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <div class="font-semibold text-gray-900">{{ $series->template_data['venue_name'] }}</div>
                        <div class="text-gray-600 mt-1">
                            {{ $series->template_data['venue_address'] }}<br>
                            {{ $series->template_data['venue_postal_code'] }} {{ $series->template_data['venue_city'] }}<br>
                            {{ $series->template_data['venue_country'] }}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Booking Button -->
            <div class="bg-white rounded-lg shadow-md p-6 sticky bottom-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-600">Gesamte Veranstaltungsreihe buchen</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $series->events->count() }} Termine</div>
                    </div>
                    <a href="{{ route('series.book', $series) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Jetzt buchen
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>

