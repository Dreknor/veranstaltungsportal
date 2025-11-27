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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Veranstaltungen buchen</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">So buchen Sie Tickets in wenigen Schritten</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Schritt-für-Schritt Anleitung</h2>
                        <div class="space-y-6">
                            <div class="border-l-4 border-blue-600 pl-4">
                                <h3 class="text-xl font-semibold mb-2">1. Veranstaltung auswählen</h3>
                                <p class="text-gray-700 dark:text-gray-300">Klicken Sie auf einer Event-Seite auf den Button <strong>"Jetzt buchen"</strong></p>
                                <div class="mt-2 bg-blue-50 dark:bg-blue-900/20 p-3 rounded text-sm">
                                    <p class="font-semibold text-blue-900 dark:text-blue-200">💡 Events mit mehreren Terminen</p>
                                    <p class="text-gray-700 dark:text-gray-300 mt-1">
                                        Einige Veranstaltungen bestehen aus mehreren Terminen (z.B. ein 8-Wochen-Kurs).
                                        Bei diesen Events sehen Sie alle Termine aufgelistet. Die Buchung gilt automatisch für <strong>alle Termine</strong> -
                                        es ist nicht möglich, nur einzelne Termine zu buchen.
                                    </p>
                                </div>
                            </div>
                            <div class="border-l-4 border-blue-600 pl-4">
                                <h3 class="text-xl font-semibold mb-2">2. Ticket-Typ wählen</h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-2">Wählen Sie den passenden Ticket-Typ (z.B. Standard, Ermäßigt, VIP) und die gewünschte Anzahl</p>
                                <ul class="list-disc list-inside ml-4 text-sm text-gray-600 dark:text-gray-400">
                                    <li>Prüfen Sie die Verfügbarkeit</li>
                                    <li>Beachten Sie eventuelle Limits pro Person</li>
                                </ul>
                            </div>
                            <div class="border-l-4 border-blue-600 pl-4">
                                <h3 class="text-xl font-semibold mb-2">3. Rabattcode eingeben (optional)</h3>
                                <p class="text-gray-700 dark:text-gray-300">Wenn Sie einen Rabattcode haben, geben Sie ihn ein und klicken Sie auf "Anwenden"</p>
                            </div>
                            <div class="border-l-4 border-blue-600 pl-4">
                                <h3 class="text-xl font-semibold mb-2">4. Persönliche Daten eingeben</h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-2">Füllen Sie das Formular aus:</p>
                                <ul class="list-disc list-inside ml-4 text-sm text-gray-600 dark:text-gray-400">
                                    <li>Vor- und Nachname</li>
                                    <li>E-Mail-Adresse (für Bestätigung und Tickets)</li>
                                    <li>Telefonnummer (optional, aber empfohlen)</li>
                                    <li>Zusätzliche Informationen (falls vom Veranstalter gewünscht)</li>
                                </ul>
                            </div>
                            <div class="border-l-4 border-blue-600 pl-4">
                                <h3 class="text-xl font-semibold mb-2">5. Buchung abschließen</h3>
                                <p class="text-gray-700 dark:text-gray-300">Prüfen Sie alle Angaben und klicken Sie auf <strong>"Verbindlich buchen"</strong></p>
                            </div>
                            <div class="border-l-4 border-green-600 pl-4 bg-green-50 dark:bg-green-900/20 p-4 rounded">
                                <h3 class="text-xl font-semibold mb-2">6. Bestätigung erhalten</h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-2">Sie erhalten sofort:</p>
                                <ul class="list-disc list-inside ml-4 text-sm text-gray-600 dark:text-gray-400">
                                    <li>Buchungsbestätigung per E-Mail</li>
                                    <li>Buchungsnummer zur Nachverfolgung</li>
                                    <li>Zahlungsinformationen (bei kostenpflichtigen Events)</li>
                                    <li>Tickets (nach Zahlungseingang)</li>
                                </ul>
                            </div>
                        </div>
                    </section>
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Zahlungsarten</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h3 class="font-semibold mb-2">💳 Überweisung</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Sie erhalten Bankdaten in der Bestätigung. Bitte als Verwendungszweck die Buchungsnummer angeben.</p>
                            </div>
                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h3 class="font-semibold mb-2">🆓 Kostenlose Events</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Bei kostenlosen Veranstaltungen erhalten Sie sofort Zugang und Tickets.</p>
                            </div>
                        </div>
                    </section>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4">
                        <p><strong>Wichtig:</strong> Tickets für Präsenz-Events werden erst nach Zahlungseingang per E-Mail versendet.
                        Zugangsdaten für Online-Events erhalten Sie ebenfalls nach Bezahlung.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
