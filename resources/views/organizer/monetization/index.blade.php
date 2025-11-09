<x-layouts.app title="Monetarisierung">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Monetarisierung & Einnahmen</h1>
                <p class="text-gray-600 mt-2">Übersicht über Ihre Einnahmen und Auszahlungen</p>
            </div>

            <!-- Statistiken -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Gesamtumsatz</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalRevenue, 2, ',', '.') }} €</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">Aus allen Ticket-Verkäufen</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Plattformgebühren</p>
                            <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($totalFees, 2, ',', '.') }} €</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-full">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">{{ $platformFeePercentage }}% der Einnahmen</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Netto-Einnahmen</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($netRevenue, 2, ',', '.') }} €</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4">Nach Plattformgebühren</p>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('organizer.monetization.index') }}" class="border-b-2 border-blue-500 text-blue-600 px-1 pb-4 text-sm font-medium">
                        Übersicht
                    </a>
                    <a href="{{ route('organizer.monetization.earnings') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Einnahmen nach Event
                    </a>
                    <a href="{{ route('organizer.monetization.transactions') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Transaktionen
                    </a>
                    <a href="{{ route('organizer.monetization.payout-settings') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Auszahlungseinstellungen
                    </a>
                </nav>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Letzte Transaktionen</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buchung</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Umsatz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gebühr</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Netto</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentTransactions as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->created_at->format('d.m.Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <a href="{{ route('organizer.events.edit', $transaction->event) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ Str::limit($transaction->event->title, 40) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->booking->booking_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($transaction->booking->total, 2, ',', '.') }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        -{{ number_format($transaction->fee_amount, 2, ',', '.') }} €
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                        {{ number_format($transaction->booking->total - $transaction->fee_amount, 2, ',', '.') }} €
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="mt-2">Noch keine Transaktionen vorhanden</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentTransactions->count() > 0)
                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        <a href="{{ route('organizer.monetization.transactions') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Alle Transaktionen anzeigen →
                        </a>
                    </div>
                @endif
            </div>

            <!-- Info Box -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informationen zu Auszahlungen</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Auszahlungen erfolgen monatlich auf Ihr hinterlegtes Bankkonto. Stellen Sie sicher, dass Ihre Auszahlungsdaten korrekt sind.</p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('organizer.monetization.payout-settings') }}" class="text-sm font-medium text-blue-800 hover:text-blue-900">
                                Auszahlungseinstellungen verwalten →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

