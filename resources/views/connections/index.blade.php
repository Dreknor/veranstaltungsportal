<x-layouts.app title="Meine Verbindungen">
    <x-ui.page-header title="Meine Verbindungen" subtitle="Vernetzen Sie sich mit anderen Pädagogen und Veranstalter" />

    <!-- Stats -->
    <div class="mb-12 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Folge ich" :value="$stats['following_count']" />
        <x-ui.stat label="Folgen mir" :value="$stats['followers_count']" />
        <x-ui.stat label="Offene Anfragen" :value="$stats['pending_requests_count']" />
    </div>

    <!-- Navigation Tabs: die ersten beiden schalten auf der Seite um, die übrigen sind Links -->
    <x-ui.tabs label="Verbindungen" class="mb-8">
        <x-ui.tab onclick="showTab('following')" id="tab-following" class="tab-button" active>
            Folge ich ({{ $stats['following_count'] }})
        </x-ui.tab>
        <x-ui.tab onclick="showTab('followers')" id="tab-followers" class="tab-button">
            Folgen mir ({{ $stats['followers_count'] }})
        </x-ui.tab>
        <x-ui.tab :href="route('connections.requests')" :count="$stats['pending_requests_count'] > 0 ? $stats['pending_requests_count'] : null">
            Anfragen
        </x-ui.tab>
        <x-ui.tab :href="route('connections.suggestions')">Vorschläge</x-ui.tab>
        <x-ui.tab :href="route('connections.search')">Suchen</x-ui.tab>
    </x-ui.tabs>

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
                    <x-ui.person-row :user="$user">
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
                    </x-ui.person-row>
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
                    <x-ui.person-row :user="$user">
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
                    </x-ui.person-row>
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

                // Remove active state from all tab buttons (die Optik folgt aria-pressed)
                document.querySelectorAll('.tab-button').forEach(button => {
                    button.setAttribute('aria-pressed', 'false');
                });

                // Show selected tab content
                document.getElementById('content-' + tabName).classList.remove('hidden');

                // Add active state to selected tab button
                document.getElementById('tab-' + tabName).setAttribute('aria-pressed', 'true');
            }
        </script>
    @endpush
</x-layouts.app>
