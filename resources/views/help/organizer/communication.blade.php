<x-help.article title="Kommunikation mit Teilnehmenden" subtitle="Halten Sie Ihre Teilnehmende informiert und engagiert."
    :back-href="route('help.category', 'organizer')" back-label="Zur Veranstalter-Hilfe">
    <section>
        <h2>1. Automatische E-Mails – wann geht was raus?</h2>
        <p>Jede Buchung erhält zu jedem Schritt genau eine passende E-Mail. Sie müssen nichts manuell versenden.</p>
        @include('organizer.partials.communication-plan')
    </section>

    <section>
        <h2>2. Teilnehmende kontaktieren</h2>
        <p>So erreichen Sie Ihre Teilnehmende direkt:</p>
        <ul>
            <li>Gehen Sie zu „Meine Veranstaltungen“ › Veranstaltung auswählen › „Teilnehmende kontaktieren“</li>
            <li>Schreiben Sie eine Nachricht an alle oder gefilterte Teilnehmende</li>
            <li>Filter: Alle, nur Bezahlte, nur Eingecheckte, nur Ausstehende</li>
            <li>Betreff und Nachricht individuell formulieren</li>
        </ul>
        <p>Nutzen Sie dies für wichtige Änderungen, Wegbeschreibungen, Material-Anforderungen etc.</p>
    </section>

    <section>
        <h2>3. Änderungen an der Veranstaltung</h2>
        <p>Beim Speichern einer veröffentlichten Veranstaltung werden alle bestätigten Buchungen automatisch informiert,
            wenn sich</p>
        <ul>
            <li>Titel, Beginn oder Ende,</li>
            <li>Veranstaltungsort oder Adresse ändern,</li>
            <li>oder der Online-Link bzw. Zugangscode geändert wird (nur an Buchungen mit freigeschaltetem Zugang).</li>
        </ul>
        <p>Kleinere Änderungen (z. B. Beschreibungstext) lösen keine Benachrichtigung aus.</p>
    </section>

    <section>
        <h2>4. Datenschutz & DSGVO</h2>
        <p>Beachten Sie beim Versand:</p>
        <ul>
            <li>Nutzen Sie BCC für Massen-E-Mails (E-Mail-Adressen nicht sichtbar)</li>
            <li>Speichern Sie E-Mail-Adressen nicht außerhalb der Plattform</li>
            <li>Versenden Sie nur veranstaltungsrelevante Informationen</li>
            <li>Teilnehmende können Benachrichtigungen abbestellen</li>
        </ul>
    </section>

    <section>
        <h2>5. Wartelisten-Benachrichtigungen</h2>
        <p>Wenn Ihre Veranstaltung ausverkauft ist:</p>
        <ul>
            <li>Interessenten können sich auf die Warteliste setzen</li>
            <li>Bei Stornierung erhalten sie automatisch eine Benachrichtigung</li>
            <li>48h Frist für Buchung (danach nächster auf Warteliste)</li>
            <li>Manuell: „Nächsten benachrichtigen“ Button in Wartelisten-Übersicht</li>
        </ul>
    </section>

    <section>
        <h2>6. Bewertungen</h2>
        <p>Teilnehmende können Ihre Veranstaltung nach dem Ende bewerten. Jede Bewertung prüfen Sie unter
            <strong>Bewertungen</strong>, bevor sie erscheint.
        </p>
        <ul>
            <li><strong>Freigeben:</strong> Die Bewertung erscheint auf der Veranstaltungsseite, bei späteren Terminen
                desselben Angebots und in Ihrem öffentlichen Veranstalter-Profil.</li>
            <li><strong>Ablehnen nur bei Regelverstoß:</strong> etwa bei Beleidigungen, persönlichen Daten Dritter,
                Werbung oder ohne Bezug zur Veranstaltung. Kritik allein ist kein Grund. Die Plattform sieht, wie
                viele Bewertungen ein Veranstalter ablehnt.</li>
            <li><strong>Öffentlich antworten:</strong> In der Ansicht „Prüfen“ können Sie eine Antwort schreiben. Sie
                steht unter der Bewertung – eine sachliche Antwort auf Kritik wirkt oft überzeugender als viele gute
                Noten.</li>
        </ul>
        <h3>Wiederkehrende Angebote verknüpfen</h3>
        <p>Bieten Sie eine Veranstaltung erneut an, nutzen Sie <strong>Duplizieren</strong>. Die Kopie wird
            automatisch mit dem Original verknüpft, und dessen Bewertungen erscheinen beim neuen Termin unter
            „Bewertungen früherer Termine“. Bei einer bestehenden Veranstaltung wählen Sie im Bearbeitungsformular
            unter <strong>Früherer Termin dieses Angebots</strong> den vorherigen Termin aus. Weicht der Titel stark ab,
            bestätigen Sie, dass es dasselbe Angebot ist.</p>
        <p>Ihre Gesamtbewertung berücksichtigt die freigegebenen Bewertungen der letzten 24 Monate. Ein Durchschnitt
            erscheint ab drei Bewertungen.</p>
    </section>

    <x-ui.alert tone="info" title="Tipp">Versenden Sie 1-2 Tage vor der Veranstaltung eine persönliche
        Nachricht mit Wegbeschreibung und Ablauf – das reduziert Rückfragen.</x-ui.alert>

    <x-ui.alert tone="info" title="Empfehlung">Danken Sie nach der Veranstaltung mit einer Follow-up-E-Mail und
        bitten Sie um Feedback/Bewertung.</x-ui.alert>
</x-help.article>
