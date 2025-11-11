<x-layouts.app title="Meine Verbindungen">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Meine Verbindungen</h1>
                <p class="text-gray-600 mt-2">Vernetzen Sie sich mit anderen Pädagogen und Organisatoren</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Folge ich</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['following_count'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Folgen mir</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['followers_count'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Offene Anfragen</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_requests_count'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="bg-white rounded-lg shadow-sm mb-8">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="showTab('following')" id="tab-following" class="tab-button border-b-2 border-blue-600 py-4 px-1 text-sm font-medium text-blue-600">
                            Folge ich ({{ $stats['following_count'] }})
                        </button>
                        <button onclick="showTab('followers')" id="tab-followers" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Folgen mir ({{ $stats['followers_count'] }})
                        </button>
                        <a href="{{ route('connections.requests') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 flex items-center">
                            Anfragen
                            @if($stats['pending_requests_count'] > 0)
                                <span class="ml-2 px-2 py-1 text-xs font-bold rounded-full bg-red-500 text-white">
                                    {{ $stats['pending_requests_count'] }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('connections.suggestions') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Vorschläge
                        </a>
                        <a href="{{ route('connections.search') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Suchen
                        </a>
                    </nav>
                </div>

                <!-- Following Tab -->
                <div id="content-following" class="tab-content p-6">
                    @if($following->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Verbindungen</h3>
                            <p class="mt-1 text-sm text-gray-500">Sie folgen noch niemandem.</p>
                            <div class="mt-6">
                                <a href="{{ route('connections.suggestions') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Vorschläge ansehen
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($following as $user)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="flex items-center mb-4">
                                        <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->fullName() }}" class="w-16 h-16 rounded-full">
                                        <div class="ml-4">
                                            <h3 class="font-semibold text-gray-900">
                                                <a href="{{ route('users.show', $user) }}" class="hover:text-blue-600">
                                                    {{ $user->fullName() }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-600">{{ $user->userTypeLabel() }}</p>
                                        </div>
                                    </div>

                                    @if($user->bio)
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $user->bio }}</p>
                                    @endif

                                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                                        <span>{{ $user->events_count }} Events</span>
                                        <span>{{ $user->bookings_count }} Buchungen</span>
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('users.show', $user) }}" class="flex-1 text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            Profil
                                        </a>
                                        <form action="{{ route('connections.remove', $user) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50" onclick="return confirm('Verbindung wirklich entfernen?')">
                                                Entfernen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $following->links() }}
                        </div>
                    @endif
                </div>

                <!-- Followers Tab -->
                <div id="content-followers" class="tab-content hidden p-6">
                    @if($followers->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Follower</h3>
                            <p class="mt-1 text-sm text-gray-500">Ihnen folgt noch niemand.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($followers as $user)
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="flex items-center mb-4">
                                        <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->fullName() }}" class="w-16 h-16 rounded-full">
                                        <div class="ml-4">
                                            <h3 class="font-semibold text-gray-900">
                                                <a href="{{ route('users.show', $user) }}" class="hover:text-blue-600">
                                                    {{ $user->fullName() }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-600">{{ $user->userTypeLabel() }}</p>
                                        </div>
                                    </div>

                                    @if($user->bio)
                                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $user->bio }}</p>
                                    @endif

                                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                                        <span>{{ $user->events_count }} Events</span>
                                        <span>{{ $user->bookings_count }} Buchungen</span>
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('users.show', $user) }}" class="flex-1 text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            Profil
                                        </a>
                                        @if(auth()->user()->isFollowing($user))
                                            <form action="{{ route('connections.remove', $user) }}" method="POST" class="flex-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                    Nicht mehr folgen
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('connections.send', $user) }}" method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit" class="w-full px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                                    Zurück folgen
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $followers->links() }}
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

