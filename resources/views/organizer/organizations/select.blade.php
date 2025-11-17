<x-layouts.app title="Organisation wählen">
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Organisation wählen</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Wählen Sie die Organisation aus, mit der Sie arbeiten möchten</p>
    </div>

    <!-- Alerts -->
    @if(session('warning'))
        <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded">
            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ session('warning') }}</p>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded">
            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ session('info') }}</p>
        </div>
    @endif

    <!-- Content -->
    @if($organizations->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Noch keine Organisation</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Sie gehören noch keiner Organisation an. Erstellen Sie jetzt Ihre erste Organisation.</p>
            <a href="{{ route('organizer.organizations.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Neue Organisation erstellen
            </a>
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($organizations as $org)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col">
                    <div class="flex items-center gap-4 mb-4">
                        @if($org->logo)
                            <img src="{{ asset('storage/'.$org->logo) }}" alt="{{ $org->name }}" class="h-16 w-16 object-cover rounded-lg">
                        @else
                            <div class="h-16 w-16 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center rounded-lg text-white text-xl font-bold shadow-sm">
                                {{ $org->initials() }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $org->name }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($org->getUserRole(auth()->user()) ?? 'Mitglied') }}</p>
                        </div>
                    </div>

                    @if($org->description)
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-2 flex-1">{{ $org->description }}</p>
                    @else
                        <p class="text-sm text-gray-400 dark:text-gray-500 mb-4 italic flex-1">Keine Beschreibung</p>
                    @endif

                    <form method="POST" action="{{ route('organizer.organizations.switch', $org) }}" class="mt-auto">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            Organisation auswählen
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('organizer.organizations.create') }}" class="inline-flex items-center px-6 py-3 border-2 border-blue-600 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Weitere Organisation erstellen
            </a>
        </div>
    @endif
</div>
</x-layouts.app>
