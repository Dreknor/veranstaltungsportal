<x-layouts.app title="System-Logs" :header="false">
    <x-ui.page-header title="System-Logs" subtitle="Anwendungs- und Fehler-Protokolle">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.log-notifications.index')">
                <x-slot:icon><x-icon.bell /></x-slot:icon>
                Benachrichtigungen
            </x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.system-logs.statistics')">
                Statistiken
            </x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.system-logs.export', request()->query())">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                Export CSV
            </x-ui.button>
            <x-ui.button variant="secondary" x-data x-on:click="$dispatch('open-modal', 'clear-logs')">
                <x-slot:icon><x-icon.trash /></x-slot:icon>
                Alte Logs löschen
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="GET" class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.select label="Log-Level" name="level" :old="false">
            <option value="">Alle Levels</option>
            @foreach ($levels as $level)
                <option value="{{ $level }}" @selected(request('level') === $level)>{{ $level }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.select label="Channel" name="channel" :old="false">
            <option value="">Alle Channels</option>
            @foreach ($channels as $channel)
                <option value="{{ $channel }}" @selected(request('channel') === $channel)>{{ $channel }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.field label="Von" name="from" type="date" :old="false" :value="request('from')" />
        <x-ui.field label="Bis" name="to" type="date" :old="false" :value="request('to')" />
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')" placeholder="Nachricht" />
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button type="submit" variant="secondary" size="lg">Filtern</x-ui.button>
            <x-ui.button variant="link" :href="route('admin.system-logs.index')">Zurücksetzen</x-ui.button>
        </div>
    </form>

    <x-ui.table label="System-Logs" dense>
        <thead>
            <tr>
                <th scope="col">Datum/Zeit</th>
                <th scope="col">Level</th>
                <th scope="col">Channel</th>
                <th scope="col">Nachricht</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                @php
                    $levelTone = \App\Support\Ui::logLevelTone($log->level_name);
                @endphp
                <tr>
                    <td class="tabular-nums whitespace-nowrap">
                        @if ($log->datetime)
                            @php
                                try {
                                    // Versuche verschiedene Formate zu parsen
                                    $datetime = \Carbon\Carbon::parse($log->datetime);
                                    echo $datetime->format('d.m.Y H:i:s');
                                } catch (\Exception $e) {
                                    // Wenn Parsing fehlschlägt, bereinige das Format
                                    $cleanDate = preg_replace('/:\d{4}$/', '', $log->datetime);
                                    try {
                                        echo \Carbon\Carbon::parse($cleanDate)->format('d.m.Y H:i:s');
                                    } catch (\Exception $e2) {
                                        // Fallback: Zeige Rohwert
                                        echo htmlspecialchars($log->datetime);
                                    }
                                }
                            @endphp
                        @else
                            -
                        @endif
                    </td>
                    <td><x-ui.status :tone="$levelTone">{{ $log->level_name }}</x-ui.status></td>
                    <td class="whitespace-nowrap">{{ $log->channel ?? '-' }}</td>
                    <td class="max-w-[320px] truncate" title="{{ $log->message }}">{{ $log->message }}</td>
                    <td class="actions">
                        <x-ui.button variant="secondary" size="sm" :href="route('admin.system-logs.show', $log->id)">Details</x-ui.button>
                        <x-ui.button variant="secondary" size="sm" type="submit" form="destroy-log-{{ $log->id }}">Löschen</x-ui.button>
                        <form id="destroy-log-{{ $log->id }}" action="{{ route('admin.system-logs.destroy', $log->id) }}" method="POST" class="hidden"
                            onsubmit="return confirm('Sind Sie sicher?')">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="5" class="p-0">
                        <x-ui.empty :bordered="false">Keine System-Logs gefunden</x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if ($logs->hasPages())
        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    @endif

    <x-ui.modal name="clear-logs" title="Alte Logs löschen">
        <form id="clear-logs-form" action="{{ route('admin.system-logs.clear') }}" method="POST">
            @csrf
            <x-ui.select label="Logs löschen, die älter sind als:" name="older_than" :old="false">
                <option value="7">7 Tage</option>
                <option value="14">14 Tage</option>
                <option value="30">30 Tage</option>
                <option value="60">60 Tage</option>
                <option value="90">90 Tage</option>
                <option value="180">180 Tage</option>
                <option value="365">1 Jahr</option>
            </x-ui.select>
        </form>
        <x-slot:footer>
            <x-ui.button variant="secondary" x-on:click="close()">Abbrechen</x-ui.button>
            <x-ui.button variant="danger" type="submit" form="clear-logs-form">Löschen</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</x-layouts.app>
