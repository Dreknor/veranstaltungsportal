<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Suchergebnisse</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Durchsuchen Sie unsere Hilfe-Artikel</p>
            </div>
            <!-- Search Box -->
            <div class="mb-8">
                <form action="{{ route('help.search') }}" method="GET">
                    <div class="relative">
                        <input
                            type="text"
                            name="q"
                            placeholder="Durchsuchen Sie die Hilfe-Artikel..."
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200"
                            value="{{ $query }}"
                            autofocus
                        >
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </form>
            </div>

            @if($query)
                <!-- Results -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            @if(count($results) > 0)
                                {{ count($results) }} {{ count($results) === 1 ? 'Ergebnis' : 'Ergebnisse' }} für "{{ $query }}"
                            @else
                                Keine Ergebnisse für "{{ $query }}"
                            @endif
                        </h3>

                        @if(count($results) > 0)
                            <div class="space-y-4">
                                @foreach($results as $result)
                                    <a href="{{ route('help.article', ['category' => $result['category'], 'article' => $result['slug']]) }}"
                                       class="block p-4 border dark:border-gray-700 rounded-lg hover:shadow-md transition-shadow">
                                        <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2">
                                            {{ $result['title'] }}
                                        </h4>
                                        <p class="text-gray-600 dark:text-gray-400 mb-3">
                                            {{ $result['description'] }}
                                        </p>
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-500">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">
                                                {{ ucfirst($result['category']) }}
                                            </span>
                                            <span class="ml-3 text-blue-600 dark:text-blue-400">
                                                Artikel lesen →
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-xl font-semibold mb-2">Keine passenden Artikel gefunden</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-6">
                                    Versuchen Sie es mit anderen Suchbegriffen oder durchsuchen Sie unsere Kategorien.
                                </p>
                                <a href="{{ route('help.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Zur Hilfe-Übersicht
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Popular Articles -->
                @if(count($results) === 0)
                    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold mb-4">Beliebte Hilfe-Artikel</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ route('help.article', ['category' => 'user', 'article' => 'getting-started']) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                → Erste Schritte
                            </a>
                            <a href="{{ route('help.article', ['category' => 'user', 'article' => 'booking-events']) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                → Veranstaltungen buchen
                            </a>
                            <a href="{{ route('help.article', ['category' => 'user', 'article' => 'manage-bookings']) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                → Buchungen verwalten
                            </a>
                            <a href="{{ route('help.article', ['category' => 'user', 'article' => 'troubleshooting']) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                → Häufige Probleme
                            </a>
                        </div>
                    </div>
                @endif
            @else
                <!-- No Query -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold mb-2">Geben Sie einen Suchbegriff ein</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Durchsuchen Sie unsere Hilfe-Artikel nach Themen, Problemen oder Fragen.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

