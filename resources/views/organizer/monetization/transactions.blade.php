<x-layouts.app title="Transaktionen">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Transaktionshistorie</h1>
                <p class="text-gray-600 mt-2">Vollständige Übersicht aller Transaktionen</p>
            </div>

            <!-- Navigation Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('organizer.monetization.index') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Übersicht
                    </a>
                    <a href="{{ route('organizer.monetization.earnings') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Einnahmen nach Event
                    </a>
                    <a href="{{ route('organizer.monetization.transactions') }}" class="border-b-2 border-blue-500 text-blue-600 px-1 pb-4 text-sm font-medium">
                        Transaktionen
                    </a>
                    <a href="{{ route('organizer.monetization.payout-settings') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Auszahlungseinstellungen
                    </a>
                </nav>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="{{ route('organizer.monetization.transactions') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="from" class="block text-sm font-medium text-gray-700">Von Datum</label>
                        <input type="date" name="from" id="from" value="{{ request('from') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="to" class="block text-sm font-medium text-gray-700">Bis Datum</label>
                        <input type="date" name="to" id="to" value="{{ request('to') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Filtern
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buchungsnr.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kunde</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Betrag</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gebühr</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Netto</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('organizer.events.edit', $transaction->event) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            {{ Str::limit($transaction->event->title, 30) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="{{ route('organizer.bookings.show', $transaction->booking) }}" class="text-gray-700 hover:text-gray-900">
                                            {{ $transaction->booking->booking_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->booking->customer_name }}
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
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="mt-2">Keine Transaktionen gefunden</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

            <!-- Export Button -->
            @if($transactions->count() > 0)
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('organizer.monetization.transactions', array_merge(request()->all(), ['export' => 'csv'])) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Als CSV exportieren
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

