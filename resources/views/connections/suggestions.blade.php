<x-layouts.app title="Verbindungsvorschläge">
    <x-ui.page-header title="Verbindungsvorschläge" subtitle="Entdecken Sie Pädagogen mit ähnlichen Interessen"
        :back-href="route('connections.index')" back-label="Zurück zu Verbindungen" />

    @if ($suggestions->isEmpty())
        <x-ui.empty title="Keine Vorschläge verfügbar">
            Wir konnten keine passenden Verbindungsvorschläge für Sie finden.
            Versuchen Sie, Ihre Interessen in den Einstellungen zu aktualisieren.
            <x-slot:action>
                <div class="flex flex-wrap gap-3">
                    <x-ui.button :href="route('settings.interests.edit')">Interessen bearbeiten</x-ui.button>
                    <x-ui.button variant="secondary" :href="route('connections.search')">Nutzer suchen</x-ui.button>
                </div>
            </x-slot:action>
        </x-ui.empty>
    @else
        <x-ui.row-list>
            @foreach ($suggestions as $user)
                @php
                    $commonInterests = array_intersect(
                        auth()->user()->interested_category_ids ?? [],
                        $user->interested_category_ids ?? [],
                    );
                    $viewer = auth()->user();
                @endphp
                <x-ui.person-row :user="$user" bio-lines="3">
                    @if (!empty($commonInterests))
                        <span class="text-small text-muted">
                            Gemeinsame Interessen:
                            {{ collect($commonInterests)->map(fn($categoryId) => \App\Models\EventCategory::find($categoryId)?->name)->filter()->implode(', ') }}
                        </span>
                    @endif
                    <x-slot:action>
                        @if ($user->canBeViewedBy($viewer))
                            <x-ui.button variant="secondary" :href="route('users.show', $user)" full>Profil</x-ui.button>
                        @endif
                        <form action="{{ route('connections.send', $user) }}" method="POST">
                            @csrf
                            <x-ui.button type="submit" full>Verbinden</x-ui.button>
                        </form>
                    </x-slot:action>
                </x-ui.person-row>
            @endforeach
        </x-ui.row-list>
    @endif
</x-layouts.app>
