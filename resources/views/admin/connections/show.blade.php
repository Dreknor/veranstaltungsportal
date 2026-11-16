<x-layouts.app title="Verbindungsdetails">
    @php
        $statusTone = ['accepted' => 'success', 'pending' => 'warning', 'blocked' => 'danger'];
        $statusLabel = ['accepted' => 'Akzeptiert', 'pending' => 'Ausstehend', 'blocked' => 'Blockiert'];
    @endphp

    <x-ui.page-header title="Verbindungsdetails" :back-href="route('admin.connections.index')" back-label="Zur Übersicht der Verbindungen" />

    <div class="grid gap-x-12 gap-y-10 lg:grid-cols-2">
        @foreach ([['Follower', $connection->follower], ['Folgt', $connection->following]] as [$heading, $person])
            <section aria-label="{{ $heading }}">
                <h2 class="m-0 mb-4 text-h3">{{ $heading }}</h2>
                <div class="mb-4 flex items-center gap-4">
                    <img src="{{ $person->profilePhotoUrl() }}" alt="" class="h-16 w-16 flex-none rounded-full">
                    <div class="min-w-0">
                        <span class="block text-h3">{{ $person->name }}</span>
                        <span class="block text-small text-muted">{{ $person->email }}</span>
                    </div>
                </div>
                <x-ui.dl layout="stack" class="!grid-cols-1">
                    <x-ui.dl-item label="Registriert seit" num>{{ $person->created_at->format('d.m.Y') }}</x-ui.dl-item>
                    @if ($person->is_organizer)
                        <x-ui.dl-item label="Rolle">Veranstalter</x-ui.dl-item>
                    @endif
                </x-ui.dl>
            </section>
        @endforeach
    </div>

    <section aria-labelledby="connection-details" class="mt-10 border-t border-line pt-8">
        <h2 id="connection-details" class="m-0 mb-4 text-h3">Verbindungsdetails</h2>
        <x-ui.dl layout="stack">
            <x-ui.dl-item label="Status">
                <x-ui.status :tone="$statusTone[$connection->status] ?? 'neutral'">{{ $statusLabel[$connection->status] ?? ucfirst($connection->status) }}</x-ui.status>
            </x-ui.dl-item>
            <x-ui.dl-item label="Erstellt am" num>{{ $connection->created_at->format('d.m.Y H:i') }}</x-ui.dl-item>
            @if ($connection->accepted_at)
                <x-ui.dl-item label="Akzeptiert am" num>{{ $connection->accepted_at->format('d.m.Y H:i') }}</x-ui.dl-item>
            @endif
        </x-ui.dl>
    </section>

    <section aria-labelledby="connection-actions" class="mt-10 border-t border-line pt-8">
        <h2 id="connection-actions" class="m-0 mb-4 text-h3">Aktionen</h2>

        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('admin.connections.update-status', $connection) }}" class="m-0">
                @csrf
                <input type="hidden" name="status" value="{{ $connection->status === 'accepted' ? 'blocked' : 'accepted' }}">
                <x-ui.button type="submit" variant="secondary">
                    {{ $connection->status === 'accepted' ? 'Blockieren' : 'Freigeben' }}
                </x-ui.button>
            </form>

            <form method="POST" action="{{ route('admin.connections.destroy', $connection) }}" class="m-0"
                onsubmit="return confirm('Sind Sie sicher, dass Sie diese Verbindung löschen möchten?')">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger">Verbindung löschen</x-ui.button>
            </form>
        </div>
    </section>
</x-layouts.app>
