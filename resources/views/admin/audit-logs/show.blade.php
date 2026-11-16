<x-layouts.app title="Audit-Log-Details" :header="false">
    <x-ui.page-header title="Audit-Log-Details" subtitle="Detaillierte Ansicht des Log-Eintrags" :back-href="route('admin.audit-logs.index')"
        back-label="Zurück zur Übersicht" />

    @php
        $actionTone = \App\Support\Ui::auditActionTone($auditLog->action);
        $changes = $auditLog->getChangesSummary();
    @endphp

    <x-ui.dl>
        <x-ui.dl-item label="ID" num>{{ $auditLog->id }}</x-ui.dl-item>
        <x-ui.dl-item label="Datum/Zeit" num>{{ $auditLog->created_at->format('d.m.Y H:i:s') }}</x-ui.dl-item>
        <x-ui.dl-item label="Benutzer">
            @if ($auditLog->user)
                {{ $auditLog->user->fullName() }} ({{ $auditLog->user->email }})
            @else
                System
            @endif
        </x-ui.dl-item>
        <x-ui.dl-item label="Aktion"><x-ui.status
                :tone="$actionTone">{{ \App\Support\AdminLabels::auditAction($auditLog->action) }}</x-ui.status></x-ui.dl-item>
        <x-ui.dl-item label="Modell">
            {{ $auditLog->auditable_type ? \App\Support\AdminLabels::auditModel($auditLog->auditable_type) : '-' }}
            @if ($auditLog->auditable_id)
                (ID: {{ $auditLog->auditable_id }})
            @endif
        </x-ui.dl-item>
        <x-ui.dl-item label="IP-Adresse" num>{{ $auditLog->ip_address ?? '-' }}</x-ui.dl-item>
        <x-ui.dl-item label="Browser (User-Agent)"><span
                class="break-all">{{ $auditLog->user_agent ?? '-' }}</span></x-ui.dl-item>
        @if ($auditLog->description)
            <x-ui.dl-item label="Beschreibung">{{ $auditLog->description }}</x-ui.dl-item>
        @endif
        @if ($auditLog->old_values)
            <x-ui.dl-item label="Alte Werte">
                <pre class="m-0 rounded-box bg-zone p-4 text-small text-ink overflow-x-auto break-words whitespace-pre-wrap">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </x-ui.dl-item>
        @endif
        @if ($auditLog->new_values)
            <x-ui.dl-item label="Neue Werte">
                <pre class="m-0 rounded-box bg-zone p-4 text-small text-ink overflow-x-auto break-words whitespace-pre-wrap">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </x-ui.dl-item>
        @endif
    </x-ui.dl>

    @if (count($changes) > 0)
        @php
            $formatValue = fn($value) => match (true) {
                is_bool($value) => $value ? 'Ja' : 'Nein',
                $value === null => '–',
                is_array($value) => json_encode($value, JSON_UNESCAPED_UNICODE),
                default => (string) $value,
            };
        @endphp
        <x-ui.section title="Änderungen" class="mt-12">
            <x-ui.table label="Änderungen" dense>
                <thead>
                    <tr>
                        <th scope="col">Feld</th>
                        <th scope="col">Alt</th>
                        <th scope="col">Neu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($changes as $field => $change)
                        <tr>
                            <th scope="row">{{ $field }}</th>
                            <td class="break-all text-danger">
                                {{ $formatValue($change['old']) }}</td>
                            <td class="break-all text-success">
                                {{ $formatValue($change['new']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        </x-ui.section>
    @endif

    <div class="mt-8 flex justify-end">
        <form action="{{ route('admin.audit-logs.destroy', $auditLog) }}" method="POST"
            onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Log-Eintrag löschen möchten?')">
            @csrf
            @method('DELETE')
            <x-ui.button variant="danger" type="submit">Eintrag löschen</x-ui.button>
        </form>
    </div>
</x-layouts.app>
