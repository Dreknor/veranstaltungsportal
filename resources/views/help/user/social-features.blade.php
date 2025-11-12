<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center mb-2">
                    <a href="{{ route('help.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Netzwerk & Kontakte</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Mit anderen Pädagogen vernetzen</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    <div class="mb-8">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Vernetzen Sie sich mit anderen Pädagogen, tauschen Sie Erfahrungen aus und erweitern Sie Ihr professionelles Netzwerk.
                        </p>
                    </div>
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Inhalt</h2>
                        <ul class="space-y-2">
                            <li><a href="#find" class="text-blue-600 dark:text-blue-400 hover:underline">1. Nutzer finden</a></li>
                            <li><a href="#connect" class="text-blue-600 dark:text-blue-400 hover:underline">2. Verbindungen aufbauen</a></li>
                            <li><a href="#manage" class="text-blue-600 dark:text-blue-400 hover:underline">3. Verbindungen verwalten</a></li>
                            <li><a href="#profile" class="text-blue-600 dark:text-blue-400 hover:underline">4. Profile ansehen</a></li>
                        </ul>
                    </div>
                    <section id="find" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                            Nutzer finden
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Suchfunktion nutzen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Netzwerk</strong> → <strong>Suchen</strong></li>
                                <li>Geben Sie einen Namen oder Suchbegriff ein</li>
                                <li>Filtern Sie nach Interessen, Schule oder Fachbereich</li>
                                <li>Durchsuchen Sie die Ergebnisse</li>
                            </ol>
                            <h3 class="text-xl font-semibold mt-6">Vorschläge erhalten</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Unter <strong>Netzwerk</strong> → <strong>Vorschläge</strong> finden Sie personalisierte Empfehlungen basierend auf:
                            </p>
                            <ul class="list-disc list-inside ml-4 space-y-1 text-gray-700 dark:text-gray-300">
                                <li>Gemeinsamen besuchten Events</li>
                                <li>Ähnlichen Interessen</li>
                                <li>Gleichen Fachbereichen</li>
                                <li>Gemeinsamen Verbindungen</li>
                            </ul>
                        </div>
                    </section>
                    <section id="connect" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                            Verbindungen aufbauen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Jemandem folgen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Besuchen Sie das Profil eines Nutzers</li>
                                <li>Klicken Sie auf <strong>"Folgen"</strong></li>
                                <li>Der Nutzer wird benachrichtigt</li>
                                <li>Sie sehen Updates über dessen Aktivitäten</li>
                            </ol>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1 a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Tipp:</strong> Gegenseitiges Folgen ermöglicht direkten Nachrichtenaustausch!</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="manage" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                            Verbindungen verwalten
                        </h2>
                        <div class="ml-11 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">👥 Folge ich</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Übersicht aller Nutzer, denen Sie folgen
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🔔 Folgen mir</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Übersicht aller Ihrer Follower
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📩 Anfragen</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Offene Verbindungsanfragen annehmen oder ablehnen
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🚫 Blockiert</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Liste blockierter Nutzer
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="profile" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                            Profile ansehen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Klicken Sie auf einen Nutzer, um dessen öffentliches Profil zu sehen:
                            </p>
                            <ul class="list-disc list-inside ml-4 space-y-1 text-gray-700 dark:text-gray-300">
                                <li>Profilbild und Bio</li>
                                <li>Besuchte Events</li>
                                <li>Badges und Erfolge</li>
                                <li>Gemeinsame Interessen</li>
                                <li>Gemeinsame Verbindungen</li>
                            </ul>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
