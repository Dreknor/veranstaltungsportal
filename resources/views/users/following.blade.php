<x-layouts.app :title="$user->fullName() . ' - Folgt'">
    <x-ui.page-header :title="$user->fullName() . ' folgt'" :subtitle="$following->total() . ' Nutzer'" :back-href="route('users.show', $user)" back-label="Zurück zum Profil" />

    @if ($following->isEmpty())
        <x-ui.empty title="Keine Verbindungen">Dieser Nutzer folgt noch niemandem.</x-ui.empty>
    @else
        <x-ui.row-list>
            @foreach ($following as $followedUser)
                <x-ui.person-row :user="$followedUser">
                    <x-slot:meta>
                        <span class="text-ui font-medium tabular-nums">{{ trans_choice('{0} Keine besuchten Veranstaltungen|{1} 1 besuchte Veranstaltung|[2,*] :count besuchte Veranstaltungen', $followedUser->events_attended_count) }}</span>
                    </x-slot:meta>
                    <x-slot:action>
                        <x-ui.button variant="secondary" :href="route('users.show', $followedUser)" full>Profil ansehen</x-ui.button>
                    </x-slot:action>
                </x-ui.person-row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-6">
            {{ $following->links() }}
        </div>
    @endif
</x-layouts.app>
