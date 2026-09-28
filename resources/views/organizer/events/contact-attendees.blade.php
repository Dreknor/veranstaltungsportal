<x-layouts.app title="Teilnehmende kontaktieren">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4 sm:py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('organizer.events.edit', $event) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">← Zurück zum Event</a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 mt-3">Teilnehmende kontaktieren</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $event->title }} · {{ $event->start_date->format('d.m.Y H:i') }} Uhr</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 text-sm" role="status">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('organizer.events.attendees.contact.send', $event) }}"
                  class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6 space-y-6"
                  x-data="{ segment: '{{ old('segment', 'all') }}', counts: @js($segments->map(fn ($s) => $s['count'])) }">
                @csrf

                <fieldset>
                    <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Empfänger</legend>
                    <div class="space-y-2">
                        @foreach($segments as $key => $segment)
                            <label class="flex items-center justify-between gap-3 p-3 border-2 rounded-lg cursor-pointer transition
                                          border-gray-200 dark:border-gray-700 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/20
                                          {{ $segment['count'] === 0 ? 'opacity-60' : '' }}">
                                <span class="flex items-center gap-3">
                                    <input type="radio" name="segment" value="{{ $key }}" x-model="segment" class="text-blue-600" @checked(old('segment', 'all') === $key)>
                                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ $segment['label'] }}</span>
                                </span>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-300 shrink-0">{{ $segment['count'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    <label class="mt-3 flex items-start gap-3 text-sm text-gray-700 dark:text-gray-300">
                        <input type="hidden" name="include_attendees" value="0">
                        <input type="checkbox" name="include_attendees" value="1" class="mt-0.5 rounded border-gray-300" @checked(old('include_attendees', true))>
                        Auch eingetragene Teilnehmende mit eigener E-Mail-Adresse anschreiben (z. B. Kolleg:innen, für die gebucht wurde)
                    </label>
                </fieldset>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Betreff *</label>
                    <input type="text" id="subject" name="subject" required value="{{ old('subject') }}" maxlength="255"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 shadow-sm"
                           placeholder="z.B. Raumänderung für morgen">
                    @error('subject')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nachricht *</label>
                    <textarea id="message" name="message" required rows="10" maxlength="5000"
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 shadow-sm"
                              placeholder="Ihre Nachricht …">{{ old('message') }}</textarea>
                    @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Die Anrede („Hallo …“), Veranstaltungsdaten und ein Link zur Buchung werden automatisch ergänzt.
                        Antworten gehen direkt an die E-Mail-Adresse Ihrer Organisation.
                        Für Änderungen an Termin, Ort oder Online-Link ist keine Nachricht nötig – darüber informiert das Portal automatisch.
                    </p>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <a href="{{ route('organizer.events.edit', $event) }}"
                       class="text-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Abbrechen</a>
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold disabled:bg-gray-300"
                            :disabled="counts[segment] === 0"
                            @click="if (!confirm('Nachricht an ' + counts[segment] + ' Buchung(en) senden?')) $event.preventDefault()">
                        Nachricht senden (<span x-text="counts[segment]">{{ $attendeesCount }}</span> Buchungen)
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
