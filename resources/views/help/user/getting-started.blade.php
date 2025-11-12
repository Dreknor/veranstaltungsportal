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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Erste Schritte</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Lernen Sie die Grundlagen der Plattform kennen</p>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">

                    <!-- Introduction -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold mb-4">Willkommen im Bildungsportal!</h1>
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Diese Anleitung hilft Ihnen dabei, die ersten Schritte auf unserer Plattform zu machen und
                            sich mit den wichtigsten Funktionen vertraut zu machen.
                        </p>
                    </div>

                    <!-- Table of Contents -->
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Inhalt</h2>
                        <ul class="space-y-2">
                            <li><a href="#registration" class="text-blue-600 dark:text-blue-400 hover:underline">1. Registrierung und Anmeldung</a></li>
                            <li><a href="#profile" class="text-blue-600 dark:text-blue-400 hover:underline">2. Profil einrichten</a></li>
                            <li><a href="#dashboard" class="text-blue-600 dark:text-blue-400 hover:underline">3. Dashboard kennenlernen</a></li>
                            <li><a href="#first-event" class="text-blue-600 dark:text-blue-400 hover:underline">4. Erste Veranstaltung finden</a></li>
                            <li><a href="#notifications" class="text-blue-600 dark:text-blue-400 hover:underline">5. Benachrichtigungen einrichten</a></li>
                            <li><a href="#next-steps" class="text-blue-600 dark:text-blue-400 hover:underline">6. Nächste Schritte</a></li>
                        </ul>
                    </div>

                    <!-- Registration -->
                    <section id="registration" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                            Registrierung und Anmeldung
                        </h2>

                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Neues Konto erstellen</h3>
                            <ol class="list-decimal list-inside space-y-3 text-gray-700 dark:text-gray-300">
                                <li>Klicken Sie oben rechts auf <strong>"Registrieren"</strong></li>
                                <li>Füllen Sie das Registrierungsformular aus:
                                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                        <li>Vorname und Nachname</li>
                                        <li>E-Mail-Adresse (wird als Login verwendet)</li>
                                        <li>Sicheres Passwort (mindestens 8 Zeichen)</li>
                                    </ul>
                                </li>
                                <li>Akzeptieren Sie die Nutzungsbedingungen und Datenschutzerklärung</li>
                                <li>Klicken Sie auf <strong>"Registrieren"</strong></li>
                                <li>Sie erhalten eine Bestätigungs-E-Mail – klicken Sie auf den Link zur Verifizierung</li>
                            </ol>

                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Wichtig:</strong> Prüfen Sie auch Ihren Spam-Ordner, falls die E-Mail nicht ankommt.</p>
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold mt-6">Anmelden</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Nach erfolgreicher Registrierung können Sie sich jederzeit mit Ihrer E-Mail-Adresse
                                und Ihrem Passwort anmelden. Klicken Sie dazu oben rechts auf <strong>"Anmelden"</strong>.
                            </p>
                        </div>
                    </section>

                    <!-- Profile Setup -->
                    <section id="profile" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                            Profil einrichten
                        </h2>

                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Ein vollständiges Profil hilft Ihnen dabei, das Beste aus dem Bildungsportal herauszuholen
                                und sich mit anderen Pädagogen zu vernetzen.
                            </p>

                            <h3 class="text-xl font-semibold">Profilbild hochladen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Klicken Sie oben rechts auf Ihren Namen</li>
                                <li>Wählen Sie <strong>"Einstellungen"</strong> → <strong>"Profil"</strong></li>
                                <li>Klicken Sie auf den Kamera-Button beim Profilbild</li>
                                <li>Wählen Sie ein Foto (max. 2MB, JPG/PNG/GIF)</li>
                                <li>Klicken Sie auf <strong>"Speichern"</strong></li>
                            </ol>

                            <h3 class="text-xl font-semibold mt-6">Zusätzliche Informationen</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Ergänzen Sie Ihr Profil mit weiteren Informationen:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300 ml-4">
                                <li><strong>Telefonnummer:</strong> Für wichtige Benachrichtigungen (optional)</li>
                                <li><strong>Bio:</strong> Stellen Sie sich kurz vor (z.B. Ihre Fächer, Schule, Interessen)</li>
                                <li><strong>Interessen:</strong> Wählen Sie Kategorien aus, um personalisierte Empfehlungen zu erhalten</li>
                            </ul>

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

                    <!-- Dashboard -->
                    <section id="dashboard" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                            Dashboard kennenlernen
                        </h2>

                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Ihr persönliches Dashboard ist Ihre Zentrale. Hier finden Sie alle wichtigen Informationen auf einen Blick.
                            </p>

                            <h3 class="text-xl font-semibold">Hauptbereiche</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📊 Statistiken</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Sehen Sie Ihre Buchungen, besuchte Events und Fortbildungsstunden auf einen Blick.
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📅 Kommende Events</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Alle Ihre anstehenden Veranstaltungen chronologisch sortiert.
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">⭐ Empfehlungen</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Personalisierte Vorschläge basierend auf Ihren Interessen.
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🏆 Badges</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Ihre gesammelten Auszeichnungen und Fortschritte.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- First Event -->
                    <section id="first-event" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                            Erste Veranstaltung finden
                        </h2>

                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Es gibt mehrere Wege, passende Fortbildungen zu finden:
                            </p>

                            <h3 class="text-xl font-semibold">Veranstaltungsübersicht</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Klicken Sie in der Navigation auf <strong>"Veranstaltungen"</strong></li>
                                <li>Nutzen Sie die Filter:
                                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                        <li>Kategorien (z.B. Pädagogik, Digitalisierung, Schulentwicklung)</li>
                                        <li>Datum und Zeitraum</li>
                                        <li>Ort (vor Ort oder online)</li>
                                        <li>Preis</li>
                                    </ul>
                                </li>
                                <li>Klicken Sie auf eine Veranstaltung, um Details zu sehen</li>
                            </ol>

                            <h3 class="text-xl font-semibold mt-6">Kalender-Ansicht</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Nutzen Sie die <strong>Kalender-Ansicht</strong>, um Veranstaltungen nach Datum zu durchsuchen.
                                Diese Ansicht hilft Ihnen, freie Termine zu finden und Ihre Fortbildungen zu planen.
                            </p>

                            <h3 class="text-xl font-semibold mt-6">Favoriten speichern</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Interessante Veranstaltungen können Sie mit dem ❤️ Herz-Symbol als Favorit markieren.
                                So finden Sie sie später schnell wieder unter <strong>"Meine Favoriten"</strong>.
                            </p>
                        </div>
                    </section>

                    <!-- Notifications -->
                    <section id="notifications" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
                            Benachrichtigungen einrichten
                        </h2>

                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Bleiben Sie auf dem Laufenden mit personalisierten Benachrichtigungen.
                            </p>

                            <h3 class="text-xl font-semibold">Benachrichtigungseinstellungen anpassen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Einstellungen</strong> → <strong>Benachrichtigungen</strong></li>
                                <li>Wählen Sie aus, worüber Sie informiert werden möchten:
                                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                        <li>Neue Events in Ihren Lieblingskategorien</li>
                                        <li>Erinnerungen vor Veranstaltungen (24h und 3h)</li>
                                        <li>Buchungsbestätigungen und Zahlungsinformationen</li>
                                        <li>Änderungen an gebuchten Events</li>
                                        <li>Neue Verbindungsanfragen</li>
                                        <li>Badge-Benachrichtigungen</li>
                                    </ul>
                                </li>
                                <li>Speichern Sie Ihre Einstellungen</li>
                            </ol>

                            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Empfehlung:</strong> Aktivieren Sie Event-Erinnerungen, damit Sie keine wichtigen Fortbildungen verpassen!</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Next Steps -->
                    <section id="next-steps" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">6</span>
                            Nächste Schritte
                        </h2>

                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Jetzt sind Sie bereit, das Bildungsportal voll zu nutzen! Hier sind einige Vorschläge:
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'booking-events']) }}"
                                   class="border dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <h4 class="font-semibold mb-2 text-blue-600 dark:text-blue-400">
                                        📚 Veranstaltungen buchen
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Lernen Sie, wie Sie Tickets buchen und bezahlen.
                                    </p>
                                </a>

                                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'social-features']) }}"
                                   class="border dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <h4 class="font-semibold mb-2 text-blue-600 dark:text-blue-400">
                                        🤝 Vernetzen Sie sich
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Knüpfen Sie Kontakte mit anderen Pädagogen.
                                    </p>
                                </a>

                                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'badges']) }}"
                                   class="border dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <h4 class="font-semibold mb-2 text-blue-600 dark:text-blue-400">
                                        🏆 Badges sammeln
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Verfolgen Sie Ihren Lernfortschritt mit Badges.
                                    </p>
                                </a>

                                <a href="{{ route('help.article', ['category' => 'user', 'article' => 'favorites']) }}"
                                   class="border dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <h4 class="font-semibold mb-2 text-blue-600 dark:text-blue-400">
                                        ❤️ Favoriten nutzen
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Speichern Sie interessante Veranstaltungen.
                                    </p>
                                </a>
                            </div>
                        </div>
                    </section>

                    <!-- Navigation -->
                    <div class="mt-12 pt-8 border-t dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('help.index') }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Zurück zur Hilfe-Übersicht
                            </a>
                            <a href="{{ route('help.article', ['category' => 'user', 'article' => 'finding-events']) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                Nächster Artikel: Veranstaltungen finden
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

