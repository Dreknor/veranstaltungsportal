<x-layouts.app title="Organisation wählen">
    <x-ui.page-header title="Organisation wählen" subtitle="Wählen Sie die Organisation aus, mit der Sie arbeiten möchten.">
        @if ($organizations->isNotEmpty())
            <x-slot:actions>
                <x-ui.button variant="secondary" :href="route('organizer.organizations.create')">
                    <x-slot:icon><x-icon.plus /></x-slot:icon>
                    Weitere Organisation erstellen
                </x-ui.button>
            </x-slot:actions>
        @endif
    </x-ui.page-header>

    @if ($organizations->isEmpty())
        <x-ui.empty title="Noch keine Organisation" :action-href="route('organizer.organizations.create')"
            action-label="Organisation erstellen" action-variant="primary">
            Sie gehören noch keiner Organisation an. Erstellen Sie jetzt Ihre erste Organisation.
        </x-ui.empty>
    @else
        <x-ui.row-list>
            @foreach ($organizations as $org)
                <x-ui.row>
                    <x-slot:lead>
                        @if ($org->logo)
                            <img src="{{ asset('storage/' . $org->logo) }}" alt=""
                                class="h-20 w-20 rounded-control border border-line object-cover">
                        @else
                            <span aria-hidden="true"
                                class="flex h-20 w-20 items-center justify-center rounded-control border border-line bg-zone text-h3 text-ink">{{ $org->initials() }}</span>
                        @endif
                    </x-slot:lead>

                    <span class="text-small text-muted">{{ ucfirst($org->getUserRole(auth()->user()) ?? 'Mitglied') }}</span>
                    <h2 class="m-0 truncate text-h3 text-ink">{{ $org->name }}</h2>
                    @if ($org->description)
                        <p class="m-0 line-clamp-2 text-small text-muted">{{ $org->description }}</p>
                    @else
                        <p class="m-0 text-small text-muted">Keine Beschreibung</p>
                    @endif

                    <x-slot:action>
                        <form method="POST" action="{{ route('organizer.organizations.switch', $org) }}">
                            @csrf
                            <x-ui.button type="submit" variant="secondary" full>Organisation auswählen</x-ui.button>
                        </form>
                    </x-slot:action>
                </x-ui.row>
            @endforeach
        </x-ui.row-list>
    @endif
</x-layouts.app>
