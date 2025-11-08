<x-layouts.app title="Neue Eventreihe erstellen">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <a href="{{ route('organizer.series.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Zurück zu Eventreihen
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-4">Neue Veranstaltungsreihe erstellen</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Erstellen Sie eine Veranstaltung mit mehreren zusammenhängenden Terminen (z.B. Kursreihen, Weiterbildungen, Workshop-Serien)</p>
        </div>

        <form method="POST" action="{{ route('organizer.series.store') }}" class="space-y-6">
                @csrf

                <!-- Grundinformationen -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Grundinformationen</h3>

                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titel der Reihe *</label>
                                <input type="text" name="title" id="title" required value="{{ old('title') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beschreibung</label>
                                <textarea name="description" id="description" rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ old('description') }}</textarea>
                            </div>

                            <div>
                                <label for="event_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategorie *</label>
                                <select name="event_category_id" id="event_category_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    <option value="">Bitte wählen...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('event_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Termine der Veranstaltungsreihe -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Termine der Veranstaltungsreihe</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            Definieren Sie die Termine Ihrer Veranstaltungsreihe. Sie können später weitere Termine hinzufügen oder einzelne Termine bearbeiten.
                        </p>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <strong>Tipp:</strong> Nach der Erstellung der Reihe können Sie die einzelnen Termine in der Event-Verwaltung individuell anpassen und weitere Termine hinzufügen.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="recurrence_count" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anzahl der Termine *</label>
                                <input type="number" name="recurrence_count" id="recurrence_count" min="1" max="50" value="{{ old('recurrence_count', 5) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <p class="mt-1 text-xs text-gray-500">Wie viele Termine sollen initial erstellt werden?</p>
                            </div>

                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Standard-Uhrzeit *</label>
                                <input type="time" name="start_time" id="start_time" required value="{{ old('start_time', '09:00') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <p class="mt-1 text-xs text-gray-500">Wird für alle Termine verwendet</p>
                            </div>

                            <div>
                                <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dauer (Minuten) *</label>
                                <input type="number" name="duration" id="duration" min="15" max="1440" required value="{{ old('duration', 120) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <p class="mt-1 text-xs text-gray-500">Dauer jedes Termins</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Veranstaltungsort -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Veranstaltungsort</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="venue_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Veranstaltungsort *</label>
                                <input type="text" name="venue_name" id="venue_name" required value="{{ old('venue_name') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            </div>

                            <div class="md:col-span-2">
                                <label for="venue_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse *</label>
                                <input type="text" name="venue_address" id="venue_address" required value="{{ old('venue_address') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            </div>

                            <div>
                                <label for="venue_postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PLZ *</label>
                                <input type="text" name="venue_postal_code" id="venue_postal_code" required value="{{ old('venue_postal_code') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            </div>

                            <div>
                                <label for="venue_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stadt *</label>
                                <input type="text" name="venue_city" id="venue_city" required value="{{ old('venue_city') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            </div>

                            <div class="md:col-span-2">
                                <label for="venue_country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Land *</label>
                                <input type="text" name="venue_country" id="venue_country" required value="{{ old('venue_country', 'Deutschland') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Optionen -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Veröffentlichung</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="flex items-start">
                                    <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 mt-1">
                                    <span class="ml-3">
                                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alle Termine sofort veröffentlichen</span>
                                        <span class="block text-xs text-gray-500 mt-1">Wenn aktiviert, sind alle generierten Termine sofort für Teilnehmer sichtbar und buchbar</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('organizer.series.index') }}"
                       class="rounded-md bg-gray-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 transition">
                        Abbrechen
                    </a>
                    <button type="submit"
                            class="rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Veranstaltungsreihe mit Terminen erstellen
                    </button>
                </div>
            </form>
    </div>
</x-layouts.app>

