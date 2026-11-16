<x-help.article title="Buchungen & Teilnehmer verwalten"
    subtitle="So behalten Sie den Überblick über Buchungen, Zahlungen und Check-Ins." :back-href="route('help.category', 'organizer')"
    back-label="Zur Veranstalter-Hilfe">
    <section>
        <h2>1. Der Ablauf einer Buchung</h2>
        <p>Jede Buchung zeigt oben einen Fortschrittsbalken und einen Kasten <strong>„Nächster Schritt“</strong> – dort
            steht immer, ob und was Sie tun müssen:</p>
        <ol>
            <li><strong>Wartet auf Freigabe</strong> (nur kostenfreie Veranstaltungen mit manueller Freigabe): Anmeldung
                bestätigen oder ablehnen.</li>
            <li><strong>Zahlung ausstehend</strong>: Sobald das Geld eingegangen ist, klicken Sie auf „Zahlung
                eingegangen“. Bei externer Rechnungsstellung markieren Sie die Buchung als fakturiert. PayPal-Zahlungen
                werden automatisch verbucht.</li>
            <li><strong>Bestätigt</strong>: Die Person erhält automatisch Tickets bzw. Online-Zugangsdaten. Bei mehreren
                Plätzen trägt sie vorher die Teilnehmenden ein.</li>
            <li><strong>Storniert</strong>: Plätze werden automatisch wieder freigegeben, die Warteliste wird
                informiert.</li>
        </ol>
        <p>Auf der Übersichtsseite „Buchungen“ zeigen Ihnen Hinweise oben an, wie viele Anmeldungen auf Freigabe warten
            und wie viele Zahlungen offen sind.</p>
    </section>

    <section>
        <h2>2. Zahlungen, Erstattungen und erneuter Versand</h2>
        <ul>
            <li><strong>Zahlung eingegangen:</strong> bestätigt die Buchung und versendet Tickets bzw. Zugangsdaten –
                ein separates „Bestätigen“ ist nicht nötig.</li>
            <li><strong>Erstattet / Fehlgeschlagen:</strong> Die Person wird automatisch per E-Mail informiert.</li>
            <li><strong>Erneut senden:</strong> Ist eine E-Mail nicht angekommen, senden Sie im Kasten „Nächster
                Schritt“ die aktuelle Bestätigung erneut.</li>
            <li><strong>E-Mail-Verlauf:</strong> Auf jeder Buchung sehen Sie, welche E-Mails wann an wen versendet
                wurden.</li>
        </ul>
    </section>

    <section>
        <h2>3. Check-In am Veranstaltungstag</h2>
        <p>Nutzen Sie den QR-Code-Scanner:</p>
        <ul>
            <li>Gehen Sie zu „Check-In" für die Veranstaltung</li>
            <li>Scannen Sie die Tickets der Teilnehmer (QR-Code auf PDF-Ticket)</li>
            <li>Alternativ: Manuelle Suche nach Name/Buchungsnummer</li>
            <li>Check-In kann rückgängig gemacht werden (falls Fehler)</li>
        </ul>
        <p>Die Check-In-Statistik zeigt live, wie viele Teilnehmer bereits anwesend sind.</p>
    </section>

    <section>
        <h2>4. Teilnehmerlisten exportieren</h2>
        <p>Exportieren Sie Daten als CSV oder Excel:</p>
        <ul>
            <li>Buchungsnummer, Name, E-Mail, Telefon</li>
            <li>Ticketart, Anzahl, Gesamtpreis</li>
            <li>Zahlungs- und Check-In-Status</li>
            <li>Buchungsdatum</li>
        </ul>
        <p>Nutzen Sie die Filter, um z.B. nur bezahlte oder eingecheckte Teilnehmer zu exportieren.</p>
    </section>

    <section>
        <h2>5. Kommunikation mit Teilnehmern</h2>
        <p>Kontaktieren Sie Teilnehmer über:</p>
        <ul>
            <li>„Teilnehmer kontaktieren" › E-Mail an alle oder gefilterte Gruppe</li>
            <li>Automatische Erinnerungs-E-Mails (24h und 3h vor der Veranstaltung)</li>
            <li>Veranstaltungs-Update-Benachrichtigungen bei Änderungen</li>
        </ul>
    </section>

    <x-ui.alert class="mt-12" tone="info" title="Best Practice">Checken Sie Teilnehmer frühzeitig ein, um Wartezeiten
        zu vermeiden. Nutzen Sie Bulk-Check-In für Gruppen.</x-ui.alert>

    <x-ui.alert tone="info" title="Tipp">Exportieren Sie die Check-In-Liste nach der Veranstaltung für Ihre Statistik
        und Teilnahmebestätigungen.</x-ui.alert>
</x-help.article>
