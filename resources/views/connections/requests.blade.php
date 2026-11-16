<x-layouts.app title="Verbindungsanfragen">
    <x-ui.page-header title="Verbindungsanfragen" subtitle="Verwalten Sie eingehende und ausgehende Anfragen"
        :back-href="route('connections.index')" back-label="Zurück zu Verbindungen" />

    <!-- Tabs -->
    <x-ui.tabs label="Anfragen" variant="toggle" class="mb-8">
        <x-ui.tab onclick="showTab('received')" id="tab-received" class="tab-button" active>
            Erhalten ({{ $received->total() }})
        </x-ui.tab>
        <x-ui.tab onclick="showTab('sent')" id="tab-sent" class="tab-button">
            Gesendet ({{ $sent->total() }})
        </x-ui.tab>
    </x-ui.tabs>

    <!-- Received Requests Tab -->
    <div id="content-received" class="tab-content">
        @if ($received->isEmpty())
            <x-ui.empty title="Keine ausstehenden Anfragen">
                Sie haben keine neuen Verbindungsanfragen erhalten.
            </x-ui.empty>
        @else
            <x-ui.row-list>
                @foreach ($received as $request)
                    <x-ui.person-row :user="$request->follower">
                        <x-slot:meta>
                            <span class="text-small text-muted">{{ $request->created_at->diffForHumans() }}</span>
                        </x-slot:meta>
                        <x-slot:action>
                            <form action="{{ route('connections.accept', $request->follower) }}" method="POST">
                                @csrf
                                <x-ui.button type="submit" full>Annehmen</x-ui.button>
                            </form>
                            <form action="{{ route('connections.decline', $request->follower) }}" method="POST">
                                @csrf
                                <x-ui.button type="submit" variant="secondary" full>Ablehnen</x-ui.button>
                            </form>
                        </x-slot:action>
                    </x-ui.person-row>
                @endforeach
            </x-ui.row-list>

            <div class="mt-6">
                {{ $received->links() }}
            </div>
        @endif
    </div>

    <!-- Sent Requests Tab -->
    <div id="content-sent" class="tab-content hidden">
        @if ($sent->isEmpty())
            <x-ui.empty title="Keine ausstehenden Anfragen">
                Sie haben keine offenen Verbindungsanfragen gesendet.
            </x-ui.empty>
        @else
            <x-ui.row-list>
                @foreach ($sent as $request)
                    <x-ui.person-row :user="$request->following">
                        <x-slot:meta>
                            <span class="text-small text-muted">Gesendet {{ $request->created_at->diffForHumans() }}</span>
                        </x-slot:meta>
                        <x-slot:action>
                            <form action="{{ route('connections.cancel', $request->following) }}" method="POST">
                                @csrf
                                <x-ui.button type="submit" variant="secondary" full
                                    onclick="return confirm('Anfrage wirklich zurückziehen?')">Zurückziehen</x-ui.button>
                            </form>
                        </x-slot:action>
                    </x-ui.person-row>
                @endforeach
            </x-ui.row-list>

            <div class="mt-6">
                {{ $sent->links() }}
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
