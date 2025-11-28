<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('help.index') }}" class="hover:text-blue-600">Hilfe</a> ·
                <a href="{{ route('help.category','organizer') }}" class="hover:text-blue-600">Veranstalter</a> ·
                <span>Tickets & Preisgestaltung</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Tickets & Preisgestaltung</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">Best Practices für Ticketarten, Preise und Kapazitäten.</p>

            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">1. Ticketarten erstellen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Definieren Sie verschiedene Ticketarten für Ihr Event:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Standard:</strong> Regulärer Preis für alle</li>
                        <li><strong>Frühbucher:</strong> Reduzierter Preis bis zu einem Stichtag</li>
                        <li><strong>Studierende/Azubis:</strong> Ermäßigter Preis (optional mit Nachweis)</li>
                        <li><strong>Gruppenticket:</strong> Für Teams oder Schulgruppen</li>
                        <li><strong>VIP/Premium:</strong> Mit besonderen Leistungen</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Jede Ticketart kann eigene Verfügbarkeit, Preis und Kontingent haben.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">2. Preisstaffeln & Zeitfenster</h2>
                    <p class="text-gray-700 dark:text-gray-300">Nutzen Sie Preisstaffeln, um Buchungen zu steuern:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Frühbucherpreis bis 4 Wochen vor Event (z.B. 20% Rabatt)</li>
                        <li>Normalpreis bis 1 Woche vor Event</li>
                        <li>Last-Minute-Preis (optional höher oder niedriger)</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Geben Sie Start- und Enddatum für jede Ticketart an. Das System zeigt automatisch nur verfügbare Tickets.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">3. Rabattcodes einrichten</h2>
                    <p class="text-gray-700 dark:text-gray-300">Erstellen Sie individuelle Rabattcodes:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Fixer Betrag:</strong> z.B. 10 € Rabatt</li>
                        <li><strong>Prozentual:</strong> z.B. 15% auf Gesamtpreis</li>
                        <li><strong>Nutzungslimit:</strong> max. X-mal einlösbar</li>
                        <li><strong>Gültigkeitszeitraum:</strong> von-bis Datum</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Rabattcodes können Sie an Partner, Sponsoren oder für Marketing-Kampagnen vergeben.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">4. Kapazitäten & Kontingente</h2>
                    <p class="text-gray-700 dark:text-gray-300">Steuern Sie die Verfügbarkeit:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Max. Teilnehmerzahl:</strong> Gesamtkapazität des Events</li>
                        <li><strong>Kontingent pro Ticketart:</strong> z.B. max. 20 Frühbucher-Tickets</li>
                        <li><strong>Warteliste:</strong> Aktivieren Sie die Warteliste, damit Interessenten bei Ausverkauf benachrichtigt werden</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">5. Kostenlose Events</h2>
                    <p class="text-gray-700 dark:text-gray-300">Auch kostenlose Events profitieren von Tickets:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Setzen Sie Preis auf 0,00 €</li>
                        <li>Nutzen Sie Tickets für Kapazitätskontrolle</li>
                        <li>Teilnehmer erhalten trotzdem Bestätigung und QR-Code für Check-In</li>
                    </ul>
                </section>

                <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded p-4">
                    <p class="text-blue-800 dark:text-blue-200"><strong>Tipp:</strong> Frühbucherrabatte erhöhen die Planungssicherheit. Setzen Sie attraktive Anreize für frühe Buchungen.</p>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800 rounded p-4 mt-4">
                    <p class="text-purple-800 dark:text-purple-200"><strong>Best Practice:</strong> Begrenzen Sie Frühbucher-Kontingente künstlich (z.B. nur 30% der Plätze), um Dringlichkeit zu erzeugen.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

