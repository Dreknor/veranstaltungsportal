<x-help.article title="Häufige Probleme & FAQ" subtitle="Lösungen für typische Fragen und Fehler." :back-href="route('help.category', 'organizer')"
    back-label="Zur Veranstalter-Hilfe">
    <section>
        <h2>Warum kann ich meine Veranstaltung nicht veröffentlichen?</h2>
        <p>Die Veranstaltung kann nur veröffentlicht werden, wenn:</p>
        <ul>
            <li>Rechnungsdaten der Organisation vollständig sind</li>
            <li>Bankverbindung hinterlegt ist</li>
            <li>Mindestens ein Ticket-Typ erstellt wurde</li>
            <li>Alle Pflichtfelder ausgefüllt sind (Titel, Datum, Ort bzw. Online-Link)</li>
        </ul>
        <p><strong>Lösung:</strong> Prüfen Sie „Organisation" › „Rechnungsdaten" und „Bankverbindung". Ergänzen Sie
            fehlende Angaben.</p>
    </section>

    <section>
        <h2>Teilnehmer haben keine E-Mail erhalten</h2>
        <p>Mögliche Ursachen:</p>
        <ul>
            <li>E-Mail im Spam-Ordner gelandet</li>
            <li>Falsche E-Mail-Adresse bei Buchung angegeben</li>
            <li>E-Mail-Server temporär nicht erreichbar</li>
        </ul>
        <p><strong>Lösung:</strong> Prüfen Sie in der Buchungsdetails die E-Mail-Adresse. Nutzen Sie „Erneut versenden"
            bei der Buchung, um die Bestätigung nochmals zu schicken.</p>
    </section>

    <section>
        <h2>Zahlungsstatus aktualisiert sich nicht</h2>
        <p>Bei manuellen Zahlungen (Überweisung, Barzahlung):</p>
        <ul>
            <li>Automatische Aktualisierung nur bei Online-Zahlung (Stripe, PayPal)</li>
            <li>Bei Überweisung: Manuell auf „Paid" setzen nach Zahlungseingang</li>
        </ul>
        <p><strong>Lösung:</strong> Gehen Sie zu „Buchungen" › Buchung öffnen › „Zahlungsstatus" › „Paid" auswählen.
            System versendet dann automatisch Tickets.</p>
    </section>

    <section>
        <h2>QR-Code funktioniert beim Check-In nicht</h2>
        <p>Troubleshooting:</p>
        <ul>
            <li>QR-Code nur auf bezahlten Tickets gültig</li>
            <li>Kamera-Berechtigung im Browser erteilt?</li>
            <li>Ausreichend Licht für Scanner?</li>
            <li>PDF korrekt geöffnet (nicht Screenshot)?</li>
        </ul>
        <p><strong>Lösung:</strong> Nutzen Sie alternativ die manuelle Suche nach Buchungsnummer oder Name.</p>
    </section>

    <section>
        <h2>Ich sehe keine Teammitglieder</h2>
        <p>Teammitglieder müssen die Einladung annehmen:</p>
        <ul>
            <li>Einladungs-E-Mail wurde versendet › Postfach prüfen</li>
            <li>Link in E-Mail anklicken und Einladung bestätigen</li>
            <li>Danach erscheinen sie unter „Team"</li>
        </ul>
        <p><strong>Lösung:</strong> Erneut einladen oder Person bitten, Spam-Ordner zu prüfen.</p>
    </section>

    <section>
        <h2>Wo finde ich meine Platform-Fee-Rechnungen?</h2>
        <p>Rechnungen werden automatisch erstellt:</p>
        <ul>
            <li>Nach Veranstaltungsende (innerhalb von 1-2 Tagen)</li>
            <li>Zugriff unter „Rechnungen" im Organizer-Bereich</li>
            <li>PDF-Download und E-Mail-Versand an Ihre hinterlegte Adresse</li>
        </ul>
    </section>

    <section>
        <h2>Keine Berechtigung für Aktion</h2>
        <p>Prüfen Sie Ihre Rolle in der Organisation:</p>
        <ul>
            <li><strong>Viewer:</strong> Kann nur ansehen, nichts bearbeiten</li>
            <li><strong>Editor:</strong> Kann Veranstaltungen und Buchungen verwalten</li>
            <li><strong>Admin/Owner:</strong> Volle Rechte</li>
        </ul>
        <p><strong>Lösung:</strong> Kontaktieren Sie den Owner Ihrer Organisation für Rollenzuweisung.</p>
    </section>

    <section>
        <h2>Statistiken zeigen keine Daten</h2>
        <p>Mögliche Gründe:</p>
        <ul>
            <li>Noch keine Buchungen für diese Veranstaltung vorhanden</li>
            <li>Zeitraum-Filter zu eng gewählt</li>
            <li>Browser-Cache veraltet</li>
        </ul>
        <p><strong>Lösung:</strong> Wählen Sie „Gesamtzeitraum" im Filter. Cache löschen (Strg+F5).</p>
    </section>

    <x-ui.alert class="mt-12" tone="warning" title="Wichtig">Bei technischen Problemen, die Sie nicht lösen können,
        kontaktieren Sie den Support mit Screenshots und Fehlermeldung.</x-ui.alert>

    <x-ui.alert tone="info" title="Weitere Hilfe benötigt?">Durchsuchen Sie unsere <a
            href="{{ route('help.index') }}">Hilfe-Artikel</a> oder kontaktieren Sie den Support.</x-ui.alert>
</x-help.article>
