<x-layouts.app title="System-Log-Details" :header="false">
    <x-ui.page-header title="System-Log-Details" subtitle="Detaillierte Ansicht des Log-Eintrags"
        :back-href="route('admin.system-logs.index')" back-label="Zurück zur Übersicht" />

    @php
        $levelTone = match (true) {
            in_array($log->level_name, ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY']) => 'danger',
            $log->level_name === 'WARNING' => 'warning',
            in_array($log->level_name, ['INFO', 'NOTICE']) => 'info',
            default => 'neutral',
        };
    @endphp

    <x-ui.dl>
        <x-ui.dl-item label="ID" num>{{ $log->id }}</x-ui.dl-item>
        <x-ui.dl-item label="Datum/Zeit" num>
            @if ($log->datetime)
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
        </x-ui.dl-item>
        <x-ui.dl-item label="Unix Time" num>{{ $log->unix_time }}</x-ui.dl-item>
        <x-ui.dl-item label="Log-Level">
            <x-ui.status :tone="$levelTone">{{ $log->level_name }} (Level: {{ $log->level }})</x-ui.status>
        </x-ui.dl-item>
        <x-ui.dl-item label="Channel">{{ $log->channel ?? '-' }}</x-ui.dl-item>
        <x-ui.dl-item label="Nachricht">
            <pre class="m-0 overflow-x-auto rounded-box bg-zone p-4 font-mono text-small whitespace-pre-wrap text-ink">{{ $log->message }}</pre>
        </x-ui.dl-item>
        @if ($log->context_decoded)
            <x-ui.dl-item label="Context (Stack Trace)">
                @if (is_array($log->context_decoded))
                    @if (isset($log->context_decoded['exception']))
                        <p class="m-0 mb-2 text-small font-medium">Exception</p>
                        <pre class="m-0 mb-4 max-h-96 overflow-x-auto overflow-y-auto rounded-box bg-zone p-4 font-mono text-small whitespace-pre-wrap text-ink">{{ is_array($log->context_decoded['exception']) ? json_encode($log->context_decoded['exception'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $log->context_decoded['exception'] }}</pre>
                    @endif
                    <pre class="m-0 max-h-96 overflow-x-auto overflow-y-auto rounded-box bg-zone p-4 font-mono text-small text-ink">{{ json_encode($log->context_decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                @else
                    <pre class="m-0 max-h-96 overflow-x-auto overflow-y-auto rounded-box bg-zone p-4 font-mono text-small text-ink">{{ $log->context }}</pre>
                @endif
            </x-ui.dl-item>
        @endif
        @if ($log->extra_decoded)
            <x-ui.dl-item label="Extra">
                <pre class="m-0 overflow-x-auto rounded-box bg-zone p-4 font-mono text-small text-ink">{{ json_encode($log->extra_decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </x-ui.dl-item>
        @endif
        <x-ui.dl-item label="Erstellt" num>
            {{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d.m.Y H:i:s') : '-' }}
        </x-ui.dl-item>
    </x-ui.dl>

    <div class="mt-8 flex justify-end">
        <form action="{{ route('admin.system-logs.destroy', $log->id) }}" method="POST"
            onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Log-Eintrag löschen möchten?')">
            @csrf
            @method('DELETE')
            <x-ui.button variant="danger" type="submit">Eintrag löschen</x-ui.button>
        </form>
    </div>
</x-layouts.app>
