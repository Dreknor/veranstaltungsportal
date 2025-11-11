<x-layouts.app title="Verbindungsvorschläge">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Verbindungsvorschläge</h1>
                <p class="text-gray-600 mt-2">Entdecken Sie Pädagogen mit ähnlichen Interessen</p>
            </div>

            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('connections.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Zurück zu Verbindungen
                </a>
            </div>

            @if($suggestions->isEmpty())
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Keine Vorschläge verfügbar</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Wir konnten keine passenden Verbindungsvorschläge für Sie finden.
                        Versuchen Sie, Ihre Interessen in den Einstellungen zu aktualisieren.
                    </p>
                    <div class="mt-6 space-x-4">
                        <a href="{{ route('settings.interests.edit') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Interessen bearbeiten
                        </a>
                        <a href="{{ route('connections.search') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Nutzer suchen
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($suggestions as $user)
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
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $user->bio }}</p>
                                @endif

                                @php
                                    $commonInterests = array_intersect(
                                        auth()->user()->interested_category_ids ?? [],
                                        $user->interested_category_ids ?? []
                                    );
                                @endphp

                                @if(!empty($commonInterests))
                                    <div class="mb-4">
                                        <p class="text-xs text-gray-500 mb-2">Gemeinsame Interessen:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($commonInterests as $categoryId)
                                                @php
                                                    $category = \App\Models\EventCategory::find($categoryId);
                                                @endphp
                                                @if($category)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $category->name }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="flex space-x-2">
                                    <a href="{{ route('users.show', $user) }}" class="flex-1 text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        Profil
                                    </a>
                                    <form action="{{ route('connections.send', $user) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                            Verbinden
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

