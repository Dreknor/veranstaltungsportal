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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Häufige Probleme</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Lösungen für die häufigsten Fragen</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">


                    <!-- FAQ Accordion -->
                    <div class="space-y-4">
                        <!-- Account -->
                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Ich habe mein Passwort vergessen
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <ol class="list-decimal list-inside space-y-2">
                                    <li>Klicken Sie auf der Anmeldeseite auf "Passwort vergessen?"</li>
                                    <li>Geben Sie Ihre E-Mail-Adresse ein</li>
                                    <li>Sie erhalten einen Link zum Zurücksetzen per E-Mail</li>
                                    <li>Klicken Sie auf den Link und wählen Sie ein neues Passwort</li>
                                </ol>
                                <div class="mt-3 bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded">
                                    <strong>Hinweis:</strong> Der Link ist 60 Minuten gültig. Prüfen Sie auch Ihren Spam-Ordner.
                                </div>
                            </div>
                        </details>

                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Ich habe keine Bestätigungs-E-Mail erhalten
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <p class="mb-3">Wenn Sie keine Bestätigungs-E-Mail erhalten haben:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Prüfen Sie Ihren Spam/Junk-Ordner</li>
                                    <li>Warten Sie einige Minuten (Versand kann verzögert sein)</li>
                                    <li>Prüfen Sie, ob die E-Mail-Adresse korrekt ist</li>
                                    <li>Fordern Sie eine neue Bestätigungsmail an (Button in Ihrem Profil)</li>
                                </ul>
                            </div>
                        </details>

                        <!-- Booking -->
                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Ich habe kein Ticket erhalten
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <p class="mb-3">Tickets werden nur bei bezahlten Buchungen versendet:</p>
                                <ol class="list-decimal list-inside space-y-2">
                                    <li>Prüfen Sie den Zahlungsstatus in "Meine Buchungen"</li>
                                    <li>Bei "Bezahlt" sollten Sie das Ticket in der Buchungsbestätigung finden</li>
                                    <li>Laden Sie das Ticket direkt aus Ihrer Buchungsübersicht herunter</li>
                                    <li>Prüfen Sie Ihren Spam-Ordner für die E-Mail</li>
                                </ol>
                                <div class="mt-3 bg-blue-50 dark:bg-blue-900/20 p-3 rounded">
                                    <strong>Hinweis:</strong> Bei Online-Events erhalten Sie nach Bezahlung die Zugangsdaten statt eines PDF-Tickets.
                                </div>
                            </div>
                        </details>

                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Wie kann ich eine Buchung stornieren?
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <ol class="list-decimal list-inside space-y-2">
                                    <li>Gehen Sie zu "Meine Buchungen"</li>
                                    <li>Klicken Sie auf die betreffende Buchung</li>
                                    <li>Klicken Sie auf "Buchung stornieren"</li>
                                    <li>Bestätigen Sie die Stornierung</li>
                                </ol>
                                <div class="mt-3 bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded">
                                    <strong>Wichtig:</strong> Beachten Sie die Stornierungsbedingungen des Veranstalters.
                                    Diese finden Sie in den Event-Details.
                                </div>
                            </div>
                        </details>

                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Der Rabattcode funktioniert nicht
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <p class="mb-3">Mögliche Gründe:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Code ist abgelaufen (prüfen Sie das Gültigkeitsdatum)</li>
                                    <li>Code wurde bereits zu oft verwendet (maximale Nutzungsanzahl erreicht)</li>
                                    <li>Code gilt nur für bestimmte Ticket-Typen</li>
                                    <li>Mindestanzahl an Tickets nicht erreicht</li>
                                    <li>Tippfehler im Code (Codes sind case-sensitive)</li>
                                </ul>
                            </div>
                        </details>

                        <!-- Technical -->
                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Die Seite lädt nicht richtig
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <p class="mb-3">Versuchen Sie folgende Schritte:</p>
                                <ol class="list-decimal list-inside space-y-2">
                                    <li>Aktualisieren Sie die Seite (F5 oder Strg+R)</li>
                                    <li>Leeren Sie den Browser-Cache (Strg+Shift+Entf)</li>
                                    <li>Versuchen Sie es mit einem anderen Browser</li>
                                    <li>Deaktivieren Sie Browser-Erweiterungen vorübergehend</li>
                                    <li>Prüfen Sie Ihre Internetverbindung</li>
                                </ol>
                            </div>
                        </details>

                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Profilbild lässt sich nicht hochladen
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <p class="mb-3">Prüfen Sie folgende Punkte:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Datei ist nicht größer als 2MB</li>
                                    <li>Format ist JPG, PNG oder GIF</li>
                                    <li>Dateiname enthält keine Sonderzeichen</li>
                                    <li>Browser erlaubt Datei-Uploads</li>
                                </ul>
                            </div>
                        </details>

                        <!-- Notifications -->
                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Ich erhalte keine Benachrichtigungen
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <ol class="list-decimal list-inside space-y-2">
                                    <li>Gehen Sie zu Einstellungen → Benachrichtigungen</li>
                                    <li>Prüfen Sie, ob die gewünschten Benachrichtigungen aktiviert sind</li>
                                    <li>Prüfen Sie Ihren Spam-Ordner</li>
                                    <li>Stellen Sie sicher, dass Ihre E-Mail-Adresse verifiziert ist</li>
                                </ol>
                            </div>
                        </details>

                        <!-- Payment -->
                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Zahlung wurde abgebucht aber Buchung zeigt "Ausstehend"
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <p class="mb-3">Das kann verschiedene Gründe haben:</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Zahlung wird noch verarbeitet (kann bis zu 24h dauern)</li>
                                    <li>Aktualisieren Sie die Seite</li>
                                    <li>Prüfen Sie Ihr E-Mail-Postfach auf Zahlungsbestätigung</li>
                                </ul>
                                <div class="mt-3 bg-red-50 dark:bg-red-900/20 p-3 rounded">
                                    <strong>Wenn das Problem länger als 24h besteht:</strong> Kontaktieren Sie bitte unseren Support
                                    mit Ihrer Buchungsnummer und einem Screenshot Ihrer Banküberweisung.
                                </div>
                            </div>
                        </details>

                        <!-- Privacy -->
                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Wie kann ich meine Daten exportieren/löschen?
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <p class="mb-3">Gemäß DSGVO haben Sie folgende Rechte:</p>
                                <p class="font-semibold mb-2">Daten exportieren:</p>
                                <ol class="list-decimal list-inside space-y-1 ml-4 mb-4">
                                    <li>Einstellungen → Datenschutz</li>
                                    <li>Klicken Sie auf "Meine Daten exportieren"</li>
                                    <li>Sie erhalten eine JSON-Datei mit allen Ihren Daten</li>
                                </ol>
                                <p class="font-semibold mb-2">Konto löschen:</p>
                                <ol class="list-decimal list-inside space-y-1 ml-4">
                                    <li>Einstellungen → Konto</li>
                                    <li>Scrollen Sie nach unten zu "Konto löschen"</li>
                                    <li>Bestätigen Sie die Löschung</li>
                                </ol>
                                <div class="mt-3 bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded">
                                    <strong>Hinweis:</strong> Bei aktiven Buchungen kann das Konto nicht sofort gelöscht werden.
                                </div>
                            </div>
                        </details>

                        <!-- Contact -->
                        <details class="border dark:border-gray-700 rounded-lg p-6">
                            <summary class="cursor-pointer font-semibold text-lg">
                                ❓ Wie kontaktiere ich einen Veranstalter?
                            </summary>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                <p class="mb-3">Auf jeder Veranstaltungsseite finden Sie die Kontaktinformationen des Veranstalters:</p>
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li>E-Mail-Adresse</li>
                                    <li>Telefonnummer (falls angegeben)</li>
                                    <li>Website (falls angegeben)</li>
                                </ul>
                                <p class="mt-3">Scrollen Sie auf der Event-Seite zum Abschnitt "Veranstalter".</p>
                            </div>
                        </details>
                    </div>

                    <!-- Still Need Help -->
                    <div class="mt-12 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-8 text-center">
                        <h2 class="text-2xl font-bold mb-4">Immer noch Probleme?</h2>
                        <p class="text-gray-700 dark:text-gray-300 mb-6">
                            Wenn Sie hier keine Lösung gefunden haben, kontaktieren Sie uns gerne direkt.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="mailto:support@bildungsportal.de"
                               class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                E-Mail an Support
                            </a>
                            <a href="{{ route('help.index') }}"
                               class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Weitere Hilfe-Artikel
                            </a>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="mt-12 pt-8 border-t dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('help.article', ['category' => 'user', 'article' => 'privacy']) }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Vorheriger Artikel: Datenschutz
                            </a>
                            <a href="{{ route('help.index') }}"
                               class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                                Zurück zur Übersicht
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

