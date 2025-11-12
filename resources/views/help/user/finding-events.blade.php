<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center mb-2">
                    <a href="{{ route('help.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Veranstaltungen finden</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Entdecken Sie passende Fortbildungen</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">


                    <!-- Search Options -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Suchmöglichkeiten</h2>

                        <div class="space-y-6">
                            <!-- List View -->
                            <div class="border dark:border-gray-700 rounded-lg p-6">
                                <h3 class="text-xl font-semibold mb-3 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                    Listenansicht
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    Die Standardansicht zeigt alle Veranstaltungen in einer übersichtlichen Liste.
                                    Hier können Sie nach verschiedenen Kriterien filtern und sortieren.
                                </p>
                                <a href="{{ route('events.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                    → Zur Veranstaltungsübersicht
                                </a>
                            </div>

                            <!-- Calendar View -->
                            <div class="border dark:border-gray-700 rounded-lg p-6">
                                <h3 class="text-xl font-semibold mb-3 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Kalenderansicht
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    Sehen Sie alle Veranstaltungen in einem Kalender. Ideal für die Planung Ihrer Fortbildungen
                                    und um freie Termine zu finden.
                                </p>
                                <a href="{{ route('events.calendar') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                    → Zum Veranstaltungskalender
                                </a>
                            </div>

                            <!-- Dashboard Recommendations -->
                            <div class="border dark:border-gray-700 rounded-lg p-6">
                                <h3 class="text-xl font-semibold mb-3 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    Personalisierte Empfehlungen
                                </h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    Basierend auf Ihren Interessen und bisherigen Buchungen empfehlen wir Ihnen passende Veranstaltungen.
                                    Diese finden Sie in Ihrem Dashboard.
                                </p>
                                <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                    → Zum Dashboard
                                </a>
                            </div>
                        </div>
                    </section>

                    <!-- Filters -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Filter nutzen</h2>

                        <div class="space-y-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                                <h3 class="text-xl font-semibold mb-4">📚 Nach Kategorie filtern</h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-3">
                                    Unsere Veranstaltungen sind in Kategorien eingeteilt:
                                </p>
                                <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-gray-700 dark:text-gray-300">
                                    <li>• Pädagogik & Didaktik</li>
                                    <li>• Digitalisierung & Medien</li>
                                    <li>• Schulentwicklung</li>
                                    <li>• Inklusion & Diversität</li>
                                    <li>• Führung & Management</li>
                                    <li>• Fachspezifische Themen</li>
                                    <li>• Evangelische Schulbildung</li>
                                    <li>• Und viele mehr...</li>
                                </ul>
                            </div>

                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6">
                                <h3 class="text-xl font-semibold mb-4">📅 Nach Datum filtern</h3>
                                <p class="text-gray-700 dark:text-gray-300">
                                    Wählen Sie einen Zeitraum aus, um nur Veranstaltungen in diesem Zeitfenster anzuzeigen.
                                    Sie können nach einzelnen Tagen, Wochen oder Monaten filtern.
                                </p>
                            </div>

                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-6">
                                <h3 class="text-xl font-semibold mb-4">📍 Nach Ort filtern</h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-3">
                                    Wählen Sie zwischen:
                                </p>
                                <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 ml-4 space-y-1">
                                    <li><strong>Präsenz-Veranstaltungen:</strong> Events mit physischem Veranstaltungsort</li>
                                    <li><strong>Online-Veranstaltungen:</strong> Webinare und digitale Fortbildungen</li>
                                    <li><strong>Hybrid-Veranstaltungen:</strong> Kombinierte Formate</li>
                                </ul>
                            </div>

                            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6">
                                <h3 class="text-xl font-semibold mb-4">💰 Nach Preis filtern</h3>
                                <p class="text-gray-700 dark:text-gray-300">
                                    Nutzen Sie den Preis-Slider, um nur Veranstaltungen in Ihrer gewünschten Preisklasse anzuzeigen.
                                    Es gibt auch viele kostenlose Angebote!
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Search -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Textsuche verwenden</h2>

                        <div class="border dark:border-gray-700 rounded-lg p-6">
                            <div class="mb-4">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>

                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                Nutzen Sie die Suchleiste, um gezielt nach Veranstaltungen zu suchen. Sie können suchen nach:
                            </p>

                            <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 ml-4 space-y-2">
                                <li><strong>Titel:</strong> z.B. "Digitale Medien im Unterricht"</li>
                                <li><strong>Beschreibung:</strong> Stichwörter aus der Veranstaltungsbeschreibung</li>
                                <li><strong>Veranstalter:</strong> Name der Organisation</li>
                                <li><strong>Ort:</strong> Stadt oder Region</li>
                            </ul>

                            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4">
                                <p class="flex items-start">
                                    <svg class="h-5 w-5 text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>
                                        <strong>Tipp:</strong> Kombinieren Sie die Textsuche mit Filtern für noch präzisere Ergebnisse!
                                    </span>
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Favorites -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Favoriten nutzen</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold mb-3">Als Favorit markieren</h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    Klicken Sie auf das Herz-Symbol ❤️ auf einer Veranstaltungsseite,
                                    um diese als Favorit zu speichern.
                                </p>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-3">Favoriten ansehen</h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    Alle gespeicherten Veranstaltungen finden Sie unter
                                    <a href="{{ route('favorites.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        Meine Favoriten
                                    </a>.
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 p-4">
                            <p class="flex items-start">
                                <svg class="h-5 w-5 text-green-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>
                                    <strong>Vorteil:</strong> Sie erhalten automatisch Benachrichtigungen, wenn sich etwas an Ihren Favoriten ändert!
                                </span>
                            </p>
                        </div>
                    </section>

                    <!-- Event Details -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Veranstaltungsdetails verstehen</h2>

                        <p class="text-gray-700 dark:text-gray-300 mb-6">
                            Auf jeder Veranstaltungsseite finden Sie folgende Informationen:
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h4 class="font-semibold mb-2">📋 Grundinformationen</h4>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• Titel und Beschreibung</li>
                                    <li>• Kategorie</li>
                                    <li>• Datum und Uhrzeit</li>
                                    <li>• Dauer</li>
                                </ul>
                            </div>

                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h4 class="font-semibold mb-2">📍 Ort & Format</h4>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• Veranstaltungsort (Adresse)</li>
                                    <li>• Online-Link (bei digitalen Events)</li>
                                    <li>• Format (Präsenz/Online/Hybrid)</li>
                                </ul>
                            </div>

                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h4 class="font-semibold mb-2">🎫 Tickets & Preise</h4>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• Verfügbare Ticket-Typen</li>
                                    <li>• Preise</li>
                                    <li>• Verfügbarkeit</li>
                                    <li>• Rabattmöglichkeiten</li>
                                </ul>
                            </div>

                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h4 class="font-semibold mb-2">👤 Veranstalter</h4>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• Name und Beschreibung</li>
                                    <li>• Kontaktinformationen</li>
                                    <li>• Website</li>
                                    <li>• Weitere Events</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Navigation -->
                    <div class="mt-12 pt-8 border-t dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('help.article', ['category' => 'user', 'article' => 'getting-started']) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Vorheriger Artikel: Erste Schritte
                            </a>
                            <a href="{{ route('help.article', ['category' => 'user', 'article' => 'booking-events']) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                Nächster Artikel: Veranstaltungen buchen
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

