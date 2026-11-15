<x-layouts.app title="Verbindungsanfragen">
    <x-ui.page-header title="Verbindungsanfragen" subtitle="Verwalten Sie eingehende und ausgehende Anfragen"
        :back-href="route('connections.index')" back-label="Zurück zu Verbindungen" />

    <!-- Tabs -->
    @php
        $tabBase = 'focus-inset -mb-px inline-flex min-h-11 cursor-pointer items-center gap-2 border-b-2 bg-transparent px-3 text-ui whitespace-nowrap no-underline transition-colors duration-150';
        $tabIdle = 'border-transparent font-medium text-muted hover:border-line-strong hover:text-ink';
    @endphp
    <nav aria-label="Anfragen" class="relative mb-8 overflow-x-auto border-b border-line">
        <ul class="m-0 flex list-none gap-1 p-0">
            <li class="flex-none">
                <button type="button" onclick="showTab('received')" id="tab-received" aria-pressed="true"
                    class="tab-button {{ $tabBase }} border-ink font-semibold text-ink">
                    Erhalten ({{ $received->total() }})
                </button>
            </li>
            <li class="flex-none">
                <button type="button" onclick="showTab('sent')" id="tab-sent" aria-pressed="false"
                    class="tab-button {{ $tabBase }} {{ $tabIdle }}">
                    Gesendet ({{ $sent->total() }})
                </button>
            </li>
        </ul>
    </nav>

    <!-- Received Requests Tab -->
    <div id="content-received" class="tab-content">
        @if ($received->isEmpty())
            <x-ui.empty title="Keine ausstehenden Anfragen">
                Sie haben keine neuen Verbindungsanfragen erhalten.
            </x-ui.empty>
        @else
            <x-ui.row-list>
                @foreach ($received as $request)
                    <x-ui.row>
                        <x-slot:lead>
                            <img src="{{ $request->follower->profilePhotoUrl() }}" alt="" class="h-14 w-14 rounded-full border border-line object-cover">
                        </x-slot:lead>
                        <a href="{{ route('users.show', $request->follower) }}" class="text-h3 text-ink no-underline hover:underline">{{ $request->follower->fullName() }}</a>
                        <span class="text-small text-muted">{{ $request->follower->userTypeLabel() }}</span>
                        @if ($request->follower->bio)
                            <span class="text-small text-ink-soft">{{ Str::limit($request->follower->bio, 100) }}</span>
                        @endif
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
                    </x-ui.row>
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
                    <x-ui.row>
                        <x-slot:lead>
                            <img src="{{ $request->following->profilePhotoUrl() }}" alt="" class="h-14 w-14 rounded-full border border-line object-cover">
                        </x-slot:lead>
                        <a href="{{ route('users.show', $request->following) }}" class="text-h3 text-ink no-underline hover:underline">{{ $request->following->fullName() }}</a>
                        <span class="text-small text-muted">{{ $request->following->userTypeLabel() }}</span>
                        @if ($request->following->bio)
                            <span class="text-small text-ink-soft">{{ Str::limit($request->following->bio, 100) }}</span>
                        @endif
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
                    </x-ui.row>
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
