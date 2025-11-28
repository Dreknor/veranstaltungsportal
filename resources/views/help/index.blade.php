<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Hilfe & Anleitungen</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Hier finden Sie alle Informationen, die Sie benötigen</p>
            </div>
            <!-- Search -->
            <div class="mb-8">
                <form action="{{ route('help.search') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="relative">
                        <input
                            type="text"
                            name="q"
                            placeholder="Durchsuchen Sie die Hilfe-Artikel..."
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200"
                            value="{{ request('q') }}"
                        >
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Welcome Message -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-8 mb-8 text-white">
                <h1 class="text-3xl font-bold mb-4">Willkommen im Hilfe-Center</h1>
                <p class="text-lg opacity-90">
                    Hier finden Sie alle Informationen, die Sie benötigen, um das Bildungsportal optimal zu nutzen.
                    Durchsuchen Sie unsere Anleitungen oder wählen Sie ein Thema aus den Kategorien unten.
                </p>
            </div>

            <!-- Help Categories -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @if(in_array($userType, ['organizer', 'admin']))
                    <!-- Organizer Help (only for organizers/admin) -->
                    <a href="{{ route('help.category', 'organizer') }}"
                       class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border-2 border-purple-400">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-semibold text-white">Veranstalter-Hilfe</h3>
                        </div>
                        <p class="text-white/90">
                            Leitfäden für Organisationen: Events erstellen, Buchungen verwalten, Marketing und mehr.
                        </p>
                    </a>
                @endif

                <!-- Getting Started -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'getting-started']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Erste Schritte</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Lernen Sie die Grundlagen kennen und starten Sie erfolgreich mit dem Bildungsportal.
                    </p>
                </a>

                <!-- Finding Events -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'finding-events']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Veranstaltungen finden</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Entdecken Sie passende Fortbildungen mit unseren Such- und Filterfunktionen.
                    </p>
                </a>

                <!-- Booking Events -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'booking-events']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Veranstaltungen buchen</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Schritt-für-Schritt Anleitung zum Buchen von Tickets und zur Zahlungsabwicklung.
                    </p>
                </a>

                <!-- Manage Bookings -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'manage-bookings']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Buchungen verwalten</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Verwalten Sie Ihre Buchungen, laden Sie Tickets herunter und stornieren Sie bei Bedarf.
                    </p>
                </a>

                <!-- Profile & Settings -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'profile-settings']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Profil & Einstellungen</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Passen Sie Ihr Profil an und verwalten Sie Ihre Kontoeinstellungen.
                    </p>
                </a>

                <!-- Notifications -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'notifications']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Benachrichtigungen</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Verwalten Sie Ihre Benachrichtigungseinstellungen und bleiben Sie auf dem Laufenden.
                    </p>
                </a>

                <!-- Social Features -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'social-features']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Netzwerk & Kontakte</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Vernetzen Sie sich mit anderen Pädagogen und erweitern Sie Ihr Netzwerk.
                    </p>
                </a>

                <!-- Badges -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'badges']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Badges & Erfolge</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Sammeln Sie Badges für Ihre Teilnahme und verfolgen Sie Ihren Lernfortschritt.
                    </p>
                </a>

                <!-- Favorites -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'favorites']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Favoriten & Merkliste</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Speichern Sie interessante Veranstaltungen und greifen Sie später darauf zu.
                    </p>
                </a>

                <!-- Reviews -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'reviews']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Bewertungen schreiben</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Teilen Sie Ihre Erfahrungen und helfen Sie anderen bei der Auswahl.
                    </p>
                </a>

                <!-- Privacy -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'privacy']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Datenschutz & Privatsphäre</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Erfahren Sie, wie Ihre Daten geschützt werden und welche Rechte Sie haben.
                    </p>
                </a>

                <!-- Troubleshooting -->
                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'troubleshooting']) }}"
                   class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Häufige Probleme</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Finden Sie Lösungen für die häufigsten Fragen und Probleme.
                    </p>
                </a>
            </div>

            <!-- Support Contact -->
            <div class="mt-12 bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Benötigen Sie weitere Hilfe?
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Wenn Sie keine Antwort auf Ihre Frage gefunden haben, kontaktieren Sie uns gerne direkt.
                    </p>
                    <a href="mailto:support@bildungsportal.de"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Support kontaktieren
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
