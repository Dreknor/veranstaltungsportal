<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Verbindungen Statistiken
            </h2>
            <a href="{{ route('admin.connections.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Zurück zur Übersicht
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Period Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                <form method="GET" action="{{ route('admin.connections.statistics') }}" class="flex gap-4 items-end">
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

            <!-- Connections Over Time -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Verbindungen über Zeit</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Datum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Anzahl</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($connectionsByDay as $day)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $day->count }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Keine Daten für diesen Zeitraum.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($connectionsByDay->isNotEmpty())
                            <tfoot class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">Gesamt</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $connectionsByDay->sum('count') }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- By Status -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Nach Status</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($byStatus as $status)
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">
                                {{ ucfirst($status->status) }}
                            </div>
                            <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">
                                {{ $status->count }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Most Active Users -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Top 10 Aktivste Benutzer</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Rang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Benutzer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Verbindungen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($mostActiveUsers as $index => $item)
                                @if($item['user'])
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            #{{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <img src="{{ $item['user']->profilePhotoUrl() }}" alt="{{ $item['user']->name }}" class="w-10 h-10 rounded-full mr-3">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['user']->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $item['user']->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $item['count'] }}
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
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

