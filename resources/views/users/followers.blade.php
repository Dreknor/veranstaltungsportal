<x-layouts.app :title="$user->fullName() . ' - Follower'">
    <x-ui.page-header :title="'Follower von ' . $user->fullName()" :subtitle="$followers->total() . ' Follower'" :back-href="route('users.show', $user)" back-label="Zurück zum Profil" />

    @if ($followers->isEmpty())
        <x-ui.empty title="Keine Follower">Diesem Nutzer folgt noch niemand.</x-ui.empty>
    @else
        <x-ui.row-list>
            @foreach ($followers as $follower)
                <x-ui.person-row :user="$follower">
                    <x-slot:meta>
                        <span class="text-ui font-medium tabular-nums">{{ $follower->events_count }} Veranstaltungen</span>
                        <span class="text-small text-muted tabular-nums">{{ $follower->bookings_count }} Buchungen</span>
                    </x-slot:meta>
                    <x-slot:action>
                        <x-ui.button variant="secondary" :href="route('users.show', $follower)" full>Profil ansehen</x-ui.button>
                    </x-slot:action>
                </x-ui.person-row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-6">
            {{ $followers->links() }}
        </div>
    @endif
</x-layouts.app>
