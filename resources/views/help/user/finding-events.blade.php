<x-help.article title="Veranstaltungen finden" subtitle="Entdecken Sie passende Fortbildungen">
    <section>
        <h2>Suchmöglichkeiten</h2>

        <h3>Listenansicht</h3>
        <p>
            Die Standardansicht zeigt alle Veranstaltungen in einer übersichtlichen Liste. Hier können Sie nach
            verschiedenen Kriterien filtern und sortieren.
        </p>
        <p><x-ui.button variant="link" arrow :href="route('events.index')">Zur Veranstaltungsübersicht</x-ui.button></p>

        <h3>Kalenderansicht</h3>
        <p>
            Sehen Sie alle Veranstaltungen in einem Kalender. Ideal für die Planung Ihrer Fortbildungen und um freie
            Termine zu finden.
        </p>
        <p><x-ui.button variant="link" arrow :href="route('events.calendar')">Zum Veranstaltungskalender</x-ui.button></p>

        <h3>Personalisierte Empfehlungen</h3>
        <p>
            Basierend auf Ihren Interessen und bisherigen Buchungen empfehlen wir Ihnen passende Veranstaltungen.
            Diese finden Sie in Ihrem Dashboard.
        </p>
        <p><x-ui.button variant="link" arrow :href="route('dashboard')">Zum Dashboard</x-ui.button></p>
    </section>

    <section>
        <h2>Filter nutzen</h2>

        <h3>Nach Kategorie filtern</h3>
        <p>Unsere Veranstaltungen sind in Kategorien eingeteilt:</p>
        <ul>
            <li>Pädagogik & Didaktik</li>
            <li>Digitalisierung & Medien</li>
            <li>Schulentwicklung</li>
            <li>Inklusion & Diversität</li>
            <li>Führung & Management</li>
            <li>Fachspezifische Themen</li>
            <li>Evangelische Schulbildung</li>
            <li>Und viele mehr...</li>
        </ul>

        <h3>Nach Zeitraum filtern</h3>
        <p>
            Wählen Sie „Heute“, „Diese Woche“, „Diesen Monat“ oder „Nächste 3 Monate“. Über die erweiterten Filter
            können Sie außerdem ein eigenes Datum von/bis eingeben.
        </p>

        <h3>Nach Format filtern</h3>
        <p>Wählen Sie zwischen:</p>
        <ul>
            <li><strong>Präsenz:</strong> Veranstaltungen mit physischem Veranstaltungsort</li>
            <li><strong>Online:</strong> Webinare und digitale Fortbildungen</li>
            <li><strong>Hybrid:</strong> Kombinierte Formate</li>
        </ul>

        <h3>Nach Ort filtern</h3>
        <p>Geben Sie im erweiterten Filter eine Stadt ein, um nur Veranstaltungen an diesem Ort zu sehen.</p>
    </section>

    <section>
        <h2>Textsuche verwenden</h2>
        <p>Nutzen Sie die Suchleiste, um gezielt nach Veranstaltungen zu suchen. Sie können suchen nach:</p>
        <ul>
            <li><strong>Titel:</strong> z. B. „Digitale Medien im Unterricht“</li>
            <li><strong>Beschreibung:</strong> Stichwörter aus der Veranstaltungsbeschreibung</li>
            <li><strong>Ort:</strong> Stadt oder Region</li>
        </ul>

        <x-ui.alert tone="info" title="Tipp">Kombinieren Sie die Textsuche mit Filtern für noch präzisere
            Ergebnisse!</x-ui.alert>
    </section>

    <section>
        <h2>Favoriten nutzen</h2>

        <h3>Als Favorit markieren</h3>
        <p>
            Klicken Sie auf das Herz-Symbol auf einer Veranstaltungsseite, um diese als Favorit zu speichern.
        </p>

        <h3>Favoriten ansehen</h3>
        <p>
            Alle gespeicherten Veranstaltungen finden Sie unter <a href="{{ route('favorites.index') }}">Favoriten</a>.
        </p>

    </section>

    <section>
        <h2>Veranstaltungsdetails verstehen</h2>
        <p>Auf jeder Veranstaltungsseite finden Sie folgende Informationen:</p>

        <h3>Grundinformationen</h3>
        <ul>
            <li>Titel und Beschreibung</li>
            <li>Kategorie</li>
            <li>Datum und Uhrzeit</li>
            <li>Dauer</li>
        </ul>

        <h3>Ort & Format</h3>
        <ul>
            <li>Veranstaltungsort (Adresse)</li>
            <li>Online-Link (bei digitalen Veranstaltungen)</li>
            <li>Format (Präsenz/Online/Hybrid)</li>
        </ul>

        <h3>Tickets & Preise</h3>
        <ul>
            <li>Verfügbare Ticketarten</li>
            <li>Preise</li>
            <li>Verfügbarkeit</li>
        </ul>

    </section>

    <x-slot:related>
        <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'getting-started'])">Vorheriger Artikel: Erste
            Schritte</x-ui.button>
        <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'booking-events'])">Nächster Artikel:
            Veranstaltungen buchen</x-ui.button>
    </x-slot:related>
</x-help.article>
