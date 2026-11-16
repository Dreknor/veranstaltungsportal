<x-help.article title="Erste Schritte" subtitle="Lernen Sie die Grundlagen der Plattform kennen">
    <section>
        <h2>Willkommen im Bildungsportal</h2>
        <p>
            Diese Anleitung hilft Ihnen dabei, die ersten Schritte auf unserer Plattform zu machen und sich mit den
            wichtigsten Funktionen vertraut zu machen.
        </p>
    </section>

    <nav aria-label="Inhalt">
        <ul>
            <li><a href="#registration">1. Registrierung und Anmeldung</a></li>
            <li><a href="#profile">2. Profil einrichten</a></li>
            <li><a href="#dashboard">3. Dashboard kennenlernen</a></li>
            <li><a href="#first-event">4. Erste Veranstaltung finden</a></li>
            <li><a href="#notifications">5. Benachrichtigungen einrichten</a></li>
            <li><a href="#next-steps">6. Nächste Schritte</a></li>
        </ul>
    </nav>

    <section id="registration">
        <h2>1. Registrierung und Anmeldung</h2>

        <h3>Neues Konto erstellen</h3>
        <ol>
            <li>Klicken Sie oben rechts auf <strong>„Registrieren“</strong></li>
            <li>
                Füllen Sie das Registrierungsformular aus:
                <ul>
                    <li>Vorname und Nachname</li>
                    <li>E-Mail-Adresse (wird als Login verwendet)</li>
                    <li>Sicheres Passwort (mindestens 8 Zeichen)</li>
                </ul>
            </li>
            <li>Akzeptieren Sie die Nutzungsbedingungen und Datenschutzerklärung</li>
            <li>Klicken Sie auf <strong>„Registrieren“</strong></li>
            <li>Sie erhalten eine Bestätigungs-E-Mail – klicken Sie auf den Link zur Verifizierung</li>
        </ol>

        <x-ui.alert tone="warning" title="Wichtig">Prüfen Sie auch Ihren Spam-Ordner, falls die E-Mail nicht
            ankommt.</x-ui.alert>

        <h3>Anmelden</h3>
        <p>
            Nach erfolgreicher Registrierung können Sie sich jederzeit mit Ihrer E-Mail-Adresse und Ihrem Passwort
            anmelden. Klicken Sie dazu oben rechts auf <strong>„Anmelden“</strong>.
        </p>
    </section>

    <section id="profile">
        <h2>2. Profil einrichten</h2>
        <p>
            Ein vollständiges Profil hilft Ihnen dabei, das Beste aus dem Bildungsportal herauszuholen und sich mit
            anderen Pädagogen zu vernetzen.
        </p>

        <h3>Profilbild hochladen</h3>
        <ol>
            <li>Klicken Sie oben rechts auf Ihren Namen</li>
            <li>Wählen Sie <strong>„Profil“</strong></li>
            <li>Klicken Sie auf den Kamera-Button beim Profilbild</li>
            <li>Wählen Sie ein Foto (max. 2MB, JPG/PNG/GIF)</li>
            <li>Klicken Sie auf <strong>„Speichern“</strong></li>
        </ol>

        <h3>Zusätzliche Informationen</h3>
        <p>Ergänzen Sie Ihr Profil mit weiteren Informationen:</p>
        <ul>
            <li><strong>Telefonnummer:</strong> Für wichtige Benachrichtigungen (optional)</li>
            <li><strong>Bio:</strong> Stellen Sie sich kurz vor (z.B. Ihre Fächer, Schule, Interessen)</li>
            <li><strong>Interessen:</strong> Wählen Sie Kategorien aus, um personalisierte Empfehlungen zu erhalten
            </li>
        </ul>

        <x-ui.alert tone="info" title="Tipp">Je vollständiger Ihr Profil, desto besser können wir Ihnen passende
            Fortbildungen empfehlen!</x-ui.alert>
    </section>

    <section id="dashboard">
        <h2>3. Dashboard kennenlernen</h2>
        <p>
            Ihr persönliches Dashboard ist Ihre Zentrale. Hier finden Sie alle wichtigen Informationen auf einen
            Blick.
        </p>

        <h3>Hauptbereiche</h3>
        <ul>
            <li><strong>Statistiken:</strong> Sehen Sie Ihre Buchungen, besuchte Veranstaltungen und
                Fortbildungsstunden auf einen Blick.</li>
            <li><strong>Kommende Veranstaltungen:</strong> Alle Ihre anstehenden Veranstaltungen chronologisch
                sortiert.</li>
            <li><strong>Empfehlungen:</strong> Personalisierte Vorschläge basierend auf Ihren Interessen.</li>
            <li><strong>Abzeichen:</strong> Ihre gesammelten Auszeichnungen und Fortschritte.</li>
        </ul>
    </section>

    <section id="first-event">
        <h2>4. Erste Veranstaltung finden</h2>
        <p>Es gibt mehrere Wege, passende Fortbildungen zu finden:</p>

        <h3>Veranstaltungsübersicht</h3>
        <ol>
            <li>Klicken Sie in der Navigation auf <strong>„Veranstaltungen“</strong></li>
            <li>
                Nutzen Sie die Filter:
                <ul>
                    <li>Kategorien (z.B. Pädagogik, Digitalisierung, Schulentwicklung)</li>
                    <li>Datum und Zeitraum</li>
                    <li>Ort (vor Ort oder online)</li>
                    <li>Preis</li>
                </ul>
            </li>
            <li>Klicken Sie auf eine Veranstaltung, um Details zu sehen</li>
        </ol>

        <h3>Kalender-Ansicht</h3>
        <p>
            Nutzen Sie die <strong>Kalender-Ansicht</strong>, um Veranstaltungen nach Datum zu durchsuchen. Diese
            Ansicht hilft Ihnen, freie Termine zu finden und Ihre Fortbildungen zu planen.
        </p>

        <h3>Favoriten speichern</h3>
        <p>
            Interessante Veranstaltungen können Sie mit dem Herz-Symbol als Favorit markieren. So finden Sie sie
            später schnell wieder unter <strong>„Favoriten“</strong>.
        </p>
    </section>

    <section id="notifications">
        <h2>5. Benachrichtigungen einrichten</h2>
        <p>Bleiben Sie auf dem Laufenden mit personalisierten Benachrichtigungen.</p>

        <h3>Benachrichtigungseinstellungen anpassen</h3>
        <ol>
            <li>Gehen Sie zu <strong>Einstellungen</strong> › <strong>Benachrichtigungen</strong></li>
            <li>
                Wählen Sie aus, worüber Sie informiert werden möchten:
                <ul>
                    <li>Neue Veranstaltungen in Ihren Lieblingskategorien</li>
                    <li>Erinnerungen vor Veranstaltungen (24h und 3h)</li>
                    <li>Buchungsbestätigungen und Zahlungsinformationen</li>
                    <li>Änderungen an gebuchten Veranstaltungen</li>
                    <li>Neue Verbindungsanfragen</li>
                    <li>Benachrichtigungen über neue Abzeichen</li>
                </ul>
            </li>
            <li>Speichern Sie Ihre Einstellungen</li>
        </ol>

        <x-ui.alert tone="success" title="Empfehlung">Aktivieren Sie Veranstaltungs-Erinnerungen, damit Sie keine
            wichtigen Fortbildungen verpassen!</x-ui.alert>
    </section>

    <section id="next-steps">
        <h2>6. Nächste Schritte</h2>
        <p>Jetzt sind Sie bereit, das Bildungsportal voll zu nutzen! Hier sind einige Vorschläge:</p>
        <ul>
            <li><a href="{{ route('help.article', ['category' => 'user', 'article' => 'booking-events']) }}">Veranstaltungen
                    buchen</a>
                – Lernen Sie, wie Sie Tickets buchen und bezahlen.</li>
            <li><a href="{{ route('help.article', ['category' => 'user', 'article' => 'social-features']) }}">Vernetzen
                    Sie sich</a>
                – Knüpfen Sie Kontakte mit anderen Pädagogen.</li>
            <li><a href="{{ route('help.article', ['category' => 'user', 'article' => 'badges']) }}">Badges sammeln</a>
                – Verfolgen Sie Ihren Lernfortschritt mit Abzeichen.</li>
            <li><a href="{{ route('help.article', ['category' => 'user', 'article' => 'favorites']) }}">Favoriten
                    nutzen</a>
                – Speichern Sie interessante Veranstaltungen.</li>
        </ul>
    </section>

    <x-slot:related>
        <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'finding-events'])">Nächster Artikel:
            Veranstaltungen finden</x-ui.button>
    </x-slot:related>
</x-help.article>
