<x-layouts.app title="Audit-Logs" :header="false">
    <x-ui.page-header title="Audit-Logs" subtitle="System-Protokolle und Audit-Trail">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.audit-logs.export', request()->query())">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                CSV exportieren
            </x-ui.button>
            <x-ui.button variant="link" x-data x-on:click="$dispatch('open-modal', 'clear-logs')">
                <x-slot:icon><x-icon.trash /></x-slot:icon>
                Alte Logs löschen
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="GET"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_9.5rem_9.5rem_minmax(0,1.2fr)_auto]">
        <x-ui.select label="Aktion" name="action" :old="false">
            <option value="">Alle Aktionen</option>
            @foreach ($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>
                    {{ \App\Support\AdminLabels::auditAction($action) }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.select label="Modell" name="auditable_type" :old="false">
            <option value="">Alle Modelle</option>
            @foreach ($modelTypes as $type)
                <option value="{{ $type }}" @selected(request('auditable_type') === $type)>
                    {{ \App\Support\AdminLabels::auditModel($type) }}</option>
            @endforeach
        </x-ui.select>
        <x-ui.field label="Von" name="from" type="date" :old="false" :value="request('from')" />
        <x-ui.field label="Bis" name="to" type="date" :old="false" :value="request('to')" />
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')" placeholder="Beschreibung" />
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button type="submit" variant="secondary" size="lg">Filtern</x-ui.button>
            @if (request()->anyFilled(['action', 'auditable_type', 'from', 'to', 'search']))
                <x-ui.button variant="link" :href="route('admin.audit-logs.index')">Zurücksetzen</x-ui.button>
            @endif
        </div>
    </form>

    <x-ui.table label="Audit-Logs" dense>
        <thead>
            <tr>
                <th scope="col">Datum/Zeit</th>
                <th scope="col" class="max-sm:hidden">Benutzer</th>
                <th scope="col">Aktion</th>
                <th scope="col" class="max-sm:hidden">Modell</th>
                <th scope="col" class="max-sm:hidden">IP</th>
                <th scope="col" class="max-sm:hidden">Beschreibung</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                @php
                    $actionTone = \App\Support\Ui::auditActionTone($log->action);
                @endphp
                <tr>
                    <td class="tabular-nums whitespace-nowrap">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                    <td class="max-sm:hidden whitespace-nowrap">{{ $log->user?->fullName() ?? 'System' }}</td>
                    <td><x-ui.status
                            :tone="$actionTone">{{ \App\Support\AdminLabels::auditAction($log->action) }}</x-ui.status>
                    </td>
                    <td class="max-sm:hidden whitespace-nowrap">
                        {{ $log->auditable_type ? \App\Support\AdminLabels::auditModel($log->auditable_type) : '-' }}
                    </td>
                    <td class="max-sm:hidden tabular-nums whitespace-nowrap text-muted">{{ $log->ip_address }}</td>
                    <td class="max-sm:hidden max-w-[220px] truncate" title="{{ $log->description }}">
                        {{ $log->description ?? '-' }}
                    </td>
                    <td class="actions">
                        <x-ui.button variant="secondary" size="sm" :href="route('admin.audit-logs.show', $log)">Details</x-ui.button>
                        <x-ui.button variant="secondary" size="sm" type="submit" class="max-sm:hidden"
                            :form="'destroy-log-' . ($log->id)">Löschen</x-ui.button>
                        <form id="destroy-log-{{ $log->id }}"
                            action="{{ route('admin.audit-logs.destroy', $log) }}" method="POST" class="hidden"
                            onsubmit="return confirm('Sind Sie sicher?')">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="7" class="p-0">
                        <x-ui.empty :bordered="false">Keine Audit-Logs gefunden</x-ui.empty>
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
        <form id="clear-logs-form" action="{{ route('admin.audit-logs.clear') }}" method="POST">
            @csrf
            <x-ui.select label="Logs löschen, die älter sind als:" name="older_than" :old="false">
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
