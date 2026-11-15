<x-layouts.app title="Meine Verbindungen">
    <x-ui.page-header title="Meine Verbindungen" subtitle="Vernetzen Sie sich mit anderen Pädagogen und Organisatoren" />

    <!-- Stats -->
    <div class="mb-12 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Folge ich" :value="$stats['following_count']" />
        <x-ui.stat label="Folgen mir" :value="$stats['followers_count']" />
        <x-ui.stat label="Offene Anfragen" :value="$stats['pending_requests_count']" />
    </div>

    <!-- Navigation Tabs -->
    @php
        $tabBase = 'focus-inset -mb-px inline-flex min-h-11 cursor-pointer items-center gap-2 border-b-2 bg-transparent px-3 text-ui whitespace-nowrap no-underline transition-colors duration-150';
        $tabIdle = 'border-transparent font-medium text-muted hover:border-line-strong hover:text-ink';
    @endphp
    <nav aria-label="Verbindungen" class="relative mb-8 overflow-x-auto border-b border-line">
        <ul class="m-0 flex list-none gap-1 p-0">
            <li class="flex-none">
                <button type="button" onclick="showTab('following')" id="tab-following" aria-pressed="true"
                    class="tab-button {{ $tabBase }} border-ink font-semibold text-ink">
                    Folge ich ({{ $stats['following_count'] }})
                </button>
            </li>
            <li class="flex-none">
                <button type="button" onclick="showTab('followers')" id="tab-followers" aria-pressed="false"
                    class="tab-button {{ $tabBase }} {{ $tabIdle }}">
                    Folgen mir ({{ $stats['followers_count'] }})
                </button>
            </li>
            <li class="flex-none">
                <a href="{{ route('connections.requests') }}" class="{{ $tabBase }} {{ $tabIdle }}">
                    Anfragen
                    @if ($stats['pending_requests_count'] > 0)
                        <span class="text-small font-normal text-muted tabular-nums">{{ $stats['pending_requests_count'] }}</span>
                    @endif
                </a>
            </li>
            <li class="flex-none">
                <a href="{{ route('connections.suggestions') }}" class="{{ $tabBase }} {{ $tabIdle }}">Vorschläge</a>
            </li>
            <li class="flex-none">
                <a href="{{ route('connections.search') }}" class="{{ $tabBase }} {{ $tabIdle }}">Suchen</a>
            </li>
        </ul>
    </nav>

    <!-- Following Tab -->
    <div id="content-following" class="tab-content">
        @if ($following->isEmpty())
            <x-ui.empty title="Keine Verbindungen" :action-href="route('connections.suggestions')"
                action-label="Vorschläge ansehen" action-variant="primary">
                Sie folgen noch niemandem.
            </x-ui.empty>
        @else
            <x-ui.row-list>
                @foreach ($following as $user)
                    <x-ui.row>
                        <x-slot:lead>
                            <img src="{{ $user->profilePhotoUrl() }}" alt="" class="h-14 w-14 rounded-full border border-line object-cover">
                        </x-slot:lead>
                        <a href="{{ route('users.show', $user) }}" class="text-h3 text-ink no-underline hover:underline">{{ $user->fullName() }}</a>
                        <span class="text-small text-muted">{{ $user->userTypeLabel() }}</span>
                        @if ($user->bio)
                            <span class="line-clamp-2 text-small text-ink-soft">{{ $user->bio }}</span>
                        @endif
                        <x-slot:meta>
                            <span class="text-ui font-medium tabular-nums">{{ $user->events_count ?? 0 }} Veranstaltungen</span>
                            <span class="text-small text-muted tabular-nums">{{ $user->bookings_count ?? 0 }} Buchungen</span>
                        </x-slot:meta>
                        <x-slot:action>
                            <x-ui.button variant="secondary" :href="route('users.show', $user)" full>Profil</x-ui.button>
                            <form action="{{ route('connections.remove', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="secondary" full
                                    onclick="return confirm('Verbindung wirklich entfernen?')">Entfernen</x-ui.button>
                            </form>
                        </x-slot:action>
                    </x-ui.row>
                @endforeach
            </x-ui.row-list>

            <div class="mt-6">
                {{ $following->links() }}
            </div>
        @endif
    </div>

    <!-- Followers Tab -->
    <div id="content-followers" class="tab-content hidden">
        @if ($followers->isEmpty())
            <x-ui.empty title="Keine Follower">Ihnen folgt noch niemand.</x-ui.empty>
        @else
            <x-ui.row-list>
                @foreach ($followers as $user)
                    <x-ui.row>
                        <x-slot:lead>
                            <img src="{{ $user->profilePhotoUrl() }}" alt="" class="h-14 w-14 rounded-full border border-line object-cover">
                        </x-slot:lead>
                        <a href="{{ route('users.show', $user) }}" class="text-h3 text-ink no-underline hover:underline">{{ $user->fullName() }}</a>
                        <span class="text-small text-muted">{{ $user->userTypeLabel() }}</span>
                        @if ($user->bio)
                            <span class="line-clamp-2 text-small text-ink-soft">{{ $user->bio }}</span>
                        @endif
                        <x-slot:meta>
                            <span class="text-ui font-medium tabular-nums">{{ $user->events_count ?? 0 }} Veranstaltungen</span>
                            <span class="text-small text-muted tabular-nums">{{ $user->bookings_count ?? 0 }} Buchungen</span>
                        </x-slot:meta>
                        <x-slot:action>
                            <x-ui.button variant="secondary" :href="route('users.show', $user)" full>Profil</x-ui.button>
                            @if (auth()->user()->isFollowing($user))
                                <form action="{{ route('connections.remove', $user) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="secondary" full>Nicht mehr folgen</x-ui.button>
                                </form>
                            @else
                                <form action="{{ route('connections.send', $user) }}" method="POST">
                                    @csrf
                                    <x-ui.button type="submit" full>Zurück folgen</x-ui.button>
                                </form>
                            @endif
                        </x-slot:action>
                    </x-ui.row>
                @endforeach
            </x-ui.row-list>

            <div class="mt-6">
                {{ $followers->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function showTab(tabName) {
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });

                // Remove active state from all tab buttons
                document.querySelectorAll('.tab-button').forEach(button => {
                    button.classList.remove('border-ink', 'text-ink', 'font-semibold');
                    button.classList.add('border-transparent', 'text-muted', 'font-medium');
                    button.setAttribute('aria-pressed', 'false');
                });

                // Show selected tab content
                document.getElementById('content-' + tabName).classList.remove('hidden');

                // Add active state to selected tab button
                const activeButton = document.getElementById('tab-' + tabName);
                activeButton.classList.add('border-ink', 'text-ink', 'font-semibold');
                activeButton.classList.remove('border-transparent', 'text-muted', 'font-medium');
                activeButton.setAttribute('aria-pressed', 'true');
            }
        </script>
    @endpush
</x-layouts.app>
