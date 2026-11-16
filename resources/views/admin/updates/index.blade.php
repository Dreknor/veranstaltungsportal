<x-layouts.app title="Updates" :header="false">
    @php
        $running = in_array($state['status'], ['queued', 'running'], true);
        $stateLabels = [
            'idle' => ['Noch kein Update ausgeführt', 'neutral'],
            'queued' => ['Update wird gestartet …', 'info'],
            'running' => ['Update läuft …', 'info'],
            'success' => ['Letztes Update erfolgreich', 'success'],
            'failed' => ['Letztes Update fehlgeschlagen', 'danger'],
        ];
        [$stateText, $stateTone] = $stateLabels[$state['status']] ?? $stateLabels['idle'];
    @endphp

    <x-ui.page-header title="Updates" :subtitle="'Anwendung aus dem Repository aktualisieren (' .
        config('updater.remote') .
        '/' .
        config('updater.branch') .
        ')'" />

    @if ($error)
        <x-ui.alert tone="danger" title="Versionsstand konnte nicht ermittelt werden" class="mb-8">
            <p class="m-0 text-small break-all">{{ $error }}</p>
        </x-ui.alert>
    @endif

    @if ($status)
        <x-ui.dl class="mb-8">
            <x-ui.dl-item label="Installierte Version">
                <span class="tabular-nums">{{ substr($status['current'], 0, 7) }}</span>
                <span class="block text-small text-muted tabular-nums">
                    {{ $status['branch'] }} ·
                    {{ \Illuminate\Support\Carbon::parse($status['current_date'])->format('d.m.Y H:i') }}
                </span>
            </x-ui.dl-item>
            <x-ui.dl-item label="Verfügbare Version">
                <span class="tabular-nums">{{ substr($status['target'], 0, 7) }}</span>
                <span class="mt-1 block">
                    @php
                        $upToDate = !$status['behind'] && !$status['ahead'] && !$status['dirty'];
                    @endphp
                    <x-ui.status :tone="$status['behind'] ? 'info' : ($upToDate ? 'success' : 'neutral')">
                        @if ($status['behind'])
                            {{ $status['behind'] }} {{ $status['behind'] === 1 ? 'neue Änderung' : 'neue Änderungen' }}
                        @elseif ($upToDate)
                            Auf dem neuesten Stand
                        @else
                            Keine neuen Änderungen im Repository
                        @endif
                    </x-ui.status>
                </span>
            </x-ui.dl-item>
            <x-ui.dl-item label="Status">
                <x-ui.status :tone="$stateTone">{{ $stateText }}</x-ui.status>
                @if (!empty($state['finished_at']))
                    <span class="block text-small text-muted tabular-nums">
                        {{ \Illuminate\Support\Carbon::parse($state['finished_at'])->format('d.m.Y H:i') }}
                    </span>
                @endif
            </x-ui.dl-item>
        </x-ui.dl>

        @if ($status['dirty'])
            <x-ui.alert tone="warning" title="Lokale Änderungen auf dem Server" class="mb-8">
                <p class="m-0">Das Update wird abgebrochen, solange diese Dateien verändert sind:</p>
                <ul class="m-0 mt-2 list-disc pl-5">
                    @foreach ($status['dirty'] as $file)
                        <li class="break-all">{{ $file }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        @if ($status['ahead'])
            <x-ui.alert tone="warning" class="mb-8">
                Die installierte Version enthält {{ $status['ahead'] }}
                {{ $status['ahead'] === 1 ? 'Commit' : 'Commits' }}, {{ $status['ahead'] === 1 ? 'der' : 'die' }}
                nicht im Repository {{ $status['ahead'] === 1 ? 'ist' : 'sind' }}. Ein Update ist nur per Fast-Forward
                möglich und schlägt in diesem Fall fehl.
            </x-ui.alert>
        @endif

        @if ($status['behind'])
            <x-ui.section title="Neue Änderungen" class="mb-12">
                <x-slot:actions>
                    @if ($enabled)
                        <form method="POST" action="{{ route('admin.updates.store') }}"
                            onsubmit="return confirm('Update jetzt installieren? Die Seite ist währenddessen im Wartungsmodus.')">
                            @csrf
                            <x-ui.button type="submit" :disabled="$running || $status['dirty']">
                                <x-slot:icon><x-icon.download /></x-slot:icon>
                                Jetzt aktualisieren
                            </x-ui.button>
                        </form>
                    @else
                        <span class="text-small text-muted">Updates über die Oberfläche sind deaktiviert
                            (UPDATER_ENABLED).</span>
                    @endif
                </x-slot:actions>

                <x-ui.row-list>
                    @foreach ($status['commits'] as $commit)
                        <li class="flex flex-col gap-1 px-6 py-3 sm:flex-row sm:items-center sm:gap-4">
                            <span class="text-small text-muted tabular-nums">{{ $commit['short'] }}</span>
                            <span class="flex-1">{{ $commit['subject'] }}</span>
                            <span class="text-small text-muted tabular-nums">
                                {{ $commit['author'] }} ·
                                {{ \Illuminate\Support\Carbon::parse($commit['date'])->format('d.m.Y') }}
                            </span>
                        </li>
                    @endforeach
                </x-ui.row-list>
            </x-ui.section>
        @endif
    @endif

    @if ($log !== '')
        <x-ui.section title="Protokoll des letzten Updates">
            @if ($running)
                <x-slot:actions>
                    <span id="update-running" class="text-small text-muted">Update läuft …</span>
                </x-slot:actions>
            @endif
            <pre id="update-log"
                class="m-0 max-h-[32rem] overflow-auto rounded-box bg-zone p-4 text-small whitespace-pre-wrap text-ink">{{ $log }}</pre>
        </x-ui.section>
    @endif

    @if ($running)
        @push('scripts')
            <script>
                (function() {
                    const logEl = document.getElementById('update-log');

                    async function poll() {
                        try {
                            const response = await fetch(@json(route('admin.updates.progress')), {
                                headers: {
                                    'Accept': 'application/json'
                                },
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
