<x-help.article title="Häufige Probleme" subtitle="Lösungen für die häufigsten Fragen">
    <div>
        <details>
            <summary>Ich habe mein Passwort vergessen</summary>
            <ol>
                <li>Klicken Sie auf der Anmeldeseite auf „Passwort vergessen?“</li>
                <li>Geben Sie Ihre E-Mail-Adresse ein</li>
                <li>Sie erhalten einen Link zum Zurücksetzen per E-Mail</li>
                <li>Klicken Sie auf den Link und wählen Sie ein neues Passwort</li>
            </ol>
            <x-ui.alert tone="warning" title="Hinweis">Der Link ist 60 Minuten gültig. Prüfen Sie auch Ihren
                Spam-Ordner.</x-ui.alert>
        </details>

        <details>
            <summary>Ich habe keine Bestätigungs-E-Mail erhalten</summary>
            <p>Wenn Sie keine Bestätigungs-E-Mail erhalten haben:</p>
            <ul>
                <li>Prüfen Sie Ihren Spam/Junk-Ordner</li>
                <li>Warten Sie einige Minuten (Versand kann verzögert sein)</li>
                <li>Prüfen Sie, ob die E-Mail-Adresse korrekt ist</li>
                <li>Fordern Sie eine neue Bestätigungsmail an (Button in Ihrem Profil)</li>
            </ul>
        </details>

        <details>
            <summary>Ich habe kein Ticket erhalten</summary>
            <p>Tickets werden nur bei bezahlten Buchungen versendet:</p>
            <ol>
                <li>Prüfen Sie den Zahlungsstatus in „Meine Buchungen“</li>
                <li>Bei „Bezahlt“ sollten Sie das Ticket in der Buchungsbestätigung finden</li>
                <li>Laden Sie das Ticket direkt aus Ihrer Buchungsübersicht herunter</li>
                <li>Prüfen Sie Ihren Spam-Ordner für die E-Mail</li>
            </ol>
            <x-ui.alert tone="info" title="Hinweis">Bei Online-Veranstaltungen erhalten Sie nach Bezahlung die
                Zugangsdaten statt eines PDF-Tickets.</x-ui.alert>
        </details>

        <details>
            <summary>Wie kann ich eine Buchung stornieren?</summary>
            <ol>
                <li>Gehen Sie zu „Meine Buchungen“</li>
                <li>Klicken Sie auf die betreffende Buchung</li>
                <li>Klicken Sie auf „Buchung stornieren“</li>
                <li>Bestätigen Sie die Stornierung</li>
            </ol>
            <x-ui.alert tone="warning" title="Wichtig">Beachten Sie die Stornierungsbedingungen des Veranstalters.
                Diese finden Sie in den Veranstaltungsdetails.</x-ui.alert>
        </details>

        <details>
            <summary>Der Rabattcode funktioniert nicht</summary>
            <p>Mögliche Gründe:</p>
            <ul>
                <li>Code ist abgelaufen (prüfen Sie das Gültigkeitsdatum)</li>
                <li>Code wurde bereits zu oft verwendet (maximale Nutzungsanzahl erreicht)</li>
                <li>Code gilt nur für bestimmte Ticketarten</li>
                <li>Mindestanzahl an Tickets nicht erreicht</li>
                <li>Tippfehler im Code (Codes sind case-sensitive)</li>
            </ul>
        </details>

        <details>
            <summary>Die Seite lädt nicht richtig</summary>
            <p>Versuchen Sie folgende Schritte:</p>
            <ol>
                <li>Aktualisieren Sie die Seite (F5 oder Strg+R)</li>
                <li>Leeren Sie den Browser-Cache (Strg+Shift+Entf)</li>
                <li>Versuchen Sie es mit einem anderen Browser</li>
                <li>Deaktivieren Sie Browser-Erweiterungen vorübergehend</li>
                <li>Prüfen Sie Ihre Internetverbindung</li>
            </ol>
        </details>

        <details>
            <summary>Profilbild lässt sich nicht hochladen</summary>
            <p>Prüfen Sie folgende Punkte:</p>
            <ul>
                <li>Datei ist nicht größer als 2MB</li>
                <li>Format ist JPG, PNG oder GIF</li>
                <li>Dateiname enthält keine Sonderzeichen</li>
                <li>Browser erlaubt Datei-Uploads</li>
            </ul>
        </details>

        <details>
            <summary>Ich erhalte keine Benachrichtigungen</summary>
            <ol>
                <li>Gehen Sie zu Einstellungen › Benachrichtigungen</li>
                <li>Prüfen Sie, ob die gewünschten Benachrichtigungen aktiviert sind</li>
                <li>Prüfen Sie Ihren Spam-Ordner</li>
                <li>Stellen Sie sicher, dass Ihre E-Mail-Adresse verifiziert ist</li>
            </ol>
        </details>

        <details>
            <summary>Zahlung wurde abgebucht aber Buchung zeigt „Ausstehend“</summary>
            <p>Das kann verschiedene Gründe haben:</p>
            <ul>
                <li>Zahlung wird noch verarbeitet (kann bis zu 24h dauern)</li>
                <li>Aktualisieren Sie die Seite</li>
                <li>Prüfen Sie Ihr E-Mail-Postfach auf Zahlungsbestätigung</li>
            </ul>
            <x-ui.alert tone="danger" title="Wenn das Problem länger als 24h besteht">Kontaktieren Sie bitte unseren
                Support mit Ihrer Buchungsnummer und einem Screenshot Ihrer Banküberweisung.</x-ui.alert>
        </details>

        <details>
            <summary>Wie kann ich meine Daten exportieren/löschen?</summary>
            <p>Gemäß DSGVO haben Sie folgende Rechte:</p>
            <p><strong>Daten exportieren:</strong></p>
            <ol>
                <li>Einstellungen › Datenschutz</li>
                <li>Klicken Sie auf „Meine Daten exportieren“</li>
                <li>Sie erhalten eine JSON-Datei mit allen Ihren Daten</li>
            </ol>
            <p><strong>Konto löschen:</strong></p>
            <ol>
                <li>Einstellungen › Datenschutz</li>
                <li>Scrollen Sie nach unten zu „Konto löschen“</li>
                <li>Bestätigen Sie die Löschung</li>
            </ol>
            <x-ui.alert tone="warning" title="Hinweis">Bei aktiven Buchungen kann das Konto nicht sofort gelöscht
                werden.</x-ui.alert>
        </details>

        <details>
            <summary>Wie kontaktiere ich einen Veranstalter?</summary>
            <p>Auf jeder Veranstaltungsseite finden Sie die Kontaktinformationen des Veranstalters:</p>
            <ul>
                <li>E-Mail-Adresse</li>
                <li>Telefonnummer (falls angegeben)</li>
                <li>Website (falls angegeben)</li>
            </ul>
            <p>Scrollen Sie auf der Veranstaltungsseite zum Abschnitt „Veranstalter“.</p>
        </details>
    </div>

    <section>
        <h2>Immer noch Probleme?</h2>
        <p>Wenn Sie hier keine Lösung gefunden haben, kontaktieren Sie uns gerne direkt.</p>
        <div class="mt-6 flex flex-wrap gap-4">
            <x-ui.button href="mailto:support@bildungsportal.de">
                <x-slot:icon><x-icon.mail /></x-slot:icon>
                E-Mail an Support
            </x-ui.button>
            <x-ui.button variant="secondary" :href="route('help.index')">Weitere Hilfe-Artikel</x-ui.button>
        </div>
    </section>

    <x-slot:related>
        <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'privacy'])">Vorheriger Artikel:
            Datenschutz</x-ui.button>
    </x-slot:related>
</x-help.article>
