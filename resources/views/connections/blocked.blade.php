<x-layouts.app title="Blockierte Nutzer">
    <x-ui.page-header title="Blockierte Nutzer" subtitle="Verwalten Sie Ihre blockierten Nutzer"
        :back-href="route('connections.index')" back-label="Zurück zu Verbindungen" />

    @if ($blockedUsers->isEmpty())
        <x-ui.empty title="Keine blockierten Nutzer">Sie haben keine Nutzer blockiert.</x-ui.empty>
    @else
        <x-ui.row-list>
            @foreach ($blockedUsers as $connection)
                <x-ui.row>
                    <x-slot:lead>
                        <img src="{{ $connection->following->profilePhotoUrl() }}" alt="" class="h-14 w-14 rounded-full border border-line object-cover">
                    </x-slot:lead>
                    <span class="text-h3 text-ink">{{ $connection->following->fullName() }}</span>
                    <span class="text-small text-muted">{{ $connection->following->userTypeLabel() }}</span>
                    <x-slot:meta>
                        <span class="text-small text-muted">Blockiert {{ $connection->created_at->diffForHumans() }}</span>
                    </x-slot:meta>
                    <x-slot:action>
                        <form action="{{ route('connections.unblock', $connection->following) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="secondary" full
                                onclick="return confirm('Nutzer wirklich entsperren?')">Entsperren</x-ui.button>
                        </form>
                    </x-slot:action>
                </x-ui.row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-6">
            {{ $blockedUsers->links() }}
        </div>
    @endif
</x-layouts.app>
