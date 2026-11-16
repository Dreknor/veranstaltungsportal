<x-help.article title="Statistiken & Berichte" subtitle="Verstehen Sie Ihre Kennzahlen und optimieren Sie Ihre Events."
    :back-href="route('help.category', 'organizer')" back-label="Zur Veranstalter-Hilfe">
    <section>
        <h2>1. Dashboard Statistiken</h2>
        <p>Ihr Organizer-Dashboard zeigt auf einen Blick:</p>
        <ul>
            <li><strong>Gesamt Events:</strong> Anzahl aller Veranstaltungen</li>
            <li><strong>Veröffentlichte Events:</strong> Aktuell online sichtbar</li>
            <li><strong>Buchungen gesamt:</strong> Alle Buchungen (inkl. storniert)</li>
            <li><strong>Umsatz:</strong> Bestätigte Zahlungen in EUR</li>
            <li><strong>Ausstehender Umsatz:</strong> Pending-Buchungen</li>
            <li><strong>Teilnehmer gesamt:</strong> Summe aller Tickets (exkl. storniert)</li>
        </ul>
    </section>

    <section>
        <h2>2. Event-spezifische Auswertungen</h2>
        <p>Für jedes Event sehen Sie detaillierte Statistiken:</p>
        <ul>
            <li><strong>Konversionsrate:</strong> Verhältnis Aufrufe zu Buchungen</li>
            <li><strong>Ticket-Typ-Verteilung:</strong> Welche Ticketarten wurden gebucht?</li>
            <li><strong>Tägliche Buchungstrends:</strong> Wann buchen Teilnehmer?</li>
            <li><strong>Rabattcode-Nutzung:</strong> Welche Codes wurden eingelöst?</li>
            <li><strong>Check-In-Rate:</strong> Wie viele Teilnehmer sind tatsächlich erschienen?</li>
        </ul>
        <p>Gehen Sie zu „Statistiken" → Event auswählen, um Details zu sehen.</p>
    </section>

    <section>
        <h2>3. Umsatz- & Trendanalysen</h2>
        <p>Monatliche und kategoriebasierte Auswertungen:</p>
        <ul>
            <li><strong>Monatliche Umsatz-Trends:</strong> Grafik der letzten 12 Monate</li>
            <li><strong>Top Events nach Umsatz:</strong> Ihre erfolgreichsten Veranstaltungen</li>
            <li><strong>Kategorie-Verteilung:</strong> In welchen Bereichen sind Sie aktiv?</li>
            <li><strong>Zeitraum-Filter:</strong> Wählen Sie 7, 30, 90 Tage oder Gesamt</li>
        </ul>
    </section>

    <section>
        <h2>4. CSV & Excel Exporte</h2>
        <p>Exportieren Sie Daten für externe Auswertungen:</p>
        <ul>
            <li><strong>Buchungsliste:</strong> Alle Buchungen inkl. Teilnehmerdaten</li>
            <li><strong>Umsatzreport:</strong> Gruppiert nach Event, Kategorie oder Monat</li>
            <li><strong>Check-In-Liste:</strong> Nach Event mit Zeitstempel</li>
            <li><strong>Rabattcode-Nutzung:</strong> Welche Codes wie oft eingelöst wurden</li>
        </ul>
        <p>Nutzen Sie Filter, um z.B. nur ein bestimmtes Event oder einen Zeitraum zu exportieren.</p>
    </section>

    <section>
        <h2>5. Plattform-Gebühren & Rechnungen</h2>
        <p>Übersicht über Ihre Kosten:</p>
        <ul>
            <li><strong>Platform-Fee:</strong> Prozentuale Gebühr auf Buchungen</li>
            <li><strong>Featured-Event-Gebühren:</strong> Kosten für Hervorhebung</li>
            <li><strong>Rechnungs-Historie:</strong> Alle Platform-Fee-Rechnungen mit Download</li>
        </ul>
        <p>Rechnungen werden automatisch nach Event-Ende erstellt und per E-Mail versandt.</p>
    </section>

    <x-ui.alert class="mt-12" tone="info" title="Tipp">Nutzen Sie die Check-In-Rate, um die Qualität Ihrer Events zu verbessern. Eine niedrige Rate kann auf Probleme hinweisen.</x-ui.alert>

    <x-ui.alert tone="info" title="Best Practice">Exportieren Sie regelmäßig Umsatzberichte für Ihre Buchhaltung und Planung.</x-ui.alert>
</x-help.article>
