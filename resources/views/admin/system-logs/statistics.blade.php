<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">System Log Statistiken</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Übersicht und Analyse der System-Logs</p>
            </div>
            <a href="{{ route('admin.system-logs.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                ← Zurück zur Übersicht
            </a>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Logs gesamt</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Heute</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['today']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Letzte 7 Tage</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['week']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Levels Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Verteilung nach Log Level</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($stats['by_level'] as $level)
                        @php
                            $percentage = $stats['total'] > 0 ? ($level->count / $stats['total']) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($level->level_name === 'DEBUG') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                        @elseif($level->level_name === 'INFO') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($level->level_name === 'NOTICE') bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200
                                        @elseif($level->level_name === 'WARNING') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($level->level_name === 'ERROR') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                        @elseif(in_array($level->level_name, ['CRITICAL', 'ALERT', 'EMERGENCY'])) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                        @endif">
                                        {{ $level->level_name }}
                                    </span>
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ number_format($level->count) }} ({{ number_format($percentage, 1) }}%)
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full
                                    @if($level->level_name === 'DEBUG') bg-gray-500
                                    @elseif($level->level_name === 'INFO') bg-blue-500
                                    @elseif($level->level_name === 'NOTICE') bg-cyan-500
                                    @elseif($level->level_name === 'WARNING') bg-yellow-500
                                    @elseif($level->level_name === 'ERROR') bg-orange-500
                                    @elseif(in_array($level->level_name, ['CRITICAL', 'ALERT', 'EMERGENCY'])) bg-red-500
                                    @else bg-gray-500
                                    @endif"
                                    style="width: {{ $percentage }}%">
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">Keine Daten verfügbar</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Channels -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Top Channels</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Channel</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Anzahl</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Prozent</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($stats['by_channel'] as $channel)
                                @php
                                    $percentage = $stats['total'] > 0 ? ($channel->count / $stats['total']) * 100 : 0;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $channel->channel }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                        {{ number_format($channel->count) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right">
                                        {{ number_format($percentage, 1) }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Keine Daten verfügbar
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Errors -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Letzte Fehler (ERROR, CRITICAL, ALERT, EMERGENCY)</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($stats['recent_errors'] as $error)
                        <div class="border border-red-200 dark:border-red-800 rounded-lg p-4 bg-red-50 dark:bg-red-900/20">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            {{ $error->level_name }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-600 dark:text-gray-400">
                                            @php
                                                try {
                                                    echo \Carbon\Carbon::parse($error->datetime)->format('d.m.Y H:i:s');
                                                } catch (\Exception $e) {
                                                    $cleanDate = preg_replace('/:\d{4}$/', '', $error->datetime);
                                                    try {
                                                        echo \Carbon\Carbon::parse($cleanDate)->format('d.m.Y H:i:s');
                                                    } catch (\Exception $e2) {
                                                        echo htmlspecialchars($error->datetime);
                                                    }
                                                }
                                            @endphp
                                        </span>
                                        @if($error->channel)
                                            <span class="ml-2 text-xs text-gray-600 dark:text-gray-400">
                                                [{{ $error->channel }}]
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-900 dark:text-gray-100 line-clamp-2">
                                        {{ $error->message }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.system-logs.show', $error->id) }}" class="ml-4 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    Details →
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">Keine kritischen Fehler gefunden</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

