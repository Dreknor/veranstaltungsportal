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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Benachrichtigungen</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Benachrichtigungen verwalten und einstellen</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    <div class="mb-8">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Bleiben Sie auf dem Laufenden mit personalisierten Benachrichtigungen zu Events, Buchungen und Netzwerk-Aktivitäten.
                        </p>
                    </div>
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Inhalt</h2>
                        <ul class="space-y-2">
                            <li><a href="#types" class="text-blue-600 dark:text-blue-400 hover:underline">1. Benachrichtigungstypen</a></li>
                            <li><a href="#settings" class="text-blue-600 dark:text-blue-400 hover:underline">2. Einstellungen anpassen</a></li>
                            <li><a href="#channels" class="text-blue-600 dark:text-blue-400 hover:underline">3. Benachrichtigungskanäle</a></li>
                            <li><a href="#manage" class="text-blue-600 dark:text-blue-400 hover:underline">4. Benachrichtigungen verwalten</a></li>
                        </ul>
                    </div>
                    <section id="types" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                            Benachrichtigungstypen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📅 Event-Benachrichtigungen</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• Neue Events in Ihren Interessengebieten</li>
                                        <li>• Erinnerungen vor Veranstaltungsbeginn</li>
                                        <li>• Änderungen an gebuchten Events</li>
                                        <li>• Event-Absagen</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🎫 Buchungs-Benachrichtigungen</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• Buchungsbestätigungen</li>
                                        <li>• Zahlungsbestätigungen</li>
                                        <li>• Ticket-Verfügbarkeit</li>
                                        <li>• Stornierungsbestätigungen</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🤝 Netzwerk-Benachrichtigungen</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• Neue Verbindungsanfragen</li>
                                        <li>• Akzeptierte Anfragen</li>
                                        <li>• Neue Follower</li>
                                        <li>• Aktivitäten von Verbindungen</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🏆 System-Benachrichtigungen</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• Neue Badges erhalten</li>
                                        <li>• Zertifikate verfügbar</li>
                                        <li>• Wichtige Systemupdates</li>
                                        <li>• Sicherheitshinweise</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="settings" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                            Einstellungen anpassen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Klicken Sie oben rechts auf Ihren Namen</li>
                                <li>Wählen Sie <strong>"Einstellungen"</strong></li>
                                <li>Navigieren Sie zu <strong>"Benachrichtigungen"</strong></li>
                                <li>Aktivieren/Deaktivieren Sie die gewünschten Benachrichtigungen</li>
                                <li>Klicken Sie auf <strong>"Speichern"</strong></li>
                            </ol>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Tipp:</strong> Sie können für jeden Benachrichtigungstyp getrennt einstellen, ob Sie E-Mails oder nur In-App-Benachrichtigungen erhalten möchten.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="channels" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                            Benachrichtigungskanäle
                        </h2>
                        <div class="ml-11 space-y-4">
                            <div class="space-y-4">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-2xl mr-2">📧</span> E-Mail-Benachrichtigungen
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Erhalten Sie wichtige Updates direkt in Ihr E-Mail-Postfach.
                                    </p>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside ml-4">
                                        <li>Sofortige Zustellung</li>
                                        <li>Detaillierte Informationen</li>
                                        <li>Archivierbar für spätere Referenz</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-2xl mr-2">🔔</span> In-App-Benachrichtigungen
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Sehen Sie Benachrichtigungen direkt im Portal.
                                    </p>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside ml-4">
                                        <li>Echtzeit-Updates</li>
                                        <li>Badge mit Anzahl ungelesener Nachrichten</li>
                                        <li>Zentrale Übersicht aller Benachrichtigungen</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="manage" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                            Benachrichtigungen verwalten
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Benachrichtigungszentrale</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Klicken Sie auf das Glockensymbol oben rechts, um alle Ihre Benachrichtigungen zu sehen.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">Als gelesen markieren</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Einzeln oder alle auf einmal als gelesen markieren
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">Löschen</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Einzelne Benachrichtigungen entfernen
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">Filtern</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Nach Typ oder Datum filtern
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">Direkt reagieren</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Auf Anfragen direkt aus der Benachrichtigung reagieren
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
