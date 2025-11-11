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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
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

