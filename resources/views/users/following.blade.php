<x-layouts.app :title="$user->fullName() . ' - Folgt'">
    <x-ui.page-header :title="$user->fullName() . ' folgt'" :subtitle="$following->total() . ' Nutzer'" :back-href="route('users.show', $user)" back-label="Zurück zum Profil" />

    @if ($following->isEmpty())
        <x-ui.empty title="Keine Verbindungen">Dieser Nutzer folgt noch niemandem.</x-ui.empty>
    @else
        <x-ui.row-list>
            @foreach ($following as $followedUser)
                <x-ui.row>
                    <x-slot:lead>
                        @if ($followedUser->profile_photo)
                            <img src="{{ $followedUser->profilePhotoUrl() }}" alt="" class="h-14 w-14 rounded-full border border-line object-cover">
                        @else
                            <span aria-hidden="true" class="flex h-14 w-14 items-center justify-center rounded-full border border-line bg-zone text-h3 text-ink-soft">{{ $followedUser->initials() }}</span>
                        @endif
                    </x-slot:lead>
                    <span class="text-small text-muted">{{ $followedUser->userTypeLabel() }}</span>
                    <a href="{{ route('users.show', $followedUser) }}" class="text-h3 text-ink no-underline hover:underline">{{ $followedUser->fullName() }}</a>
                    @if ($followedUser->bio)
                        <p class="m-0 line-clamp-2 text-small text-muted">{{ $followedUser->bio }}</p>
                    @endif
                    <x-slot:meta>
                        <span class="text-ui font-medium tabular-nums">{{ $followedUser->events_count }} Veranstaltungen</span>
                        <span class="text-small text-muted tabular-nums">{{ $followedUser->bookings_count }} Buchungen</span>
                    </x-slot:meta>
                    <x-slot:action>
                        <x-ui.button variant="secondary" :href="route('users.show', $followedUser)" full>Profil ansehen</x-ui.button>
                    </x-slot:action>
                </x-ui.row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-6">
            {{ $following->links() }}
        </div>
    @endif
</x-layouts.app>
