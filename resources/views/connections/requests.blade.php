<x-layouts.app title="Verbindungsanfragen">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Verbindungsanfragen</h1>
                <p class="text-gray-600 mt-2">Verwalten Sie eingehende und ausgehende Anfragen</p>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-lg shadow-sm mb-8">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="showTab('received')" id="tab-received" class="tab-button border-b-2 border-blue-600 py-4 px-1 text-sm font-medium text-blue-600">
                            Erhalten ({{ $received->total() }})
                        </button>
                        <button onclick="showTab('sent')" id="tab-sent" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Gesendet ({{ $sent->total() }})
                        </button>
                        <a href="{{ route('connections.index') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Zurück zu Verbindungen
                        </a>
                    </nav>
                </div>

                <!-- Received Requests Tab -->
                <div id="content-received" class="tab-content p-6">
                    @if($received->isEmpty())
                        <div class="text-center py-12">
                            <x-icon.mail class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Keine ausstehenden Anfragen</h3>
                            <p class="mt-1 text-sm text-gray-500">Sie haben keine neuen Verbindungsanfragen erhalten.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($received as $request)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <img src="{{ $request->follower->profilePhotoUrl() }}" alt="{{ $request->follower->fullName() }}" class="w-16 h-16 rounded-full">
                                            <div class="ml-4">
                                                <h3 class="font-semibold text-gray-900">
                                                    <a href="{{ route('users.show', $request->follower) }}" class="hover:text-blue-600">
                                                        {{ $request->follower->fullName() }}
                                                    </a>
                                                </h3>
                                                <p class="text-sm text-gray-600">{{ $request->follower->userTypeLabel() }}</p>
                                                @if($request->follower->bio)
                                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($request->follower->bio, 100) }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500 mt-1">{{ $request->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>

                                        <div class="flex space-x-2 ml-4">
                                            <form action="{{ route('connections.accept', $request->follower) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                                    Akzeptieren
                                                </button>
                                            </form>
                                            <form action="{{ route('connections.decline', $request->follower) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                    Ablehnen
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $received->links() }}
                        </div>
                    @endif
                </div>

                <!-- Sent Requests Tab -->
                <div id="content-sent" class="tab-content hidden p-6">
                    @if($sent->isEmpty())
                        <div class="text-center py-12">
                            <x-icon.mail class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Keine ausstehenden Anfragen</h3>
                            <p class="mt-1 text-sm text-gray-500">Sie haben keine offenen Verbindungsanfragen gesendet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($sent as $request)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <img src="{{ $request->following->profilePhotoUrl() }}" alt="{{ $request->following->fullName() }}" class="w-16 h-16 rounded-full">
                                            <div class="ml-4">
                                                <h3 class="font-semibold text-gray-900">
                                                    <a href="{{ route('users.show', $request->following) }}" class="hover:text-blue-600">
                                                        {{ $request->following->fullName() }}
                                                    </a>
                                                </h3>
                                                <p class="text-sm text-gray-600">{{ $request->following->userTypeLabel() }}</p>
                                                @if($request->following->bio)
                                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($request->following->bio, 100) }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500 mt-1">Gesendet {{ $request->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>

                                        <div class="ml-4">
                                            <form action="{{ route('connections.cancel', $request->following) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50" onclick="return confirm('Anfrage wirklich zurückziehen?')">
                                                    Zurückziehen
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $sent->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
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
                button.classList.remove('border-blue-600', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active state to selected tab button
            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.add('border-blue-600', 'text-blue-600');
            activeButton.classList.remove('border-transparent', 'text-gray-500');
        }
    </script>
    @endpush
</x-layouts.app>

