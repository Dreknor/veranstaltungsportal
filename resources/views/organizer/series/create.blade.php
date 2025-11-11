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

                <!-- Kostenübersicht -->
                @if(isset($publishingCosts))
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">💰 Geschätzte Kosten</h3>

                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded dark:bg-blue-900/20">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="text-sm text-blue-800 dark:text-blue-200" id="series-cost-info">
                                        @if(count($publishingCosts['breakdown']) > 0)
                                            <p class="font-medium mb-2">Kosten pro Termin (geschätzt):</p>

                                            <div class="space-y-2 mb-3">
                                                @foreach($publishingCosts['breakdown'] as $item)
                                                    <div class="flex justify-between text-xs">
                                                        <span>{{ $item['label'] }}</span>
                                                        <span class="font-semibold">{{ number_format($item['amount'], 2, ',', '.') }} €</span>
                                                    </div>
                                                @endforeach
                                                <div class="border-t border-blue-200 pt-2">
                                                    <div class="flex justify-between font-semibold">
                                                        <span>Pro Termin (netto):</span>
                                                        <span>{{ number_format($publishingCosts['total'], 2, ',', '.') }} €</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <p class="font-medium mb-2">Ihre Plattformgebühren:</p>

                                            @if(isset($platformFeeInfo))
                                                <div class="bg-white border border-blue-200 rounded p-3 mb-3">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span class="font-semibold text-gray-900">{{ $platformFeeInfo['description'] }}</span>
                                                    </div>
                                                    <p class="text-xs text-gray-600">
                                                        @if($platformFeeInfo['type'] === 'percentage')
                                                            Bei einem Ticket-Umsatz von 100 € fallen {{ number_format($platformFeeInfo['percentage'], 2) }} € Gebühr an.
                                                        @else
                                                            Pro verkauftem Ticket/Buchung: {{ number_format($platformFeeInfo['fixed_amount'], 2, ',', '.') }} €
                                                        @endif
                                                    </p>
                                                </div>
                                            @endif

                                            <p class="text-xs mb-3">Die genaue Abrechnung erfolgt nach Event-Ende pro Termin basierend auf den tatsächlichen Buchungen.</p>
                                        @endif

                                        @if(count($publishingCosts['breakdown']) > 0)
                                        <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mt-3 dark:bg-yellow-900/20">
                                            <p class="font-semibold mb-1">📊 Hochrechnung für Serie:</p>
                                            <p class="text-xs" id="series-total-estimate">
                                                Bei <span id="event-count-display">0</span> Terminen:
                                                <strong id="series-total-amount">0,00 €</strong> (geschätzt, netto)
                                            </p>
                                            <p class="text-xs text-gray-600 mt-1">
                                                Die tatsächliche Abrechnung erfolgt nach Ende jedes einzelnen Termins basierend auf den realen Buchungen.
                                            </p>
                                        </div>
                                        @else
                                        <div class="bg-gray-50 border border-gray-200 rounded p-3 mt-3">
                                            <p class="text-xs text-gray-600">
                                                <strong>Hinweis:</strong> Die finale Rechnung hängt von den tatsächlichen Buchungen ab.
                                                Jeder Termin wird einzeln nach Ende des Events abgerechnet.
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

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

    @push('scripts')
    <script>
        // Update series cost estimate based on event count
        const recurrenceCountInput = document.querySelector('input[name="recurrence_count"]');
        const costPerEvent = {{ $publishingCosts['total'] ?? 0 }};

        function updateSeriesCostEstimate() {
            const eventCount = parseInt(recurrenceCountInput?.value || 0);
            const totalCost = eventCount * costPerEvent;

            document.getElementById('event-count-display').textContent = eventCount;
            document.getElementById('series-total-amount').textContent =
                new Intl.NumberFormat('de-DE', {
                    style: 'currency',
                    currency: 'EUR'
                }).format(totalCost);
        }

        if (recurrenceCountInput) {
            recurrenceCountInput.addEventListener('input', updateSeriesCostEstimate);
            // Initial update
            updateSeriesCostEstimate();
        }
    </script>
    @endpush
</x-layouts.app>

