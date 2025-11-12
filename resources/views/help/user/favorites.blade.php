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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Favoriten & Merkliste</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Veranstaltungen als Favoriten speichern</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    <div class="mb-8">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Speichern Sie interessante Veranstaltungen als Favoriten, um sie später schnell wiederzufinden und automatisch über Änderungen informiert zu werden.
                        </p>
                    </div>
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Inhalt</h2>
                        <ul class="space-y-2">
                            <li><a href="#add" class="text-blue-600 dark:text-blue-400 hover:underline">1. Favoriten hinzufügen</a></li>
                            <li><a href="#manage" class="text-blue-600 dark:text-blue-400 hover:underline">2. Favoriten verwalten</a></li>
                            <li><a href="#organize" class="text-blue-600 dark:text-blue-400 hover:underline">3. Mit Listen organisieren</a></li>
                            <li><a href="#notifications" class="text-blue-600 dark:text-blue-400 hover:underline">4. Benachrichtigungen</a></li>
                        </ul>
                    </div>
                    <section id="add" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                            Favoriten hinzufügen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Veranstaltung als Favorit markieren</h3>
                            <ol class="list-decimal list-inside space-y-3 text-gray-700 dark:text-gray-300">
                                <li>Öffnen Sie die Detailseite einer Veranstaltung</li>
                                <li>Klicken Sie auf das Herz-Symbol ❤️ oben rechts</li>
                                <li>Das Herz färbt sich rot - die Veranstaltung ist jetzt gespeichert</li>
                                <li>Sie finden sie unter <strong>Meine Favoriten</strong></li>
                            </ol>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🔴 Favorit hinzufügen</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Klick auf das leere Herz ♡ speichert die Veranstaltung
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">⚪ Favorit entfernen</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Klick auf das rote Herz ❤️ entfernt die Veranstaltung
                                    </p>
                                </div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Vorteil:</strong> Sie werden automatisch benachrichtigt, wenn sich etwas an einer favorisierten Veranstaltung ändert!</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="manage" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                            Favoriten verwalten
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Ihre Favoritenliste aufrufen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Klicken Sie in der Sidebar auf <strong>Favoriten</strong></li>
                                <li>Oder gehen Sie zu Ihrem Profil → <strong>Meine Favoriten</strong></li>
                            </ol>
                            <h3 class="text-xl font-semibold mt-6">Ansichtsoptionen</h3>
                            <div class="space-y-3">
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="flex-1">
                                        <strong>📋 Listenansicht</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Kompakte Übersicht aller Favoriten mit wichtigsten Informationen
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="flex-1">
                                        <strong>📅 Kalenderansicht</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Favoriten chronologisch nach Datum sortiert
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="flex-1">
                                        <strong>🏷️ Nach Kategorien</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Gruppiert nach Themenbereichen
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold mt-6">Filteroptionen</h3>
                            <ul class="list-disc list-inside ml-4 space-y-1 text-gray-700 dark:text-gray-300">
                                <li>Nach Datum (kommende, vergangene)</li>
                                <li>Nach Kategorie</li>
                                <li>Nach Ort (online, vor Ort)</li>
                                <li>Nach Status (offen für Buchungen, ausgebucht)</li>
                            </ul>
                        </div>
                    </section>
                    <section id="organize" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                            Mit Listen organisieren
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Organisieren Sie Ihre Favoriten in thematischen Listen für bessere Übersicht.
                            </p>
                            <h3 class="text-xl font-semibold mt-6">Neue Liste erstellen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Meine Favoriten</strong></li>
                                <li>Klicken Sie auf <strong>"Neue Liste"</strong></li>
                                <li>Geben Sie einen Namen ein (z.B. "Digitalisierung", "Für später")</li>
                                <li>Optional: Fügen Sie eine Beschreibung hinzu</li>
                                <li>Klicken Sie auf <strong>"Liste erstellen"</strong></li>
                            </ol>
                            <h3 class="text-xl font-semibold mt-6">Events zu Listen hinzufügen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Klicken Sie bei einem Favoriten auf das Drei-Punkte-Menü</li>
                                <li>Wählen Sie <strong>"Zu Liste hinzufügen"</strong></li>
                                <li>Wählen Sie eine oder mehrere Listen aus</li>
                                <li>Das Event erscheint nun in den gewählten Listen</li>
                            </ol>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Tipp:</strong> Ein Event kann in mehreren Listen gleichzeitig sein!</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="notifications" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                            Benachrichtigungen zu Favoriten
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Bleiben Sie automatisch auf dem Laufenden über Ihre favorisierten Veranstaltungen.
                            </p>
                            <h3 class="text-xl font-semibold mt-6">Automatische Benachrichtigungen</h3>
                            <div class="space-y-3">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📝 Änderungen an Events</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Zeit, Ort oder Inhalt wurde geändert
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🎫 Ticket-Verfügbarkeit</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Wenn ein ausgebuchtes Event wieder Plätze frei hat
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">⏰ Erinnerung vor Buchungsende</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        24h bevor die Buchungsfrist endet
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">❌ Event-Absage</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Wenn eine favorisierte Veranstaltung abgesagt wird
                                    </p>
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold mt-6">Benachrichtigungen anpassen</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Gehen Sie zu <strong>Einstellungen</strong> → <strong>Benachrichtigungen</strong> um festzulegen, 
                                welche Updates Sie zu Favoriten erhalten möchten.
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
