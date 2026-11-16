<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center mb-2">
                    <a href="{{ route('help.index') }}"
                        class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Datenschutz & Privatsphäre</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Ihre Daten und Privatsphäre schützen</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    <div class="mb-8">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Ihre Privatsphäre ist uns wichtig. Hier erfahren Sie, wie Sie Ihre Daten verwalten und Ihre
                            Privatsphäre-Einstellungen anpassen können.
                        </p>
                    </div>
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Inhalt</h2>
                        <ul class="space-y-2">
                            <li><a href="#visibility" class="text-blue-600 dark:text-blue-400 hover:underline">1.
                                    Profil-Sichtbarkeit</a></li>
                            <li><a href="#data" class="text-blue-600 dark:text-blue-400 hover:underline">2. Ihre
                                    Daten</a></li>
                            <li><a href="#permissions" class="text-blue-600 dark:text-blue-400 hover:underline">3.
                                    Berechtigungen</a></li>
                            <li><a href="#gdpr" class="text-blue-600 dark:text-blue-400 hover:underline">4.
                                    DSGVO-Rechte</a></li>
                        </ul>
                    </div>
                    <section id="visibility" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span
                                class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                            Profil-Sichtbarkeit steuern
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Sie können genau festlegen, wer welche Informationen Ihres Profils sehen kann.
                            </p>
                            <h3 class="text-xl font-semibold mt-6">Sichtbarkeits-Stufen</h3>
                            <div class="space-y-4">
                                <div
                                    class="border dark:border-gray-700 rounded-lg p-4 bg-green-50 dark:bg-green-900/20">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-2xl mr-2">🔓</span> Öffentlich
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Alle Nutzer können Ihr Profil sehen
                                    </p>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside ml-4">
                                        <li>Empfohlen für aktives Networking</li>
                                        <li>Maximale Sichtbarkeit in der Community</li>
                                        <li>Erhöht Chancen auf neue Verbindungen</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4 bg-blue-50 dark:bg-blue-900/20">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-2xl mr-2">👥</span> Nur Verbindungen
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Nur Nutzer, denen Sie folgen oder die Ihnen folgen
                                    </p>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside ml-4">
                                        <li>Ausgewogene Privatsphäre</li>
                                        <li>Kontrollierter Zugriff</li>
                                        <li>Ideal für selektives Networking</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4 bg-red-50 dark:bg-red-900/20">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-2xl mr-2">🔒</span> Privat
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Minimale Informationen sichtbar
                                    </p>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside ml-4">
                                        <li>Nur Name und Profilbild</li>
                                        <li>Keine Aktivitäten sichtbar</li>
                                        <li>Maximale Privatsphäre</li>
                                    </ul>
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold mt-6">Einstellungen anpassen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Einstellungen</strong> → <strong>Datenschutz</strong></li>
                                <li>Wählen Sie Ihre bevorzugte Sichtbarkeits-Stufe</li>
                                <li>Klicken Sie auf <strong>"Speichern"</strong></li>
                            </ol>
                        </div>
                    </section>
                    <section id="data" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span
                                class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                            Ihre Daten verwalten
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Welche Daten speichern wir?</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📋 Profildaten</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside">
                                        <li>Name und E-Mail</li>
                                        <li>Profilbild</li>
                                        <li>Bio und Interessen</li>
                                        <li>Institution/Schule</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📅 Aktivitätsdaten</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside">
                                        <li>Gebuchte Events</li>
                                        <li>Bewertungen</li>
                                        <li>Favoriten</li>
                                        <li>Verbindungen</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🔐 Sicherheitsdaten</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside">
                                        <li>Passwort (verschlüsselt)</li>
                                        <li>Login-Historie</li>
                                        <li>Sicherheitseinstellungen</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📊 Nutzungsdaten</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside">
                                        <li>Seitenaufrufe (anonymisiert)</li>
                                        <li>Suchverläufe</li>
                                        <li>Einstellungen</li>
                                    </ul>
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold mt-6">Daten exportieren</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Einstellungen</strong> → <strong>Datenschutz</strong></li>
                                <li>Klicken Sie auf <strong>"Meine Daten exportieren"</strong></li>
                                <li>Sie erhalten eine JSON-Datei mit allen Ihren Daten</li>
                            </ol>
                        </div>
                    </section>
                    <section id="permissions" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span
                                class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                            Berechtigungen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Kontrollieren Sie, welche Funktionen auf Ihre Daten zugreifen dürfen.
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <input type="checkbox" checked disabled class="mt-1 mr-3">
                                    <div class="flex-1">
                                        <strong>📧 E-Mail-Benachrichtigungen</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Erlauben Sie uns, Ihnen wichtige Updates per E-Mail zu senden
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <input type="checkbox" checked disabled class="mt-1 mr-3">
                                    <div class="flex-1">
                                        <strong>🔔 Push-Benachrichtigungen</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Echtzeit-Updates im Browser
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <input type="checkbox" disabled class="mt-1 mr-3">
                                    <div class="flex-1">
                                        <strong>📊 Analyse & Verbesserung</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Anonymisierte Nutzungsdaten zur Verbesserung der Plattform
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <input type="checkbox" disabled class="mt-1 mr-3">
                                    <div class="flex-1">
                                        <strong>📢 Marketing</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Informationen über neue Features und Angebote
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="gdpr" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span
                                class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                            Ihre DSGVO-Rechte
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Gemäß der Datenschutz-Grundverordnung (DSGVO) haben Sie folgende Rechte:
                            </p>
                            <div class="space-y-4">
                                <div class="border-l-4 border-blue-400 pl-4 bg-blue-50 dark:bg-blue-900/20 p-4">
                                    <h4 class="font-semibold mb-2">📖 Recht auf Auskunft</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Sie können jederzeit Auskunft über Ihre gespeicherten Daten erhalten.
                                    </p>
                                </div>
                                <div class="border-l-4 border-blue-400 pl-4 bg-blue-50 dark:bg-blue-900/20 p-4">
                                    <h4 class="font-semibold mb-2">✏️ Recht auf Berichtigung</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Fehlerhafte Daten können Sie jederzeit in Ihren Einstellungen korrigieren.
                                    </p>
                                </div>
                                <div class="border-l-4 border-blue-400 pl-4 bg-blue-50 dark:bg-blue-900/20 p-4">
                                    <h4 class="font-semibold mb-2">🗑️ Recht auf Löschung</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Sie können Ihr Konto und alle Daten jederzeit löschen lassen.
                                    </p>
                                </div>
                                <div class="border-l-4 border-blue-400 pl-4 bg-blue-50 dark:bg-blue-900/20 p-4">
                                    <h4 class="font-semibold mb-2">📦 Recht auf Datenübertragbarkeit</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Exportieren Sie Ihre Daten in einem maschinenlesbaren Format.
                                    </p>
                                </div>
                                <div class="border-l-4 border-blue-400 pl-4 bg-blue-50 dark:bg-blue-900/20 p-4">
                                    <h4 class="font-semibold mb-2">🚫 Recht auf Widerspruch</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Widersprechen Sie der Verarbeitung Ihrer Daten für bestimmte Zwecke.
                                    </p>
                                </div>
                            </div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mt-6">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <p class="font-semibold">Fragen zum Datenschutz?</p>
                                        <p class="text-sm mt-1">Kontaktieren Sie unseren Datenschutzbeauftragten unter:
                                            <a href="mailto:datenschutz@bildungsportal.de"
                                                class="text-blue-600 dark:text-blue-400 hover:underline">datenschutz@bildungsportal.de</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
