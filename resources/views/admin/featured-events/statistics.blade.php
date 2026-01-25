<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Featured Events Statistiken
            </h2>
            <a href="{{ route('admin.featured-events.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Zurück zur Übersicht
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Period Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                <form method="GET" action="{{ route('admin.featured-events.statistics') }}" class="flex gap-4 items-end">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zeitraum</label>
                        <select name="period" id="period" class="input" onchange="this.form.submit()">
                            <option value="7" {{ $period == 7 ? 'selected' : '' }}>Letzte 7 Tage</option>
                            <option value="30" {{ $period == 30 ? 'selected' : '' }}>Letzte 30 Tage</option>
                            <option value="90" {{ $period == 90 ? 'selected' : '' }}>Letzte 90 Tage</option>
                            <option value="365" {{ $period == 365 ? 'selected' : '' }}>Letztes Jahr</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Revenue Over Time -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Umsatz über Zeit</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Datum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Anzahl</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Umsatz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($revenueByDay as $day)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $day->count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($day->total, 2, ',', '.') }} €
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Keine Daten für diesen Zeitraum.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($revenueByDay->isNotEmpty())
                            <tfoot class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">Gesamt</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $revenueByDay->sum('count') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                        {{ number_format($revenueByDay->sum('total'), 2, ',', '.') }} €
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- By Duration Type -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Nach Zeitraum-Typ</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($byDuration as $duration)
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">
                                @if($duration->duration_type === 'daily')
                                    Täglich
                                @elseif($duration->duration_type === 'weekly')
                                    Wöchentlich
                                @else
                                    Monatlich
                                @endif
                            </div>
                            <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">
                                {{ $duration->count }}
                            </div>
                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ number_format($duration->total, 2, ',', '.') }} €
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Organizers -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Top 10 Events</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Rang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Veranstalter</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Anzahl</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Umsatz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($topOrganizers as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        #{{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        {{ Str::limit($item->event->title, 40) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $item->event->organizer->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $item->count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($item->total, 2, ',', '.') }} €
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Keine Daten für diesen Zeitraum.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

