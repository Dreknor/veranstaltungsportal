<x-layouts.app title="Teilnehmer kontaktieren">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('organizer.events.edit', $event) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    ← Zurück zum Event
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Teilnehmer kontaktieren</h1>
                <p class="text-gray-600 mt-2">{{ $event->title }}</p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900">Diese Nachricht wird an {{ $attendeesCount }} Teilnehmer gesendet</p>
                        <p class="text-sm text-blue-700 mt-1">Alle Teilnehmer mit bestätigten Buchungen erhalten Ihre Nachricht per E-Mail.</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('organizer.events.attendees.contact.send', $event) }}" class="bg-white rounded-lg shadow-md p-6">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Betreff *</label>
                        <input type="text" id="subject" name="subject" required value="{{ old('subject') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="z.B. Wichtige Information zu Ihrer Buchung">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Nachricht *</label>
                        <textarea id="message" name="message" required rows="10"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Ihre Nachricht an die Teilnehmer...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Die Nachricht wird als Text-E-Mail versendet. HTML-Formatierung ist nicht möglich.</p>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-2">Vorschau</h3>
                        <p class="text-sm text-gray-600">Die E-Mail wird automatisch folgende Informationen enthalten:</p>
                        <ul class="mt-2 text-sm text-gray-600 list-disc list-inside space-y-1">
                            <li>Event-Titel: {{ $event->title }}</li>
                            <li>Event-Datum: {{ $event->start_date->format('d.m.Y H:i') }} Uhr</li>
                        </ul>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('organizer.events.edit', $event) }}"
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Abbrechen
                        </a>
                        <button type="submit"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold"
                                onclick="return confirm('Möchten Sie die Nachricht wirklich an {{ $attendeesCount }} Teilnehmer senden?')">
                            Nachricht senden
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

