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

                <!-- Kostenübersicht -->
                @if(isset($publishingCosts))
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Geschätzte Kosten pro Termin</h3>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="text-sm text-blue-800">
                                    @if(count($publishingCosts['breakdown']) > 0)
                                        <p class="font-medium mb-2">Kosten pro Termin der Reihe (geschätzt):</p>

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
                                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mt-3">
                                        <p class="font-semibold mb-1">📊 Hochrechnung für diese Serie:</p>
                                        <p class="text-xs">
                                            Bei {{ $series->events->count() }} Terminen:
                                            <strong>{{ number_format($series->events->count() * $publishingCosts['total'], 2, ',', '.') }} €</strong> (geschätzt, netto)
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
                @endif

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

