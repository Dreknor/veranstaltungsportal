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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Profil & Einstellungen</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Ihr Profil anpassen und Einstellungen ändern</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    <!-- Introduction -->
                    <div class="mb-8">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Ein vollständiges und gut gepflegtes Profil hilft Ihnen dabei, das Beste aus dem Bildungsportal 
                            herauszuholen und sich mit anderen Pädagogen zu vernetzen.
                        </p>
                    </div>
                    <!-- Table of Contents -->
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Inhalt</h2>
                        <ul class="space-y-2">
                            <li><a href="#profile-info" class="text-blue-600 dark:text-blue-400 hover:underline">1. Profilinformationen</a></li>
                            <li><a href="#profile-photo" class="text-blue-600 dark:text-blue-400 hover:underline">2. Profilbild hochladen</a></li>
                            <li><a href="#password" class="text-blue-600 dark:text-blue-400 hover:underline">3. Passwort ändern</a></li>
                            <li><a href="#notifications" class="text-blue-600 dark:text-blue-400 hover:underline">4. Benachrichtigungen</a></li>
                            <li><a href="#privacy" class="text-blue-600 dark:text-blue-400 hover:underline">5. Privatsphäre & Sichtbarkeit</a></li>
                            <li><a href="#data" class="text-blue-600 dark:text-blue-400 hover:underline">6. Daten exportieren/löschen</a></li>
                        </ul>
                    </div>
                    <!-- Profile Info -->
                    <section id="profile-info" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                            Profilinformationen bearbeiten
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Wo finde ich meine Profileinstellungen?</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Klicken Sie oben rechts auf Ihren Namen</li>
                                <li>Wählen Sie <strong>"Einstellungen"</strong> aus dem Dropdown-Menü</li>
                                <li>Navigieren Sie zum Tab <strong>"Profil"</strong></li>
                            </ol>
                            <h3 class="text-xl font-semibold mt-6">Welche Informationen kann ich bearbeiten?</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📋 Grunddaten</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• Vorname und Nachname</li>
                                        <li>• E-Mail-Adresse</li>
                                        <li>• Telefonnummer (optional)</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">✏️ Zusatzinformationen</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• Bio/Über mich (max. 500 Zeichen)</li>
                                        <li>• Schule/Institution</li>
                                        <li>• Fachbereiche</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🎯 Interessen</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• Themenbereiche auswählen</li>
                                        <li>• Für personalisierte Empfehlungen</li>
                                        <li>• Mehrfachauswahl möglich</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🔗 Social Media</h4>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                        <li>• Website/Blog</li>
                                        <li>• Twitter/LinkedIn</li>
                                        <li>• Xing/GitHub</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Tipp:</strong> Je vollständiger Ihr Profil, desto besser können wir Ihnen passende Fortbildungen empfehlen!</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Profile Photo -->
                    <section id="profile-photo" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                            Profilbild hochladen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Schritt-für-Schritt Anleitung</h3>
                            <ol class="list-decimal list-inside space-y-3 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Einstellungen</strong> → <strong>Profil</strong></li>
                                <li>Klicken Sie auf das Kamera-Symbol beim aktuellen Profilbild</li>
                                <li>Wählen Sie ein Bild von Ihrem Computer aus</li>
                                <li>Das Bild wird automatisch zugeschnitten (quadratisches Format)</li>
                                <li>Klicken Sie auf <strong>"Speichern"</strong></li>
                            </ol>
                            <h3 class="text-xl font-semibold mt-6">Technische Anforderungen</h3>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mt-2">
                                <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                                    <li>✅ <strong>Dateiformate:</strong> JPG, PNG, GIF</li>
                                    <li>✅ <strong>Maximale Größe:</strong> 2 MB</li>
                                    <li>✅ <strong>Empfohlene Auflösung:</strong> Mindestens 400x400 Pixel</li>
                                    <li>✅ <strong>Seitenverhältnis:</strong> Quadratisch (wird automatisch zugeschnitten)</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Empfehlung:</strong> Verwenden Sie ein professionelles Foto, das Sie gut erkennbar zeigt. Dies erhöht das Vertrauen bei Vernetzungen!</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Password -->
                    <section id="password" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                            Passwort ändern
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Passwort sicher ändern</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Einstellungen</strong> → <strong>Passwort</strong></li>
                                <li>Geben Sie Ihr aktuelles Passwort ein</li>
                                <li>Wählen Sie ein neues, sicheres Passwort
                                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                        <li>Mindestens 8 Zeichen</li>
                                        <li>Groß- und Kleinbuchstaben</li>
                                        <li>Mindestens eine Zahl</li>
                                        <li>Sonderzeichen empfohlen</li>
                                    </ul>
                                </li>
                                <li>Bestätigen Sie das neue Passwort durch erneute Eingabe</li>
                                <li>Klicken Sie auf <strong>"Passwort ändern"</strong></li>
                            </ol>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Sicherheitshinweis:</strong> Nach der Passwortänderung werden Sie auf allen Geräten abgemeldet und müssen sich mit dem neuen Passwort anmelden.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Notifications -->
                    <section id="notifications" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                            Benachrichtigungseinstellungen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Unter <strong>Einstellungen</strong> → <strong>Benachrichtigungen</strong> können Sie genau festlegen, 
                                worüber Sie informiert werden möchten.
                            </p>
                            <h3 class="text-xl font-semibold mt-6">Verfügbare Benachrichtigungstypen</h3>
                            <div class="space-y-3 mt-4">
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <input type="checkbox" checked disabled class="mt-1 mr-3">
                                    <div>
                                        <strong>Neue Events in Lieblingskategorien</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Benachrichtigung, wenn neue Veranstaltungen in Ihren favorisierten Themenbereichen erstellt werden</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <input type="checkbox" checked disabled class="mt-1 mr-3">
                                    <div>
                                        <strong>Event-Erinnerungen</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Automatische Erinnerungen 24h und 3h vor Veranstaltungsbeginn</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <input type="checkbox" checked disabled class="mt-1 mr-3">
                                    <div>
                                        <strong>Buchungsbestätigungen</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Bestätigung bei erfolgreichen Buchungen und Zahlungen</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <input type="checkbox" checked disabled class="mt-1 mr-3">
                                    <div>
                                        <strong>Event-Änderungen</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Information über Änderungen an gebuchten Veranstaltungen</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                    <input type="checkbox" disabled class="mt-1 mr-3">
                                    <div>
                                        <strong>Neue Verbindungsanfragen</strong>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Benachrichtigung, wenn jemand Ihnen folgen möchte</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Privacy -->
                    <section id="privacy" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
                            Privatsphäre & Sichtbarkeit
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Unter <strong>Einstellungen</strong> → <strong>Datenschutz</strong> können Sie steuern, 
                                wer was von Ihrem Profil sehen kann.
                            </p>
                            <h3 class="text-xl font-semibold mt-6">Sichtbarkeitseinstellungen</h3>
                            <div class="space-y-4 mt-4">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🔓 Öffentliches Profil</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Ihr Profil ist für alle Nutzer sichtbar. Empfohlen für Networking.
                                    </p>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside ml-4">
                                        <li>Profilbild und Name sichtbar</li>
                                        <li>Bio und Interessen sichtbar</li>
                                        <li>Besuchte Events sichtbar</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">👥 Nur für Verbindungen</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Nur Nutzer, denen Sie folgen, können Ihr vollständiges Profil sehen.
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🔒 Privat</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Minimale Sichtbarkeit. Nur Name und Profilbild sind sichtbar.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Data Export/Delete -->
                    <section id="data" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">6</span>
                            Daten exportieren/löschen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Gemäß DSGVO haben Sie volle Kontrolle über Ihre Daten.
                            </p>
                            <h3 class="text-xl font-semibold mt-6">Daten exportieren</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Einstellungen</strong> → <strong>Datenschutz</strong></li>
                                <li>Klicken Sie auf <strong>"Meine Daten exportieren"</strong></li>
                                <li>Sie erhalten eine JSON-Datei mit allen Ihren Daten zum Download</li>
                            </ol>
                            <h3 class="text-xl font-semibold mt-6">Konto löschen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Einstellungen</strong> → <strong>Konto</strong></li>
                                <li>Scrollen Sie nach unten zum Bereich <strong>"Gefahrenzone"</strong></li>
                                <li>Klicken Sie auf <strong>"Konto löschen"</strong></li>
                                <li>Bestätigen Sie die Löschung durch Eingabe Ihres Passworts</li>
                            </ol>
                            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="font-semibold">Wichtig bei Kontolöschung:</p>
                                        <ul class="text-sm mt-2 space-y-1">
                                            <li>• Alle Ihre Daten werden unwiderruflich gelöscht</li>
                                            <li>• Aktive Buchungen müssen vorher storniert werden</li>
                                            <li>• Die Löschung kann nicht rückgängig gemacht werden</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Navigation -->
                    <div class="mt-12 pt-8 border-t dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('help.article', ['category' => 'user', 'article' => 'manage-bookings']) }}" 
                               class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Vorheriger Artikel: Buchungen verwalten
                            </a>
                            <a href="{{ route('help.article', ['category' => 'user', 'article' => 'notifications']) }}" 
                               class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                Nächster Artikel: Benachrichtigungen
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
