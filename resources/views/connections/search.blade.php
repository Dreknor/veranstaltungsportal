<x-layouts.app title="Nutzer suchen">
    <x-ui.page-header title="Nutzer suchen" subtitle="Finden Sie Pädagogen und Veranstalter"
        :back-href="route('connections.index')" back-label="Zurück zu Verbindungen" />

    <!-- Search Form -->
    <form action="{{ route('connections.search') }}" method="GET" role="search" aria-label="Nutzer suchen"
        class="mb-8 flex max-w-form flex-wrap items-end gap-x-4 gap-y-3">
        <x-ui.field label="Name oder E-Mail" name="q" :value="$query ?? ''" :old="false"
            placeholder="Nach Name oder E-Mail suchen..." class="min-w-0 flex-[1_1_240px]" />
        <x-ui.button type="submit" size="lg">
            <x-slot:icon><x-icon.search /></x-slot:icon>
            Suchen
        </x-ui.button>
    </form>

    @if ($query && $users->isEmpty())
        <x-ui.empty title="Keine Ergebnisse">
            Keine Nutzer gefunden für "{{ $query }}". Versuchen Sie es mit einem anderen Suchbegriff.
        </x-ui.empty>
    @elseif(!$query)
        <x-ui.empty title="Bereit zum Suchen">
            Geben Sie einen Namen oder eine E-Mail-Adresse ein, um Nutzer zu finden.
        </x-ui.empty>
    @else
        <p class="m-0 mb-4 text-small text-muted tabular-nums">{{ $users->total() }} Ergebnisse gefunden</p>

        <x-ui.row-list>
            @foreach ($users as $user)
                <x-ui.person-row :user="$user">
                    <x-slot:meta>
                        <span class="text-ui font-medium tabular-nums">{{ trans_choice('{0} Keine besuchten Veranstaltungen|{1} 1 besuchte Veranstaltung|[2,*] :count besuchte Veranstaltungen', $user->events_attended_count) }}</span>
                        <span class="text-small text-muted tabular-nums">{{ $user->followers_count }} Follower</span>
                    </x-slot:meta>
                    <x-slot:action>
                        @if (auth()->user()->isFollowing($user))
                            <form action="{{ route('connections.remove', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="secondary" full>Verbunden</x-ui.button>
                            </form>
                        @elseif(auth()->user()->hasPendingConnectionWith($user))
                            <x-ui.status tone="warning" class="justify-center">Ausstehend</x-ui.status>
                        @else
                            <form action="{{ route('connections.send', $user) }}" method="POST">
                                @csrf
                                <x-ui.button type="submit" full>Verbinden</x-ui.button>
                            </form>
                        @endif
                    </x-slot:action>
                </x-ui.person-row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-6">
            {{ $users->appends(['q' => $query])->links() }}
        </div>
    @endif
</x-layouts.app>
