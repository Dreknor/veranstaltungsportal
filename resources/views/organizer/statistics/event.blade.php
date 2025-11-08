<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Statistiken: {{ $event->title }}
            </h2>
            <a href="{{ route('organizer.statistics.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                ← Zurück zur Übersicht
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

            <!-- Overview Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Bookings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Gesamt Buchungen</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalBookings }}</p>
                    <div class="mt-4 flex gap-2 text-xs">
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded">{{ $confirmedBookings }} bestätigt</span>
                        <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded">{{ $pendingBookings }} ausstehend</span>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Gesamt Umsatz</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalRevenue, 2, ',', '.') }} €</p>
                    <div class="mt-4 text-xs text-gray-600 dark:text-gray-400">
                        {{ number_format($confirmedRevenue, 2, ',', '.') }} € bestätigt
                    </div>
                </div>

                <!-- Tickets Sold -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Verkaufte Tickets</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $ticketsSold }}</p>
                    @if($event->max_attendees)
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                                <span>Auslastung</span>
                                <span>{{ $capacityPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($capacityPercentage, 100) }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Check-in Rate -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Check-in Rate</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $checkInRate }}%</p>
                    <div class="mt-4 text-xs text-gray-600 dark:text-gray-400">
                        {{ $checkedInCount }} von {{ $ticketsSold }} eingecheckt
                    </div>
                </div>
            </div>

            <!-- Ticket Type Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Verkäufe nach Ticket-Typ</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ticket-Typ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Verkauft</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Umsatz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($ticketTypeStats as $stat)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $stat->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $stat->quantity_sold }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($stat->revenue, 2, ',', '.') }} €</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Keine Daten verfügbar</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daily Booking Trend -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Buchungen pro Tag</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Datum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Buchungen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Umsatz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($dailyBookings as $day)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $day->count }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($day->revenue, 2, ',', '.') }} €</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Keine Daten verfügbar</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Discount Code Usage -->
            @if($discountCodeStats->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rabattcode Nutzung</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Verwendungen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Gesamt Rabatt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($discountCodeStats as $stat)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-mono text-gray-900 dark:text-gray-100">{{ $stat->discountCode->code ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $stat->usage_count }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-red-600 dark:text-red-400">-{{ number_format($stat->total_discount, 2, ',', '.') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Export & Aktionen</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('organizer.bookings.export', ['event_id' => $event->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Teilnehmerliste exportieren (CSV)
                    </a>
                    <a href="{{ route('organizer.events.edit', $event) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Event bearbeiten
                    </a>
                    <a href="{{ route('organizer.bookings.index', ['event_id' => $event->id]) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Alle Buchungen anzeigen
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

