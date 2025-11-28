<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('help.index') }}" class="hover:text-blue-600">Hilfe</a> ·
                <a href="{{ route('help.category','organizer') }}" class="hover:text-blue-600">Veranstalter</a> ·
                <span>Statistiken & Berichte</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Statistiken & Berichte</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">Verstehen Sie Ihre Kennzahlen und optimieren Sie Ihre Events.</p>

            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">1. Dashboard Statistiken</h2>
                    <p class="text-gray-700 dark:text-gray-300">Ihr Organizer-Dashboard zeigt auf einen Blick:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Gesamt Events:</strong> Anzahl aller Veranstaltungen</li>
                        <li><strong>Veröffentlichte Events:</strong> Aktuell online sichtbar</li>
                        <li><strong>Buchungen gesamt:</strong> Alle Buchungen (inkl. storniert)</li>
                        <li><strong>Umsatz:</strong> Bestätigte Zahlungen in EUR</li>
                        <li><strong>Ausstehender Umsatz:</strong> Pending-Buchungen</li>
                        <li><strong>Teilnehmer gesamt:</strong> Summe aller Tickets (exkl. storniert)</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">2. Event-spezifische Auswertungen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Für jedes Event sehen Sie detaillierte Statistiken:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Konversionsrate:</strong> Verhältnis Aufrufe zu Buchungen</li>
                        <li><strong>Ticket-Typ-Verteilung:</strong> Welche Ticketarten wurden gebucht?</li>
                        <li><strong>Tägliche Buchungstrends:</strong> Wann buchen Teilnehmer?</li>
                        <li><strong>Rabattcode-Nutzung:</strong> Welche Codes wurden eingelöst?</li>
                        <li><strong>Check-In-Rate:</strong> Wie viele Teilnehmer sind tatsächlich erschienen?</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Gehen Sie zu „Statistiken" → Event auswählen, um Details zu sehen.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">3. Umsatz- & Trendanalysen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Monatliche und kategoriebasierte Auswertungen:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Monatliche Umsatz-Trends:</strong> Grafik der letzten 12 Monate</li>
                        <li><strong>Top Events nach Umsatz:</strong> Ihre erfolgreichsten Veranstaltungen</li>
                        <li><strong>Kategorie-Verteilung:</strong> In welchen Bereichen sind Sie aktiv?</li>
                        <li><strong>Zeitraum-Filter:</strong> Wählen Sie 7, 30, 90 Tage oder Gesamt</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">4. CSV & Excel Exporte</h2>
                    <p class="text-gray-700 dark:text-gray-300">Exportieren Sie Daten für externe Auswertungen:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Buchungsliste:</strong> Alle Buchungen inkl. Teilnehmerdaten</li>
                        <li><strong>Umsatzreport:</strong> Gruppiert nach Event, Kategorie oder Monat</li>
                        <li><strong>Check-In-Liste:</strong> Nach Event mit Zeitstempel</li>
                        <li><strong>Rabattcode-Nutzung:</strong> Welche Codes wie oft eingelöst wurden</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Nutzen Sie Filter, um z.B. nur ein bestimmtes Event oder einen Zeitraum zu exportieren.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">5. Plattform-Gebühren & Rechnungen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Übersicht über Ihre Kosten:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Platform-Fee:</strong> Prozentuale Gebühr auf Buchungen</li>
                        <li><strong>Featured-Event-Gebühren:</strong> Kosten für Hervorhebung</li>
                        <li><strong>Rechnungs-Historie:</strong> Alle Platform-Fee-Rechnungen mit Download</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Rechnungen werden automatisch nach Event-Ende erstellt und per E-Mail versandt.</p>
                </section>

                <div class="bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 rounded p-4">
                    <p class="text-indigo-800 dark:text-indigo-200"><strong>Tipp:</strong> Nutzen Sie die Check-In-Rate, um die Qualität Ihrer Events zu verbessern. Eine niedrige Rate kann auf Probleme hinweisen.</p>
                </div>

                <div class="bg-pink-50 dark:bg-pink-900/30 border border-pink-200 dark:border-pink-800 rounded p-4 mt-4">
                    <p class="text-pink-800 dark:text-pink-200"><strong>Best Practice:</strong> Exportieren Sie regelmäßig Umsatzberichte für Ihre Buchhaltung und Planung.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

