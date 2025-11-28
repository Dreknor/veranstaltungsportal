<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('help.index') }}" class="hover:text-blue-600">Hilfe</a> ·
                <a href="{{ route('help.category','organizer') }}" class="hover:text-blue-600">Veranstalter</a> ·
                <span>Kommunikation mit Teilnehmern</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Kommunikation mit Teilnehmern</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">Halten Sie Ihre Teilnehmer informiert und engagiert.</p>

            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">1. Automatische E-Mail-Benachrichtigungen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Das System versendet automatisch:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Buchungsbestätigung:</strong> Sofort nach Buchung</li>
                        <li><strong>Zahlungsbestätigung:</strong> Nach Eingang der Zahlung (inkl. Tickets/Zugangsdaten)</li>
                        <li><strong>Event-Erinnerung:</strong> 24 Stunden vor Event-Start</li>
                        <li><strong>Event-Erinnerung:</strong> 3 Stunden vor Event-Start</li>
                        <li><strong>Stornierungsbestätigung:</strong> Bei Absage durch Teilnehmer</li>
                        <li><strong>Event-Absage:</strong> Bei Absage durch Veranstalter</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Teilnehmer können Erinnerungen in ihren Einstellungen deaktivieren.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">2. Manuelle Teilnehmer-Kontaktierung</h2>
                    <p class="text-gray-700 dark:text-gray-300">So erreichen Sie Ihre Teilnehmer direkt:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Gehen Sie zu „Events" → Event auswählen → „Teilnehmer kontaktieren"</li>
                        <li>Schreiben Sie eine Nachricht an alle oder gefilterte Teilnehmer</li>
                        <li>Filter: Alle, nur Bezahlte, nur Eingecheckte, nur Ausstehende</li>
                        <li>Betreff und Nachricht individuell formulieren</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Nutzen Sie dies für wichtige Änderungen, Wegbeschreibungen, Material-Anforderungen etc.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">3. Event-Update-Benachrichtigungen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Bei wesentlichen Änderungen am Event werden Teilnehmer automatisch informiert:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Datum/Uhrzeit geändert</li>
                        <li>Ort geändert</li>
                        <li>Online-Zugangsdaten aktualisiert</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Kleinere Änderungen (z.B. Beschreibungstext) lösen keine Benachrichtigung aus.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">4. Datenschutz & DSGVO</h2>
                    <p class="text-gray-700 dark:text-gray-300">Beachten Sie beim Versand:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Nutzen Sie BCC für Massen-E-Mails (E-Mail-Adressen nicht sichtbar)</li>
                        <li>Speichern Sie E-Mail-Adressen nicht außerhalb der Plattform</li>
                        <li>Versenden Sie nur Event-relevante Informationen</li>
                        <li>Teilnehmer können Benachrichtigungen abbestellen</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">5. Wartelisten-Benachrichtigungen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Wenn Ihr Event ausverkauft ist:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Interessenten können sich auf die Warteliste setzen</li>
                        <li>Bei Stornierung erhalten sie automatisch eine Benachrichtigung</li>
                        <li>48h Frist für Buchung (danach nächster auf Warteliste)</li>
                        <li>Manuell: „Nächsten benachrichtigen" Button in Wartelisten-Übersicht</li>
                    </ul>
                </section>

                <div class="bg-teal-50 dark:bg-teal-900/30 border border-teal-200 dark:border-teal-800 rounded p-4">
                    <p class="text-teal-800 dark:text-teal-200"><strong>Tipp:</strong> Versenden Sie 1-2 Tage vor dem Event eine persönliche Nachricht mit Wegbeschreibung und Ablauf – das reduziert Rückfragen.</p>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/30 border border-orange-200 dark:border-orange-800 rounded p-4 mt-4">
                    <p class="text-orange-800 dark:text-orange-200"><strong>Best Practice:</strong> Danken Sie nach dem Event mit einer Follow-up-E-Mail und bitten Sie um Feedback/Bewertung.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

