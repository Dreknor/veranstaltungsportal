<x-help.article title="Einstellungen & Präferenzen" subtitle="Verwalten Sie Ihre Organisation, Rechnung, Team und Profil."
    :back-href="route('help.category', 'organizer')" back-label="Zur Veranstalter-Hilfe">
    <section>
        <h2>1. Organisationsdaten & Logo</h2>
        <p>Unter „Organisation bearbeiten" pflegen Sie:</p>
        <ul>
            <li><strong>Name:</strong> Offizieller Organisationsname</li>
            <li><strong>Beschreibung:</strong> Über Ihre Organisation (wird auf Veranstaltungsseiten angezeigt)</li>
            <li><strong>Logo:</strong> Wird auf Veranstaltungen, Tickets, Rechnungen angezeigt (max. 2MB, JPG/PNG)</li>
            <li><strong>Website & Kontakt:</strong> E-Mail, Telefon, Website-URL</li>
        </ul>
        <p>Das Logo erscheint automatisch auf allen Ihren Veranstaltungen und in E-Mails an Teilnehmer.</p>
    </section>

    <section>
        <h2>2. Rechnungseinstellungen</h2>
        <p>Hinterlegen Sie vollständige Rechnungsdaten:</p>
        <ul>
            <li><strong>Firma/Institution:</strong> Offizieller Name für Rechnungen</li>
            <li><strong>Rechnungsadresse:</strong> Straße, PLZ, Stadt, Bundesland, Land</li>
            <li><strong>USt-IdNr./Steuernummer:</strong> Für steuerliche Zwecke</li>
            <li><strong>Rechnungsnummernformat:</strong> Platzhalter {YEAR}, {MONTH}, {COUNTER}</li>
        </ul>
        <p>Ohne vollständige Rechnungsdaten können Sie keine Veranstaltungen veröffentlichen.</p>
    </section>

    <section>
        <h2>3. Bankverbindung</h2>
        <p>Hinterlegen Sie Ihre Bankdaten für Auszahlungen:</p>
        <ul>
            <li><strong>Kontoinhaber:</strong> Name des Kontoinhabers</li>
            <li><strong>IBAN:</strong> Internationale Kontonummer</li>
            <li><strong>BIC/SWIFT:</strong> Bank-Identifikationscode</li>
            <li><strong>Bankname:</strong> Name Ihrer Bank</li>
        </ul>
        <p>Auszahlungen erfolgen automatisch nach Veranstaltungsende abzgl. Platform-Fee.</p>
    </section>

    <section>
        <h2>4. Teamverwaltung</h2>
        <p>Laden Sie Teammitglieder ein und vergeben Sie Rollen:</p>
        <ul>
            <li><strong>Owner:</strong> Volle Rechte inkl. Löschung der Organisation</li>
            <li><strong>Admin:</strong> Kann alles außer Organisation löschen</li>
            <li><strong>Editor:</strong> Kann Veranstaltungen erstellen/bearbeiten, Buchungen verwalten</li>
            <li><strong>Viewer:</strong> Kann nur ansehen, keine Änderungen</li>
        </ul>
        <p>Teammitglieder erhalten eine Einladungs-E-Mail und müssen diese bestätigen.</p>
    </section>

    <section>
        <h2>5. Persönliches Profil (Organizer)</h2>
        <p>Ihr persönliches Profil ist getrennt von der Organisation:</p>
        <ul>
            <li><strong>Vor- und Nachname:</strong> Ihr persönlicher Name</li>
            <li><strong>Profilbild:</strong> Für Ihr Konto (nicht für Veranstaltungen)</li>
            <li><strong>E-Mail & Passwort:</strong> Login-Daten</li>
            <li><strong>Benachrichtigungseinstellungen:</strong> Was möchten Sie per E-Mail erhalten?</li>
        </ul>
    </section>

    <section>
        <h2>6. Monetarisierungs-Einstellungen</h2>
        <p>Einsehen, aber nicht ändern (Admin-Rechte erforderlich):</p>
        <ul>
            <li><strong>Platform-Fee:</strong> Standardmäßig z.B. 5% auf Buchungen</li>
            <li><strong>Featured-Event-Gebühren:</strong> Kosten für Hervorhebung</li>
            <li><strong>Individuelle Gebühren:</strong> Können vom Admin angepasst werden</li>
        </ul>
    </section>

    <x-ui.alert tone="warning" title="Wichtig">Halten Sie Ihre Rechnungs- und Bankdaten aktuell, um
        Verzögerungen bei Auszahlungen zu vermeiden.</x-ui.alert>

    <x-ui.alert tone="info" title="Tipp">Laden Sie mehrere Admins ein für Ausfallsicherheit – falls eine Person nicht
        verfügbar ist.</x-ui.alert>
</x-help.article>
