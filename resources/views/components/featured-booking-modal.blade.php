@props(['event'])

<!-- Featured Event Modal -->
<div x-data="{ open: false, durationType: 'weekly', customDays: 7, startDate: '{{ now()->format('Y-m-d') }}' }"
     @featured-modal-open.window="open = true"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="open = false"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6"
             @click.away="open = false">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">
                    📌 Event als Featured markieren
                </h3>
                <button @click="open = false"
                        class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Pricing Cards -->
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Zeitraum wählen:</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Daily -->
                    <label class="relative">
                        <input type="radio"
                               name="duration_type"
                               value="daily"
                               x-model="durationType"
                               class="peer sr-only">
                        <div class="border-2 rounded-lg p-4 cursor-pointer transition-all
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50
                                    hover:border-blue-300">
                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-600">1 Tag</div>
                                <div class="text-2xl font-bold text-gray-900 mt-2">
                                    {{ number_format(config('monetization.featured_event_rates.daily', 5.00), 2, ',', '.') }} €
                                </div>
                                <div class="text-xs text-gray-500 mt-1">pro Tag</div>
                            </div>
                        </div>
                    </label>

                    <!-- Weekly -->
                    <label class="relative">
                        <input type="radio"
                               name="duration_type"
                               value="weekly"
                               x-model="durationType"
                               class="peer sr-only">
                        <div class="border-2 rounded-lg p-4 cursor-pointer transition-all
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50
                                    hover:border-blue-300
                                    ring-2 ring-blue-400 ring-offset-2">
                            <div class="text-center">
                                <div class="text-xs text-blue-600 font-semibold mb-1">⭐ BELIEBT</div>
                                <div class="text-sm font-medium text-gray-600">7 Tage</div>
                                <div class="text-2xl font-bold text-gray-900 mt-2">
                                    {{ number_format(config('monetization.featured_event_rates.weekly', 25.00), 2, ',', '.') }} €
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ number_format(config('monetization.featured_event_rates.weekly', 25.00) / 7, 2, ',', '.') }} € / Tag</div>
                            </div>
                        </div>
                    </label>

                    <!-- Monthly -->
                    <label class="relative">
                        <input type="radio"
                               name="duration_type"
                               value="monthly"
                               x-model="durationType"
                               class="peer sr-only">
                        <div class="border-2 rounded-lg p-4 cursor-pointer transition-all
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50
                                    hover:border-blue-300">
                            <div class="text-center">
                                <div class="text-xs text-green-600 font-semibold mb-1">💰 SPAR-PAKET</div>
                                <div class="text-sm font-medium text-gray-600">30 Tage</div>
                                <div class="text-2xl font-bold text-gray-900 mt-2">
                                    {{ number_format(config('monetization.featured_event_rates.monthly', 80.00), 2, ',', '.') }} €
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ number_format(config('monetization.featured_event_rates.monthly', 80.00) / 30, 2, ',', '.') }} € / Tag</div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Custom Duration -->
                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="radio"
                               name="duration_type"
                               value="custom"
                               x-model="durationType"
                               class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2 text-sm font-medium text-gray-700">Benutzerdefiniert</span>
                    </label>
                    <div x-show="durationType === 'custom'" class="mt-2 ml-6">
                        <div class="flex items-center gap-2">
                            <input type="number"
                                   x-model="customDays"
                                   min="1"
                                   max="{{ config('monetization.featured_event_max_duration_days', 90) }}"
                                   class="w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-sm text-gray-600">Tage</span>
                            <span class="text-sm font-semibold text-gray-900">
                                = <span x-text="(customDays * {{ config('monetization.featured_event_rates.daily', 5.00) }}).toFixed(2)"></span> €
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Start Date -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Featured-Zeitraum startet am:
                </label>
                <input type="date"
                       x-model="startDate"
                       min="{{ now()->format('Y-m-d') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Summary -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-blue-900 mb-2">Zusammenfassung:</h4>
                <div class="space-y-1 text-sm text-blue-800">
                    <div class="flex justify-between">
                        <span>Dauer:</span>
                        <span class="font-medium" x-text="durationType === 'custom' ? customDays + ' Tage' : (durationType === 'daily' ? '1 Tag' : (durationType === 'weekly' ? '7 Tage' : '30 Tage'))"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Kosten (netto):</span>
                        <span class="font-medium" x-text="(() => {
                            const rates = {
                                'daily': {{ config('monetization.featured_event_rates.daily', 5.00) }},
                                'weekly': {{ config('monetization.featured_event_rates.weekly', 25.00) }},
                                'monthly': {{ config('monetization.featured_event_rates.monthly', 80.00) }},
                                'custom': customDays * {{ config('monetization.featured_event_rates.daily', 5.00) }}
                            };
                            return rates[durationType].toFixed(2) + ' €';
                        })()"></span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span>+ MwSt. (19%):</span>
                        <span x-text="(() => {
                            const rates = {
                                'daily': {{ config('monetization.featured_event_rates.daily', 5.00) }},
                                'weekly': {{ config('monetization.featured_event_rates.weekly', 25.00) }},
                                'monthly': {{ config('monetization.featured_event_rates.monthly', 80.00) }},
                                'custom': customDays * {{ config('monetization.featured_event_rates.daily', 5.00) }}
                            };
                            return (rates[durationType] * 0.19).toFixed(2) + ' €';
                        })()"></span>
                    </div>
                    <div class="flex justify-between font-bold text-base pt-2 border-t border-blue-200">
                        <span>Gesamt (brutto):</span>
                        <span x-text="(() => {
                            const rates = {
                                'daily': {{ config('monetization.featured_event_rates.daily', 5.00) }},
                                'weekly': {{ config('monetization.featured_event_rates.weekly', 25.00) }},
                                'monthly': {{ config('monetization.featured_event_rates.monthly', 80.00) }},
                                'custom': customDays * {{ config('monetization.featured_event_rates.daily', 5.00) }}
                            };
                            return (rates[durationType] * 1.19).toFixed(2) + ' €';
                        })()"></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="button"
                        @click="open = false"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Abbrechen
                </button>
                <button type="button"
                        @click="$dispatch('featured-booking-confirm', { durationType, customDays, startDate }); open = false;"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Jetzt buchen →
                </button>
            </div>

            <!-- Info -->
            <div class="mt-4 text-xs text-gray-500 text-center">
                Die Zahlung erfolgt nach Bestätigung. Sie werden zur Zahlungsseite weitergeleitet.
            </div>
        </div>
    </div>
</div>

