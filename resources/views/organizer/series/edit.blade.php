<x-layouts.app title="Veranstaltungsreihe bearbeiten">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('organizer.series.show', $series) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Zurück zu Reihendetails
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Veranstaltungsreihe bearbeiten</h1>
                <p class="text-gray-600 mt-2">{{ $series->title }}</p>
            </div>

            <form method="POST" action="{{ route('organizer.series.update', $series) }}">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Grundinformationen</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Titel der Veranstaltungsreihe *</label>
                            <input type="text" name="title" id="title" required value="{{ old('title', $series->title) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Dieser Titel wird für alle Termine der Reihe verwendet</p>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Beschreibung</label>
                            <textarea name="description" id="description" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $series->description) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Beschreibung, die für alle Termine der Reihe gilt</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>

                    <div>
                        <label class="flex items-start">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $series->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 mt-1">
                            <span class="ml-3">
                                <span class="block text-sm font-medium text-gray-700">Reihe ist aktiv</span>
                                <span class="block text-xs text-gray-500 mt-1">Wenn deaktiviert, werden keine neuen Buchungen für Termine dieser Reihe mehr akzeptiert</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Hinweis:</strong> Änderungen am Titel und der Beschreibung werden NICHT automatisch auf bereits erstellte Termine übertragen.
                                Um einzelne Termine zu bearbeiten, nutzen Sie die Event-Verwaltung.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('organizer.series.show', $series) }}"
                       class="rounded-md bg-gray-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 transition">
                        Abbrechen
                    </a>
                    <button type="submit"
                            class="rounded-md bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Änderungen speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

