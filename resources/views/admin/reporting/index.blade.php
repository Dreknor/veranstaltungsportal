<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Reporting & Analytics</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Übersicht über System-Statistiken und Reports</p>
    </div>

    <div class="space-y-6">
            <!-- Period Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Zeitraum:</label>
                        <select name="period" onchange="this.form.submit()" class="rounded-md border-gray-300">
                            <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>Letzte 7 Tage</option>
                            <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>Letzte 30 Tage</option>
                            <option value="90days" {{ $period === '90days' ? 'selected' : '' }}>Letzte 90 Tage</option>
                            <option value="365days" {{ $period === '365days' ? 'selected' : '' }}>Letztes Jahr</option>
                            <option value="ytd" {{ $period === 'ytd' ? 'selected' : '' }}>Jahr bis heute</option>
                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Gesamt</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Benutzer (Gesamt)</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['total_users']) }}</p>
                                <p class="text-xs text-green-600 mt-1">+{{ number_format($metrics['new_users']) }} neu</p>
                            </div>
                            <div class="text-blue-500 text-3xl">👥</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Events (Gesamt)</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['total_events']) }}</p>
                                <p class="text-xs text-green-600 mt-1">+{{ number_format($metrics['new_events']) }} neu</p>
                            </div>
                            <div class="text-purple-500 text-3xl">📅</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Buchungen (Gesamt)</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['total_bookings']) }}</p>
                                <p class="text-xs text-green-600 mt-1">+{{ number_format($metrics['new_bookings']) }} neu</p>
                            </div>
                            <div class="text-green-500 text-3xl">🎫</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Umsatz (Gesamt)</p>
                                <p class="text-2xl font-bold text-gray-900">€{{ number_format($metrics['total_revenue'], 2, ',', '.') }}</p>
                                <p class="text-xs text-green-600 mt-1">+€{{ number_format($metrics['period_revenue'], 2, ',', '.') }}</p>
                            </div>
                            <div class="text-yellow-500 text-3xl">💰</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversion Funnel -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Conversion Funnel</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium">Event Views</span>
                                <span class="text-sm text-gray-600">{{ number_format($conversionFunnel['event_views']) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium">Buchungen gestartet</span>
                                <span class="text-sm text-gray-600">{{ number_format($conversionFunnel['bookings_started']) }} ({{ number_format($conversionFunnel['start_rate'], 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $conversionFunnel['start_rate'] }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium">Buchungen abgeschlossen</span>
                                <span class="text-sm text-gray-600">{{ number_format($conversionFunnel['bookings_completed']) }} ({{ number_format($conversionFunnel['completion_rate'], 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $conversionFunnel['completion_rate'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <a href="{{ route('admin.reporting.users') }}?period={{ $period }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6 text-center">
                        <div class="text-4xl mb-2">👥</div>
                        <h3 class="font-semibold text-lg">Benutzer-Report</h3>
                        <p class="text-sm text-gray-600 mt-1">Detaillierte Benutzerstatistiken</p>
                    </div>
                </a>

                <a href="{{ route('admin.reporting.events') }}?period={{ $period }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6 text-center">
                        <div class="text-4xl mb-2">📅</div>
                        <h3 class="font-semibold text-lg">Event-Report</h3>
                        <p class="text-sm text-gray-600 mt-1">Event-Performance & Trends</p>
                    </div>
                </a>

                <a href="{{ route('admin.reporting.revenue') }}?period={{ $period }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6 text-center">
                        <div class="text-4xl mb-2">💰</div>
                        <h3 class="font-semibold text-lg">Umsatz-Report</h3>
                        <p class="text-sm text-gray-600 mt-1">Finanzielle Übersicht</p>
                    </div>
                </a>
            </div>

            <!-- Top Events -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Top 10 Events (nach Umsatz)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buchungen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Umsatz</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($topEvents as $event)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('events.show', $event->slug) }}" class="text-blue-600 hover:underline">
                                                {{ $event->title }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $event->bookings_count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold">€{{ number_format($event->bookings_sum_total_amount ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Keine Daten verfügbar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Organizers -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Top 10 Veranstalter (nach Umsatz)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Veranstalter</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Events</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Umsatz</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($topOrganizers as $organizer)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $organizer->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $organizer->organized_events_count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold">€{{ number_format($organizer->organized_events_bookings_sum_total_amount ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Keine Daten verfügbar</td>
                                    </tr>
                                @endforelse
            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

