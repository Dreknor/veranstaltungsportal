<x-layouts.app title="Blockierte Nutzer">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Blockierte Nutzer</h1>
                <p class="text-gray-600 mt-2">Verwalten Sie Ihre blockierten Nutzer</p>
            </div>

            <div class="mb-6">
                <a href="{{ route('connections.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Zurück zu Verbindungen
                </a>
            </div>

            @if($blockedUsers->isEmpty())
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Keine blockierten Nutzer</h3>
                    <p class="mt-2 text-sm text-gray-500">Sie haben keine Nutzer blockiert.</p>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="space-y-4">
                        @foreach($blockedUsers as $connection)
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <img src="{{ $connection->following->profilePhotoUrl() }}" alt="{{ $connection->following->fullName() }}" class="w-16 h-16 rounded-full">
                                        <div class="ml-4">
                                            <h3 class="font-semibold text-gray-900">
                                                {{ $connection->following->fullName() }}
                                            </h3>
                                            <p class="text-sm text-gray-600">{{ $connection->following->userTypeLabel() }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Blockiert {{ $connection->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>

                                    <div class="ml-4">
                                        <form action="{{ route('connections.unblock', $connection->following) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50" onclick="return confirm('Nutzer wirklich entsperren?')">
                                                Entsperren
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $blockedUsers->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
<x-layouts.app :title="$user->fullName() . ' - Follower'">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-800">
                    ← Zurück zum Profil
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Follower von {{ $user->fullName() }}</h1>
                <p class="text-gray-600 mt-2">{{ $followers->total() }} Follower</p>
            </div>

            @if($followers->isEmpty())
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Keine Follower</h3>
                    <p class="mt-2 text-sm text-gray-500">Diesem Nutzer folgt noch niemand.</p>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($followers as $follower)
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <img src="{{ $follower->profilePhotoUrl() }}" alt="{{ $follower->fullName() }}" class="w-16 h-16 rounded-full">
                                    <div class="ml-4">
                                        <h3 class="font-semibold text-gray-900">
                                            <a href="{{ route('users.show', $follower) }}" class="hover:text-blue-600">
                                                {{ $follower->fullName() }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-600">{{ $follower->userTypeLabel() }}</p>
                                    </div>
                                </div>

                                @if($follower->bio)
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $follower->bio }}</p>
                                @endif

                                <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                                    <span>{{ $follower->events_count }} Events</span>
                                    <span>{{ $follower->bookings_count }} Buchungen</span>
                                </div>

                                <a href="{{ route('users.show', $follower) }}" class="block text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Profil ansehen
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $followers->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

