<x-help.article title="Favoriten & Merkliste" subtitle="Veranstaltungen als Favoriten speichern">
    <p>
        Speichern Sie interessante Veranstaltungen als Favoriten, um sie später schnell wiederzufinden und
        automatisch über Änderungen informiert zu werden.
    </p>

    <nav aria-label="Inhalt">
        <ul>
            <li><a href="#add">1. Favoriten hinzufügen</a></li>
            <li><a href="#manage">2. Favoriten verwalten</a></li>
            <li><a href="#organize">3. Mit Listen organisieren</a></li>
            <li><a href="#notifications">4. Benachrichtigungen</a></li>
        </ul>
    </nav>

    <section id="add">
        <h2>1. Favoriten hinzufügen</h2>

        <h3>Veranstaltung als Favorit markieren</h3>
        <ol>
            <li>Öffnen Sie die Detailseite einer Veranstaltung</li>
            <li>Klicken Sie auf das Herz-Symbol oben rechts</li>
            <li>Das Herz färbt sich rot - die Veranstaltung ist jetzt gespeichert</li>
            <li>Sie finden sie unter <strong>Favoriten</strong></li>
        </ol>
        <ul>
            <li><strong>Favorit hinzufügen:</strong> Klick auf das leere Herz speichert die Veranstaltung</li>
            <li><strong>Favorit entfernen:</strong> Klick auf das rote Herz entfernt die Veranstaltung</li>
        </ul>

        <x-ui.alert tone="success" title="Vorteil">Sie werden automatisch benachrichtigt, wenn sich etwas an einer
            favorisierten Veranstaltung ändert!</x-ui.alert>
    </section>

    <section id="manage">
        <h2>2. Favoriten verwalten</h2>

        <h3>Ihre Favoritenliste aufrufen</h3>
        <ol>
            <li>Klicken Sie in der Sidebar auf <strong>Favoriten</strong></li>
            <li>Oder gehen Sie zu Ihrem Profil › <strong>Favoriten</strong></li>
        </ol>

        <h3>Ansichtsoptionen</h3>
        <ul>
            <li><strong>Listenansicht:</strong> Kompakte Übersicht aller Favoriten mit wichtigsten Informationen</li>
            <li><strong>Kalenderansicht:</strong> Favoriten chronologisch nach Datum sortiert</li>
            <li><strong>Nach Kategorien:</strong> Gruppiert nach Themenbereichen</li>
        </ul>

        <h3>Filteroptionen</h3>
        <ul>
            <li>Nach Datum (kommende, vergangene)</li>
            <li>Nach Kategorie</li>
            <li>Nach Ort (online, vor Ort)</li>
            <li>Nach Status (offen für Buchungen, ausgebucht)</li>
        </ul>
    </section>

    <section id="organize">
        <h2>3. Mit Listen organisieren</h2>
        <p>Organisieren Sie Ihre Favoriten in thematischen Listen für bessere Übersicht.</p>

        <h3>Neue Liste erstellen</h3>
        <ol>
            <li>Gehen Sie zu <strong>Favoriten</strong></li>
            <li>Klicken Sie auf <strong>„Neue Liste“</strong></li>
            <li>Geben Sie einen Namen ein (z.B. „Digitalisierung“, „Für später“)</li>
            <li>Optional: Fügen Sie eine Beschreibung hinzu</li>
            <li>Klicken Sie auf <strong>„Liste erstellen“</strong></li>
        </ol>

        <h3>Veranstaltungen zu Listen hinzufügen</h3>
        <ol>
            <li>Klicken Sie bei einem Favoriten auf das Drei-Punkte-Menü</li>
            <li>Wählen Sie <strong>„Zu Liste hinzufügen“</strong></li>
            <li>Wählen Sie eine oder mehrere Listen aus</li>
            <li>Die Veranstaltung erscheint nun in den gewählten Listen</li>
        </ol>

        <x-ui.alert tone="info" title="Tipp">Eine Veranstaltung kann in mehreren Listen gleichzeitig sein!
        </x-ui.alert>
    </section>

    <section id="notifications">
        <h2>4. Benachrichtigungen zu Favoriten</h2>
        <p>Bleiben Sie automatisch auf dem Laufenden über Ihre favorisierten Veranstaltungen.</p>

        <h3>Automatische Benachrichtigungen</h3>
        <ul>
            <li><strong>Änderungen an Veranstaltungen:</strong> Zeit, Ort oder Inhalt wurde geändert</li>
            <li><strong>Ticket-Verfügbarkeit:</strong> Wenn eine ausgebuchte Veranstaltung wieder Plätze frei hat
            </li>
            <li><strong>Erinnerung vor Buchungsende:</strong> 24h bevor die Buchungsfrist endet</li>
            <li><strong>Absage:</strong> Wenn eine favorisierte Veranstaltung abgesagt wird</li>
        </ul>

        <h3>Benachrichtigungen anpassen</h3>
        <p>
            Gehen Sie zu <strong>Einstellungen</strong> › <strong>Benachrichtigungen</strong> um festzulegen, welche
            Updates Sie zu Favoriten erhalten möchten.
        </p>
    </section>
</x-help.article>
