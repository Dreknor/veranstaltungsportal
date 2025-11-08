<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Statistiken & Analytics
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

            <!-- Date Range Filter -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <form method="GET" action="{{ route('organizer.statistics.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Von</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bis</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Aktualisieren</button>
                </form>
            </div>

            <!-- Overview Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Events -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Gesamt Events</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalEvents }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-4 text-sm">
                        <span class="text-green-600 dark:text-green-400">{{ $publishedEvents }} veröffentlicht</span>
                        <span class="text-blue-600 dark:text-blue-400">{{ $upcomingEvents }} kommend</span>
                    </div>
                </div>

                <!-- Total Bookings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Gesamt Buchungen</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalBookings }}</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-4 text-sm">
                        <span class="text-green-600 dark:text-green-400">{{ $confirmedBookings }} bestätigt</span>
                        <span class="text-yellow-600 dark:text-yellow-400">{{ $pendingBookings }} ausstehend</span>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Gesamt Umsatz</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalRevenue, 2, ',', '.') }} €</p>
                        </div>
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-4 text-sm">
                        <span class="text-green-600 dark:text-green-400">{{ number_format($confirmedRevenue, 2, ',', '.') }} € bestätigt</span>
                    </div>
                </div>

                <!-- Tickets Sold -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Verkaufte Tickets</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $ticketsSold }}</p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        Ø {{ number_format($avgTicketPrice ?? 0, 2, ',', '.') }} € pro Ticket
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Conversion Rate -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Konversionsrate</h3>
                    <div class="flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-5xl font-bold text-blue-600 dark:text-blue-400">{{ $conversionRate }}%</p>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Bestätigte Buchungen von Gesamt</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Zahlungsstatus</h3>
                    <div class="space-y-3">
                        @foreach($paymentStats as $stat)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $stat->payment_status }}</span>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stat->count }} Buchungen</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ number_format($stat->total, 2, ',', '.') }} €</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Monthly Trend Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Monatlicher Trend</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Buchungen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Umsatz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($monthlyBookings as $month)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($month->month . '-01')->format('M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $month->count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($month->revenue, 2, ',', '.') }} €</td>
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

            <!-- Top Events -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- By Revenue -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top Events nach Umsatz</h3>
                    <div class="space-y-3">
                        @forelse($topEventsByRevenue->take(5) as $event)
                            <div class="flex items-center justify-between">
                                <a href="{{ route('organizer.statistics.event', $event) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline truncate flex-1">
                                    {{ $event->title }}
                                </a>
                                <span class="ml-2 text-sm font-bold text-gray-900 dark:text-gray-100">{{ number_format($event->bookings_sum_total_amount ?? 0, 2, ',', '.') }} €</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Keine Daten verfügbar</p>
                        @endforelse
                    </div>
                </div>

                <!-- By Attendees -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top Events nach Teilnehmern</h3>
                    <div class="space-y-3">
                        @forelse($topEventsByAttendees->take(5) as $event)
                            <div class="flex items-center justify-between">
                                <a href="{{ route('organizer.statistics.event', $event) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline truncate flex-1">
                                    {{ $event->title }}
                                </a>
                                <span class="ml-2 text-sm font-bold text-gray-900 dark:text-gray-100">{{ $event->bookings_count }} Buchungen</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Keine Daten verfügbar</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Events nach Kategorie</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @forelse($categoryStats as $stat)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $stat->category->name ?? 'Unbekannt' }}</p>
                            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stat->count }}</p>
                        </div>
                    @empty
                        <p class="col-span-full text-sm text-gray-500 dark:text-gray-400 text-center">Keine Daten verfügbar</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

