<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">System Log Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Detaillierte Ansicht des Log-Eintrags</p>
            </div>
            <a href="{{ route('admin.system-logs.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                ← Zurück zur Übersicht
            </a>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $log->id }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Datum/Zeit</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            @if($log->datetime)
                                @php
                                    try {
                                        $datetime = \Carbon\Carbon::parse($log->datetime);
                                        echo $datetime->format('d.m.Y H:i:s');
                                    } catch (\Exception $e) {
                                        $cleanDate = preg_replace('/:\d{4}$/', '', $log->datetime);
                                        try {
                                            echo \Carbon\Carbon::parse($cleanDate)->format('d.m.Y H:i:s');
                                        } catch (\Exception $e2) {
                                            echo htmlspecialchars($log->datetime);
                                        }
                                    }
                                @endphp
                            @else
                                -
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unix Time</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $log->unix_time }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Log Level</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($log->level_name === 'DEBUG') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @elseif($log->level_name === 'INFO') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($log->level_name === 'NOTICE') bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200
                                @elseif($log->level_name === 'WARNING') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($log->level_name === 'ERROR') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                @elseif(in_array($log->level_name, ['CRITICAL', 'ALERT', 'EMERGENCY'])) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                {{ $log->level_name }} (Level: {{ $log->level }})
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Channel</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $log->channel ?? '-' }}
                        </dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Nachricht</dt>
                        <dd class="mt-1">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded border border-gray-200 dark:border-gray-600">
                                <pre class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $log->message }}</pre>
                            </div>
                        </dd>
                    </div>

                    @if($log->context_decoded)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Context (Stack Trace)</dt>
                            <dd class="mt-1">
                                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded border border-red-200 dark:border-red-800 max-h-96 overflow-auto">
                                    @if(is_array($log->context_decoded))
                                        @if(isset($log->context_decoded['exception']))
                                            <div class="mb-4">
                                                <h4 class="font-semibold text-red-800 dark:text-red-300 mb-2">Exception</h4>
                                                <pre class="text-xs text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ is_array($log->context_decoded['exception']) ? json_encode($log->context_decoded['exception'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $log->context_decoded['exception'] }}</pre>
                                            </div>
                                        @endif
                                        <pre class="text-xs text-gray-900 dark:text-gray-100">{{ json_encode($log->context_decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @else
                                        <pre class="text-xs text-gray-900 dark:text-gray-100">{{ $log->context }}</pre>
                                    @endif
                                </div>
                            </dd>
                        </div>
                    @endif

                    @if($log->extra_decoded)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Extra</dt>
                            <dd class="mt-1">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded border border-gray-200 dark:border-gray-600">
                                    <pre class="text-xs text-gray-900 dark:text-gray-100">{{ json_encode($log->extra_decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </dd>
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Erstellt</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d.m.Y H:i:s') : '-' }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 flex justify-end space-x-2">
                    <form action="{{ route('admin.system-logs.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Log-Eintrag löschen möchten?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Eintrag löschen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

