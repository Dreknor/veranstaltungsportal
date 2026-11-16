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

        <h3>Nach Datum filtern</h3>
        <p>
            Wählen Sie einen Zeitraum aus, um nur Veranstaltungen in diesem Zeitfenster anzuzeigen. Sie können nach
            einzelnen Tagen, Wochen oder Monaten filtern.
        </p>

        <h3>Nach Ort filtern</h3>
        <p>Wählen Sie zwischen:</p>
        <ul>
            <li><strong>Präsenz-Veranstaltungen:</strong> Veranstaltungen mit physischem Veranstaltungsort</li>
            <li><strong>Online-Veranstaltungen:</strong> Webinare und digitale Fortbildungen</li>
            <li><strong>Hybrid-Veranstaltungen:</strong> Kombinierte Formate</li>
        </ul>

        <h3>Nach Preis filtern</h3>
        <p>
            Nutzen Sie den Preis-Slider, um nur Veranstaltungen in Ihrer gewünschten Preisklasse anzuzeigen. Es gibt
            auch viele kostenfreie Angebote!
        </p>
    </section>

    <section>
        <h2>Textsuche verwenden</h2>
        <p>Nutzen Sie die Suchleiste, um gezielt nach Veranstaltungen zu suchen. Sie können suchen nach:</p>
        <ul>
            <li><strong>Titel:</strong> z.B. „Digitale Medien im Unterricht“</li>
            <li><strong>Beschreibung:</strong> Stichwörter aus der Veranstaltungsbeschreibung</li>
            <li><strong>Veranstalter:</strong> Name der Organisation</li>
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

        <x-ui.alert tone="success" title="Vorteil">Sie erhalten automatisch Benachrichtigungen, wenn sich etwas an
            Ihren Favoriten ändert!</x-ui.alert>
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
            <li>Verfügbare Ticket-Typen</li>
            <li>Preise</li>
            <li>Verfügbarkeit</li>
            <li>Rabattmöglichkeiten</li>
        </ul>

        <h3>Veranstalter</h3>
        <ul>
            <li>Name und Beschreibung</li>
            <li>Kontaktinformationen</li>
            <li>Website</li>
            <li>Weitere Veranstaltungen</li>
        </ul>
    </section>

    <x-slot:related>
        <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'getting-started'])">Vorheriger Artikel: Erste
            Schritte</x-ui.button>
        <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'booking-events'])">Nächster Artikel:
            Veranstaltungen buchen</x-ui.button>
    </x-slot:related>
</x-help.article>
