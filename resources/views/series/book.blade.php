<x-layouts.app title="Veranstaltungsreihe buchen - {{ $series->title }}">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-6">
                <a href="{{ route('series.show', $series) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Zurück zur Übersicht
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Veranstaltungsreihe buchen</h1>
                <p class="text-gray-600 mt-2">{{ $series->title }}</p>
            </div>

            <!-- Series Summary Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full mb-2">
                            Veranstaltungsreihe
                        </span>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $series->title }}</h2>
                        @if($series->category)
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full mt-2">
                                {{ $series->category->name }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Series Info -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <x-icon.calendar class="w-5 h-5 mr-2 text-blue-600" />
                        <span><strong>{{ $series->events->count() }} Termine</strong></span>
                    </div>
                    @if($series->events->first())
                        <div class="flex items-center">
                            <x-icon.clock class="w-5 h-5 mr-2 text-blue-600" />
                            <span>{{ $series->events->first()->start_date->format('H:i') }} - {{ $series->events->first()->end_date->format('H:i') }} Uhr</span>
                        </div>
                        <div class="flex items-center">
                            <x-icon.location class="w-5 h-5 mr-2 text-blue-600" />
                            <span>{{ $series->template_data['venue_city'] ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Start: {{ $series->events->first()->start_date->format('d.m.Y') }}</span>
                        </div>
                    @endif
                </div>

                <!-- All Dates Preview -->
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Alle Termine dieser Reihe:</h3>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($series->events->sortBy('series_position') as $event)
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg text-sm">
                                <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mr-3">
                                    {{ $event->series_position }}
                                </span>
                                <div class="flex-1">
                                    <span class="font-medium">{{ $event->start_date->format('d.m.Y') }}</span>
                                    <span class="text-gray-600 ml-2">{{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }} Uhr</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                            <strong>Wichtig:</strong> Mit dieser Buchung melden Sie sich für <strong>alle {{ $series->events->count() }} Termine</strong> an.
                            Die Teilnahme an allen Terminen ist verbindlich. Einzelne Termine können nicht separat gebucht oder storniert werden.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Buchungsinformationen</h2>

                <form method="POST" action="{{ route('series.book', $series) }}" class="space-y-6">
                    @csrf

                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Persönliche Daten</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Vorname *</label>
                                <input type="text" id="first_name" name="first_name" required
                                       value="{{ old('first_name', auth()->user()->first_name ?? '') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nachname *</label>
                                <input type="text" id="last_name" name="last_name" required
                                       value="{{ old('last_name', auth()->user()->last_name ?? '') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-Mail *</label>
                            <input type="email" id="email" name="email" required
                                   value="{{ old('email', auth()->user()->email ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon (optional)</label>
                            <input type="tel" id="phone" name="phone"
                                   value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Anmerkungen (optional)</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Haben Sie besondere Wünsche oder Fragen? Teilen Sie uns diese hier mit.</p>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="border-t pt-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms" name="terms" type="checkbox" required
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="font-medium text-gray-700">
                                    Ich akzeptiere die AGB und Datenschutzerklärung *
                                </label>
                                <p class="text-gray-500">
                                    Ich bestätige, dass ich an allen {{ $series->events->count() }} Terminen teilnehmen werde und die
                                    <a href="#" class="text-blue-600 hover:text-blue-800">Teilnahmebedingungen</a> akzeptiere.
                                </p>
                            </div>
                        </div>
                        @error('terms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-4">
                        <a href="{{ route('series.show', $series) }}"
                           class="flex-1 text-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Abbrechen
                        </a>
                        <button type="submit"
                                class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            Verbindlich buchen ({{ $series->events->count() }} Termine)
                        </button>
                    </div>
                </form>
            </div>

            <!-- Venue Information -->
            @if(isset($series->template_data['venue_name']))
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Veranstaltungsort</h2>
                <div class="flex items-start">
                    <x-icon.location class="w-6 h-6 text-blue-600 mr-3 mt-1" />
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

        </div>
    </div>
</x-layouts.app>

