<x-layouts.app title="Verbindungsvorschläge">
    <x-ui.page-header title="Verbindungsvorschläge" subtitle="Entdecken Sie Pädagogen mit ähnlichen Interessen"
        :back-href="route('connections.index')" back-label="Zurück zu Verbindungen" />

    @if ($suggestions->isEmpty())
        <x-ui.empty title="Keine Vorschläge verfügbar" :action-href="route('settings.interests.edit')"
            action-label="Interessen bearbeiten" action-variant="primary">
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
                <x-ui.row>
                    <x-slot:lead>
                        <img src="{{ $user->profilePhotoUrl() }}" alt="" class="h-14 w-14 rounded-full border border-line object-cover">
                    </x-slot:lead>
                    <a href="{{ route('users.show', $user) }}" class="text-h3 text-ink no-underline hover:underline">{{ $user->fullName() }}</a>
                    <span class="text-small text-muted">{{ $user->userTypeLabel() }}</span>
                    @if ($user->bio)
                        <span class="line-clamp-3 text-small text-ink-soft">{{ $user->bio }}</span>
                    @endif
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
                </x-ui.row>
            @endforeach
        </x-ui.row-list>
    @endif
</x-layouts.app>
