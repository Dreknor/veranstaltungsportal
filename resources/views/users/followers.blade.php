<x-layouts.app :title="$user->fullName() . ' - Follower'">
    <x-ui.page-header :title="'Follower von ' . $user->fullName()" :subtitle="$followers->total() . ' Follower'" :back-href="route('users.show', $user)" back-label="Zurück zum Profil" />

    @if ($followers->isEmpty())
        <x-ui.empty title="Keine Follower">Diesem Nutzer folgt noch niemand.</x-ui.empty>
    @else
        <x-ui.row-list>
            @foreach ($followers as $follower)
                <x-ui.row>
                    <x-slot:lead>
                        @if ($follower->profile_photo)
                            <img src="{{ $follower->profilePhotoUrl() }}" alt="" class="h-14 w-14 rounded-full border border-line object-cover">
                        @else
                            <span aria-hidden="true" class="flex h-14 w-14 items-center justify-center rounded-full border border-line bg-zone text-h3 text-ink-soft">{{ $follower->initials() }}</span>
                        @endif
                    </x-slot:lead>
                    <span class="text-small text-muted">{{ $follower->userTypeLabel() }}</span>
                    <a href="{{ route('users.show', $follower) }}" class="text-h3 text-ink no-underline hover:underline">{{ $follower->fullName() }}</a>
                    @if ($follower->bio)
                        <p class="m-0 line-clamp-2 text-small text-muted">{{ $follower->bio }}</p>
                    @endif
                    <x-slot:meta>
                        <span class="text-ui font-medium tabular-nums">{{ $follower->events_count }} Veranstaltungen</span>
                        <span class="text-small text-muted tabular-nums">{{ $follower->bookings_count }} Buchungen</span>
                    </x-slot:meta>
                    <x-slot:action>
                        <x-ui.button variant="secondary" :href="route('users.show', $follower)" full>Profil ansehen</x-ui.button>
                    </x-slot:action>
                </x-ui.row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-6">
            {{ $followers->links() }}
        </div>
    @endif
</x-layouts.app>
