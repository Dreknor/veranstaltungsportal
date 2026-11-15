<x-layouts.app :title="$user->fullName()">
    <div class="mb-8 flex flex-col gap-6 sm:flex-row sm:items-start">
        @if ($user->profile_photo)
            <img src="{{ $user->profilePhotoUrl() }}" alt="" class="h-24 w-24 flex-none rounded-full border border-line object-cover">
        @else
            <span aria-hidden="true" class="flex h-24 w-24 flex-none items-center justify-center rounded-full border border-line bg-zone text-h2 text-ink-soft">{{ $user->initials() }}</span>
        @endif

        <x-ui.page-header class="mb-0 min-w-0 flex-1" :title="$user->fullName()" :subtitle="$user->userTypeLabel()">
            @if (auth()->check() && auth()->id() !== $user->id)
                <x-slot:actions>
                    @if ($connectionStatus === 'following')
                        <x-ui.status tone="success">Verbunden</x-ui.status>
                        <form action="{{ route('connections.remove', $user) }}" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="secondary" onclick="return confirm('Verbindung wirklich entfernen?')">Entfernen</x-ui.button>
                        </form>
                    @elseif ($connectionStatus === 'pending')
                        @if ($isPendingRequest)
                            {{-- Anfrage wurde von diesem Nutzer gesendet: kann zurückgezogen werden --}}
                            <x-ui.status tone="warning">Anfrage gesendet</x-ui.status>
                            <form action="{{ route('connections.cancel', $user) }}" method="POST" class="m-0">
                                @csrf
                                <x-ui.button type="submit" variant="secondary">Zurückziehen</x-ui.button>
                            </form>
                        @else
                            {{-- Anfrage wurde empfangen: annehmen oder ablehnen --}}
                            <x-ui.status tone="info">Möchte sich verbinden</x-ui.status>
                            <form action="{{ route('connections.decline', $user) }}" method="POST" class="m-0">
                                @csrf
                                <x-ui.button type="submit" variant="secondary">Ablehnen</x-ui.button>
                            </form>
                            <form action="{{ route('connections.accept', $user) }}" method="POST" class="m-0">
                                @csrf
                                <x-ui.button type="submit">Zurückfolgen</x-ui.button>
                            </form>
                        @endif
                    @elseif ($canSendConnectionRequest)
                        <form action="{{ route('connections.send', $user) }}" method="POST" class="m-0">
                            @csrf
                            <x-ui.button type="submit">Verbinden</x-ui.button>
                        </form>
                    @else
                        <x-ui.status tone="neutral">Verbindungsanfragen deaktiviert</x-ui.status>
                    @endif
                </x-slot:actions>
            @endif
        </x-ui.page-header>
    </div>

    @if ($user->bio)
        <p class="m-0 mb-8 max-w-prose text-base text-ink">{{ $user->bio }}</p>
    @endif

    <div class="mb-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-ui.stat label="Veranstaltungen besucht" :value="$stats['events_attended']" />
        @if ($user->isOrganizer())
            <x-ui.stat label="Veranstaltungen organisiert" :value="$stats['events_organized']" />
        @endif
        <x-ui.stat label="Stunden Fortbildung" :value="$stats['total_hours']" />
        <x-ui.stat label="Follower" :value="$stats['followers_count']" :href="route('users.followers', $user)" />
        <x-ui.stat label="Folgt" :value="$stats['following_count']" :href="route('users.following', $user)" />
    </div>

    <div class="grid items-start gap-x-12 gap-y-12 lg:grid-cols-[minmax(0,1fr)_minmax(0,368px)]">
        <div class="flex min-w-0 flex-col gap-12">
            @if ($recentEvents->isNotEmpty())
                <x-ui.section :title="$user->isOrganizer() ? 'Organisierte Veranstaltungen' : 'Besuchte Veranstaltungen'">
                    <x-ui.row-list>
                        @foreach ($recentEvents as $event)
                            <x-ui.event-row :event="$event" :show-action="false" />
                        @endforeach
                    </x-ui.row-list>
                </x-ui.section>
            @endif

            <x-ui.section title="Aktivität">
                <ul class="m-0 flex list-none flex-col p-0 text-base text-ink">
                    @if ($stats['events_attended'] > 0)
                        <li class="border-t border-line py-3">Hat <span class="font-semibold tabular-nums">{{ $stats['events_attended'] }}</span> Veranstaltung(en) besucht</li>
                    @endif
                    @if ($user->isOrganizer() && $stats['events_organized'] > 0)
                        <li class="border-t border-line py-3">Hat <span class="font-semibold tabular-nums">{{ $stats['events_organized'] }}</span> Veranstaltung(en) organisiert</li>
                    @endif
                    @if ($user->badges->isNotEmpty())
                        <li class="border-t border-line py-3">Hat <span class="font-semibold tabular-nums">{{ $user->badges->count() }}</span> Abzeichen verdient</li>
                    @endif
                    @if ($stats['events_attended'] === 0 && $user->badges->isEmpty() && !($user->isOrganizer() && $stats['events_organized'] > 0))
                        <li class="border-t border-line py-3 text-muted">Noch keine Aktivität.</li>
                    @endif
                </ul>
            </x-ui.section>
        </div>

        <div class="flex min-w-0 flex-col gap-12">
            @if (($user->email || $user->phone) && auth()->check())
                <x-ui.section title="Kontakt" level="h3">
                    <ul class="m-0 flex list-none flex-col gap-2 p-0 text-base">
                        @if ($showEmail && $user->email)
                            <li class="flex items-center gap-2">
                                <x-icon.mail class="h-5 w-5 flex-none text-muted" />
                                <a href="mailto:{{ $user->email }}" class="min-w-0 break-words font-medium text-ink underline underline-offset-[3px]">{{ $user->email }}</a>
                            </li>
                        @endif
                        @if ($showPhone && $user->phone)
                            <li class="flex items-center gap-2">
                                <x-icon.phone class="h-5 w-5 flex-none text-muted" />
                                <a href="tel:{{ $user->phone }}" class="font-medium text-ink underline underline-offset-[3px]">{{ $user->phone }}</a>
                            </li>
                        @endif
                        @if (!$showEmail && !$showPhone)
                            <li class="text-small text-muted">Verbinden Sie sich, um Kontaktdaten zu sehen.</li>
                        @endif
                    </ul>
                </x-ui.section>
            @endif

            @if ($user->badges->isNotEmpty())
                <x-ui.section title="Abzeichen" level="h3">
                    <ul class="m-0 flex list-none flex-col p-0">
                        @foreach ($user->badges as $badge)
                            <li class="border-t border-line">
                                <a href="{{ route('badges.show', $badge) }}" class="flex min-h-14 items-center gap-4 py-3 text-ui font-medium text-ink no-underline underline-offset-[3px] hover:underline">
                                    <x-badge-icon :badge="$badge" size="sm" />
                                    <span class="min-w-0">{{ $badge->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @if ($user->badges->count() > 6)
                        <x-ui.button variant="link" :href="route('badges.index')" arrow>Alle Abzeichen ansehen</x-ui.button>
                    @endif
                </x-ui.section>
            @endif
        </div>
    </div>
</x-layouts.app>
