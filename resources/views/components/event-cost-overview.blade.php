@props(['costs', 'showDetails' => true])

<div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg p-6 shadow-sm">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="ml-4 flex-1">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">Kostenübersicht</h3>

            @if($showDetails && count($costs['breakdown']) > 0)
                <div class="space-y-3 mb-4">
                    @foreach($costs['breakdown'] as $item)
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900">{{ $item['label'] }}</span>
                                        @if(isset($item['status']) && $item['status'] === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Zahlung ausstehend
                                            </span>
                                        @elseif(isset($item['status']) && $item['status'] === 'paid')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Bezahlt
                                            </span>
                                        @elseif(isset($item['status']) && $item['status'] === 'estimated')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Geschätzt
                                            </span>
                                        @endif
                                    </div>
                                    @if(isset($item['description']))
                                        <p class="text-sm text-gray-600 mt-1">{{ $item['description'] }}</p>
                                    @endif
                                </div>
                                <div class="font-semibold text-gray-900 ml-4 whitespace-nowrap text-lg">
                                    {{ number_format($item['amount'], 2, ',', '.') }} €
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Total -->
            <div class="bg-white rounded-lg p-4 shadow-md border-2 border-blue-200">
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Zwischensumme (netto):</span>
                        <span class="font-semibold text-gray-900">{{ number_format($costs['total'], 2, ',', '.') }} €</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">MwSt. (19%):</span>
                        <span class="text-gray-700">{{ number_format($costs['total'] * 0.19, 2, ',', '.') }} €</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-blue-900 text-lg">Gesamtkosten (brutto):</span>
                            <span class="font-bold text-blue-900 text-2xl">{{ number_format($costs['total'] * 1.19, 2, ',', '.') }} €</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hints -->
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3 text-sm text-yellow-800">
                        <p class="font-medium">Wichtige Hinweise:</p>
                        <ul class="mt-1 list-disc list-inside space-y-1">
                            <li>Plattformgebühren sind Schätzungen basierend auf Ihren Ticket-Einstellungen</li>
                            <li>Die tatsächliche Abrechnung erfolgt nach Event-Ende</li>
                            @if($costs['featured_fees']['active'] ?? false)
                                <li class="font-semibold">Featured Event Gebühren müssen vor Veröffentlichung bezahlt werden</li>
                            @endif
                            <li>Sie erhalten eine detaillierte Rechnung nach Event-Abschluss</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

