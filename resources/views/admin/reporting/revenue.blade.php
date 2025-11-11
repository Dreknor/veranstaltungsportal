<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Umsatz-Report</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Finanzielle Übersicht und Umsatzanalyse</p>
    </div>

    <div class="space-y-6">
        <!-- Period Filter -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="p-6">
                <form method="GET" class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Zeitraum:</label>
                    <select name="period" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>Letzte 7 Tage</option>
                        <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>Letzte 30 Tage</option>
                        <option value="90days" {{ $period === '90days' ? 'selected' : '' }}>Letzte 90 Tage</option>
                        <option value="365days" {{ $period === '365days' ? 'selected' : '' }}>Letztes Jahr</option>
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Gesamt</option>
                    </select>
                    <a href="{{ route('admin.reporting.index', ['period' => $period]) }}" class="ml-auto text-blue-600 hover:text-blue-800 dark:text-blue-400">
                        ← Zurück zur Übersicht
                    </a>
                </form>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Zahlungsstatus</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Anzahl</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Summe</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($data['payment_status_breakdown'] as $status)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($status->payment_status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($status->payment_status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @endif">
                                            {{ ucfirst($status->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ number_format($status->count) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100">
                                        €{{ number_format($status->total, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Keine Daten verfügbar</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Zahlungsmethoden</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Methode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Anzahl</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Summe</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($data['payment_methods'] as $method)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ ucfirst($method->payment_method ?? 'Nicht angegeben') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ number_format($method->count) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100">
                                        €{{ number_format($method->total, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Keine Daten verfügbar</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Platform Fees -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Platform-Gebühren</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 dark:bg-blue-900/20 dark:border-blue-800">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Gesamte Platform-Gebühren (Featured Events)</p>
                        <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">€{{ number_format($data['platform_fees'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Category -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Umsatz nach Kategorie</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategorie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Umsatz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($data['revenue_by_category'] as $category)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $category->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100">
                                        €{{ number_format($category->bookings_sum_total_amount ?? 0, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Keine Daten verfügbar</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

