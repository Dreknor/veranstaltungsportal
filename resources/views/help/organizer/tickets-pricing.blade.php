<x-help.article title="Tickets & Preisgestaltung" subtitle="Best Practices für Ticketarten, Preise und Kapazitäten."
    :back-href="route('help.category', 'organizer')" back-label="Zur Veranstalter-Hilfe">
    <section>
        <h2>1. Ticketarten erstellen</h2>
        <p>Definieren Sie verschiedene Ticketarten für Ihre Veranstaltung:</p>
        <ul>
            <li><strong>Standard:</strong> Regulärer Preis für alle</li>
            <li><strong>Frühbucher:</strong> Reduzierter Preis bis zu einem Stichtag</li>
            <li><strong>Studierende/Azubis:</strong> Ermäßigter Preis (optional mit Nachweis)</li>
            <li><strong>Gruppenticket:</strong> Für Teams oder Schulgruppen</li>
            <li><strong>VIP/Premium:</strong> Mit besonderen Leistungen</li>
        </ul>
        <p>Jede Ticketart kann eigene Verfügbarkeit, Preis und Kontingent haben.</p>
    </section>

    <section>
        <h2>2. Preisstaffeln & Zeitfenster</h2>
        <p>Nutzen Sie Preisstaffeln, um Buchungen zu steuern:</p>
        <ul>
            <li>Frühbucherpreis bis 4 Wochen vor der Veranstaltung (z.B. 20% Rabatt)</li>
            <li>Normalpreis bis 1 Woche vor der Veranstaltung</li>
            <li>Last-Minute-Preis (optional höher oder niedriger)</li>
        </ul>
        <p>Geben Sie Start- und Enddatum für jede Ticketart an. Das System zeigt automatisch nur verfügbare Tickets.</p>
    </section>

    <section>
        <h2>3. Rabattcodes einrichten</h2>
        <p>Erstellen Sie individuelle Rabattcodes:</p>
        <ul>
            <li><strong>Fixer Betrag:</strong> z.B. 10 € Rabatt</li>
            <li><strong>Prozentual:</strong> z.B. 15% auf Gesamtpreis</li>
            <li><strong>Nutzungslimit:</strong> max. X-mal einlösbar</li>
            <li><strong>Gültigkeitszeitraum:</strong> von-bis Datum</li>
        </ul>
        <p>Rabattcodes können Sie an Partner, Sponsoren oder für Marketing-Kampagnen vergeben.</p>
    </section>

    <section>
        <h2>4. Kapazitäten & Kontingente</h2>
        <p>Steuern Sie die Verfügbarkeit:</p>
        <ul>
            <li><strong>Max. Teilnehmerzahl:</strong> Gesamtkapazität der Veranstaltung</li>
            <li><strong>Kontingent pro Ticketart:</strong> z.B. max. 20 Frühbucher-Tickets</li>
            <li><strong>Warteliste:</strong> Aktivieren Sie die Warteliste, damit Interessenten bei Ausverkauf
                benachrichtigt werden</li>
        </ul>
    </section>

    <section>
        <h2>5. Kostenlose Veranstaltungen</h2>
        <p>Auch kostenlose Veranstaltungen profitieren von Tickets:</p>
        <ul>
            <li>Setzen Sie Preis auf 0,00 €</li>
            <li>Nutzen Sie Tickets für Kapazitätskontrolle</li>
            <li>Teilnehmer erhalten trotzdem Bestätigung und QR-Code für Check-In</li>
        </ul>
    </section>

    <x-ui.alert class="mt-12" tone="info" title="Tipp">Frühbucherrabatte erhöhen die Planungssicherheit. Setzen Sie
        attraktive Anreize für frühe Buchungen.</x-ui.alert>

    <x-ui.alert tone="info" title="Best Practice">Begrenzen Sie Frühbucher-Kontingente künstlich (z.B. nur 30% der
        Plätze), um Dringlichkeit zu erzeugen.</x-ui.alert>
</x-help.article>
