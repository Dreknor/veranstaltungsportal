<x-layouts.app>
    @php
        $running = in_array($state['status'], ['queued', 'running'], true);
        $stateLabels = [
            'idle' => ['Noch kein Update ausgeführt', 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200'],
            'queued' => ['Update wird gestartet …', 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'],
            'running' => ['Update läuft …', 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'],
            'success' => ['Letztes Update erfolgreich', 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'],
            'failed' => ['Letztes Update fehlgeschlagen', 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'],
        ];
        [$stateText, $stateClass] = $stateLabels[$state['status']] ?? $stateLabels['idle'];
    @endphp

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Updates</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">
            Anwendung aus dem Repository aktualisieren ({{ config('updater.remote') }}/{{ config('updater.branch') }})
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded dark:bg-green-900 dark:border-green-700 dark:text-green-200" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if($error)
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
            <p class="font-semibold">Versionsstand konnte nicht ermittelt werden</p>
            <p class="text-sm mt-1 font-mono break-all">{{ $error }}</p>
        </div>
    @endif

    <div class="space-y-6">
        @if($status)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Installierte Version</p>
                    <p class="text-2xl font-mono font-bold text-gray-800 dark:text-gray-100 mt-1">{{ substr($status['current'], 0, 7) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $status['branch'] }} · {{ \Illuminate\Support\Carbon::parse($status['current_date'])->format('d.m.Y H:i') }}
                    </p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Verfügbare Version</p>
                    <p class="text-2xl font-mono font-bold text-gray-800 dark:text-gray-100 mt-1">{{ substr($status['target'], 0, 7) }}</p>
                    <p class="text-sm mt-1 {{ $status['behind'] ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'text-green-600 dark:text-green-400' }}">
                        {{ $status['behind'] ? $status['behind'].' neue Änderung(en)' : 'Auf dem neuesten Stand' }}
                    </p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-medium {{ $stateClass }}">{{ $stateText }}</span>
                    @if(!empty($state['finished_at']))
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            {{ \Illuminate\Support\Carbon::parse($state['finished_at'])->format('d.m.Y H:i') }}
                        </p>
                    @endif
                </div>
            </div>

            @if($status['dirty'])
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-200">
                    <p class="font-semibold">Lokale Änderungen auf dem Server</p>
                    <p class="text-sm mt-1">Das Update wird abgebrochen, solange diese Dateien verändert sind:</p>
                    <ul class="text-sm font-mono mt-2 list-disc list-inside">
                        @foreach($status['dirty'] as $file)
                            <li>{{ $file }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($status['ahead'])
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-200">
                    Die installierte Version enthält {{ $status['ahead'] }} Commit(s), die nicht im Repository sind. Ein Update ist nur per Fast-Forward möglich und schlägt in diesem Fall fehl.
                </div>
            @endif

            @if($status['behind'])
                <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Neue Änderungen</h2>

                        @if($enabled)
                            <form method="POST" action="{{ route('admin.updates.store') }}"
                                  onsubmit="return confirm('Update jetzt installieren? Die Seite ist währenddessen im Wartungsmodus.')">
                                @csrf
                                <button type="submit" @disabled($running || $status['dirty'])
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <x-fas-cloud-arrow-down class="w-4 h-4 mr-2" />
                                    Jetzt aktualisieren
                                </button>
                            </form>
                        @else
                            <span class="text-sm text-gray-500 dark:text-gray-400">Updates über die Oberfläche sind deaktiviert (UPDATER_ENABLED).</span>
                        @endif
                    </div>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($status['commits'] as $commit)
                            <li class="px-6 py-3 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="font-mono text-sm text-gray-500 dark:text-gray-400">{{ $commit['short'] }}</span>
                                <span class="flex-1 text-gray-800 dark:text-gray-100">{{ $commit['subject'] }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $commit['author'] }} · {{ \Illuminate\Support\Carbon::parse($commit['date'])->format('d.m.Y') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

        @if($log !== '')
            <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Protokoll des letzten Updates</h2>
                    @if($running)
                        <span id="update-running" class="text-sm text-blue-600 dark:text-blue-400">Update läuft …</span>
                    @endif
                </div>
                <pre id="update-log" class="p-6 text-xs font-mono text-gray-100 bg-gray-900 overflow-auto max-h-[32rem] whitespace-pre-wrap sm:rounded-b-lg">{{ $log }}</pre>
            </div>
        @endif
    </div>

    @if($running)
        @push('scripts')
            <script>
                (function () {
                    const logEl = document.getElementById('update-log');

                    async function poll() {
                        try {
                            const response = await fetch(@json(route('admin.updates.progress')), {
                                headers: { 'Accept': 'application/json' },
                            });
                            if (response.ok) {
                                const data = await response.json();
                                if (logEl) {
                                    logEl.textContent = data.log;
                                    logEl.scrollTop = logEl.scrollHeight;
                                }
                                if (!data.running) {
                                    window.location.reload();
                                    return;
                                }
                            }
                        } catch (e) {
                            // Server startet evtl. gerade neu – beim nächsten Durchlauf erneut versuchen
                        }
                        setTimeout(poll, 2000);
                    }

                    setTimeout(poll, 1000);
                })();
            </script>
        @endpush
    @endif
</x-layouts.app>
