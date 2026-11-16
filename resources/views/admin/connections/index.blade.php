<x-layouts.app title="Verbindungen verwalten">
    @php
        $statusTone = ['accepted' => 'success', 'pending' => 'warning', 'blocked' => 'danger'];
        $statusLabel = ['accepted' => 'Akzeptiert', 'pending' => 'Ausstehend', 'blocked' => 'Blockiert'];
    @endphp

    <x-ui.page-header title="Verbindungen verwalten">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.connections.statistics')">
                <x-slot:icon><x-icon.chart /></x-slot:icon>
                Statistiken
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Statistics -->
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-ui.stat label="Gesamt" :value="$stats['total']" />
        <x-ui.stat label="Akzeptiert" :value="$stats['accepted']" />
        <x-ui.stat label="Ausstehend" :value="$stats['pending']" />
        <x-ui.stat label="Blockiert" :value="$stats['blocked']" />
        <x-ui.stat label="Aktivster Nutzer" :value="$stats['most_connected_user'] ? Str::limit($stats['most_connected_user']->name, 20) : '–'"
            class="[&>span:nth-child(2)]:truncate [&>span:nth-child(2)]:text-h3" />
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.connections.index') }}"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')" placeholder="Benutzer suchen" />
        <x-ui.select label="Status" name="status" :old="false" :selected="request('status')"
            :options="['' => 'Alle Status', 'pending' => 'Ausstehend', 'accepted' => 'Akzeptiert', 'blocked' => 'Blockiert']" />
        <div class="flex flex-wrap gap-3">
            <x-ui.button type="submit" variant="secondary" size="lg">Filtern</x-ui.button>
            @if (request()->hasAny(['search', 'status']))
                <x-ui.button variant="link" :href="route('admin.connections.index')">Zurücksetzen</x-ui.button>
            @endif
        </div>
    </form>

    <form id="bulkActionForm" method="POST" action="{{ route('admin.connections.bulk') }}">
        @csrf
        <div class="mb-4 flex flex-wrap items-end justify-between gap-x-6 gap-y-3">
            <h2 class="m-0 text-h3">Verbindungen ({{ $connections->total() }})</h2>
            <div class="flex flex-wrap items-end gap-3">
                <x-ui.select label="Sammelaktion" name="action" id="bulkAction" :old="false" class="min-w-52"
                    :options="['' => 'Sammelaktion wählen...', 'approve' => 'Freigeben', 'block' => 'Blockieren', 'delete' => 'Löschen']" />
                <x-ui.button type="submit" variant="secondary" size="lg" onclick="return confirm('Sind Sie sicher?')">Ausführen</x-ui.button>
            </div>
        </div>

        <x-ui.table label="Verbindungen">
            <thead>
                <tr>
                    <th scope="col">
                        <input type="checkbox" id="selectAll" class="ui-checkbox" aria-label="Alle Verbindungen auswählen">
                    </th>
                    <th scope="col">Follower</th>
                    <th scope="col">Folgt</th>
                    <th scope="col">Status</th>
                    <th scope="col">Erstellt</th>
                    <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($connections as $connection)
                    <tr>
                        <td>
                            <input type="checkbox" name="connection_ids[]" value="{{ $connection->id }}"
                                class="connection-checkbox ui-checkbox"
                                aria-label="Verbindung von {{ $connection->follower->name }} zu {{ $connection->following->name }} auswählen">
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $connection->follower->profilePhotoUrl() }}" alt="" class="h-10 w-10 flex-none rounded-full">
                                <div class="min-w-0">
                                    {{ $connection->follower->name }}
                                    <span class="block text-small text-muted">{{ $connection->follower->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $connection->following->profilePhotoUrl() }}" alt="" class="h-10 w-10 flex-none rounded-full">
                                <div class="min-w-0">
                                    {{ $connection->following->name }}
                                    <span class="block text-small text-muted">{{ $connection->following->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <x-ui.status :tone="$statusTone[$connection->status] ?? 'neutral'">{{ $statusLabel[$connection->status] ?? ucfirst($connection->status) }}</x-ui.status>
                        </td>
                        <td class="tabular-nums whitespace-nowrap">{{ $connection->created_at->format('d.m.Y H:i') }}</td>
                        <td class="actions">
                            <x-ui.button size="sm" variant="secondary" :href="route('admin.connections.show', $connection)">Details</x-ui.button>
                            <x-ui.button size="sm" variant="secondary" type="submit" :form="'delete-connection-' . $connection->id">Löschen</x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="6" class="p-0">
                            <x-ui.empty :bordered="false">Keine Verbindungen gefunden.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </form>

    {{-- Einzel-Löschformulare außerhalb des Sammelformulars (Formulare dürfen nicht geschachtelt sein) --}}
    @foreach ($connections as $connection)
        <form id="delete-connection-{{ $connection->id }}" method="POST"
            action="{{ route('admin.connections.destroy', $connection) }}" class="hidden"
            onsubmit="return confirm('Sind Sie sicher?')">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    @if ($connections->hasPages())
        <div class="mt-6">
            {{ $connections->links() }}
        </div>
    @endif

    @push('scripts')
        <script>
            document.getElementById('selectAll').addEventListener('change', function(e) {
                document.querySelectorAll('.connection-checkbox').forEach(checkbox => {
                    checkbox.checked = e.target.checked;
                });
            });
        </script>
    @endpush
</x-layouts.app>
