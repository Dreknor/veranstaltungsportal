<x-layouts.app title="Nutzer suchen">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Nutzer suchen</h1>
                <p class="text-gray-600 mt-2">Finden Sie Pädagogen und Organisatoren</p>
            </div>

            <!-- Search Form -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <form action="{{ route('connections.search') }}" method="GET" class="flex gap-4">
                    <div class="flex-1">
                        <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Nach Name oder E-Mail suchen..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" class="px-6 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <x-icon.search class="inline w-5 h-5 mr-2" />
                        Suchen
                    </button>
                </form>
            </div>

            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('connections.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Zurück zu Verbindungen
                </a>
            </div>

            @if($query && $users->isEmpty())
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <x-icon.search class="mx-auto h-16 w-16 text-gray-400" />
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Keine Ergebnisse</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Keine Nutzer gefunden für "{{ $query }}".
                        Versuchen Sie es mit einem anderen Suchbegriff.
                    </p>
                </div>
            @elseif(!$query)
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <x-icon.search class="mx-auto h-16 w-16 text-gray-400" />
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Bereit zum Suchen</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Geben Sie einen Namen oder eine E-Mail-Adresse ein, um Nutzer zu finden.
                    </p>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">{{ $users->total() }} Ergebnisse gefunden</p>
                    </div>

                    <div class="space-y-4">
                        @foreach($users as $user)
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->fullName() }}" class="w-16 h-16 rounded-full">
                                        <div class="ml-4">
                                            <h3 class="font-semibold text-gray-900">
                                                <a href="{{ route('users.show', $user) }}" class="hover:text-blue-600">
                                                    {{ $user->fullName() }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-600">{{ $user->userTypeLabel() }}</p>
                                            @if($user->bio)
                                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($user->bio, 100) }}</p>
                                            @endif

                                            <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                                <span>{{ $user->events_count }} Events</span>
                                                <span>{{ $user->bookings_count }} Buchungen</span>
                                                <span>{{ $user->followers_count }} Follower</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-4">
                                        @if(auth()->user()->isFollowing($user))
                                            <form action="{{ route('connections.remove', $user) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                    Verbunden
                                                </button>
                                            </form>
                                        @elseif(auth()->user()->hasPendingConnectionWith($user))
                                            <span class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-500 bg-gray-100">
                                                Ausstehend
                                            </span>
                                        @else
                                            <form action="{{ route('connections.send', $user) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                                    Verbinden
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $users->appends(['q' => $query])->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

