<x-help.article title="Profil & Einstellungen" subtitle="Ihr Profil anpassen und Einstellungen ändern">
    <p>
        Ein vollständiges und gut gepflegtes Profil hilft Ihnen dabei, das Beste aus dem Bildungsportal
        herauszuholen und sich mit anderen Pädagogen zu vernetzen.
    </p>

    <nav aria-label="Inhalt">
        <ul>
            <li><a href="#profile-info">1. Profilinformationen</a></li>
            <li><a href="#profile-photo">2. Profilbild hochladen</a></li>
            <li><a href="#password">3. Passwort ändern</a></li>
            <li><a href="#notifications">4. Benachrichtigungen</a></li>
            <li><a href="#privacy">5. Privatsphäre & Sichtbarkeit</a></li>
            <li><a href="#data">6. Daten exportieren/löschen</a></li>
        </ul>
    </nav>

    <section id="profile-info">
        <h2>1. Profilinformationen bearbeiten</h2>

        <h3>Wo finde ich meine Profileinstellungen?</h3>
        <ol>
            <li>Klicken Sie oben rechts auf Ihren Namen</li>
            <li>Wählen Sie <strong>„Profil“</strong> aus dem Dropdown-Menü</li>
        </ol>

        <h3>Welche Informationen kann ich bearbeiten?</h3>
        <ul>
            <li>
                <strong>Grunddaten:</strong>
                <ul>
                    <li>Vorname und Nachname</li>
                    <li>E-Mail-Adresse</li>
                    <li>Telefonnummer (optional)</li>
                </ul>
            </li>
            <li>
                <strong>Zusatzinformationen:</strong>
                <ul>
                    <li>Bio/Über mich</li>
                </ul>
            </li>
            <li>
                <strong>Interessen:</strong>
                <ul>
                    <li>Themenbereiche auswählen (unter Einstellungen › Interessen &amp; Newsletter)</li>
                    <li>Für personalisierte Empfehlungen</li>
                    <li>Mehrfachauswahl möglich</li>
                </ul>
            </li>
        </ul>

        <x-ui.alert tone="info" title="Tipp">Je vollständiger Ihr Profil, desto besser können wir Ihnen passende
            Fortbildungen empfehlen!</x-ui.alert>
    </section>

    <section id="profile-photo">
        <h2>2. Profilbild hochladen</h2>

        <h3>Schritt-für-Schritt Anleitung</h3>
        <ol>
            <li>Gehen Sie zu <strong>Profil</strong></li>
            <li>Klicken Sie auf das Kamera-Symbol beim aktuellen Profilbild</li>
            <li>Wählen Sie ein Bild von Ihrem Computer aus</li>
            <li>Das Bild wird automatisch zugeschnitten (quadratisches Format)</li>
            <li>Klicken Sie auf <strong>„Speichern“</strong></li>
        </ol>

        <h3>Technische Anforderungen</h3>
        <ul>
            <li><strong>Dateiformate:</strong> JPG, PNG, GIF</li>
            <li><strong>Maximale Größe:</strong> 2 MB</li>
            <li><strong>Empfohlene Auflösung:</strong> Mindestens 400x400 Pixel</li>
            <li><strong>Seitenverhältnis:</strong> Quadratisch (wird automatisch zugeschnitten)</li>
        </ul>

        <x-ui.alert tone="success" title="Empfehlung">Verwenden Sie ein professionelles Foto, das Sie gut erkennbar
            zeigt. Dies erhöht das Vertrauen bei Vernetzungen!</x-ui.alert>
    </section>

    <section id="password">
        <h2>3. Passwort ändern</h2>

        <h3>Passwort sicher ändern</h3>
        <ol>
            <li>Gehen Sie zu <strong>Einstellungen</strong> › <strong>Passwort</strong></li>
            <li>Geben Sie Ihr aktuelles Passwort ein</li>
            <li>
                Wählen Sie ein neues, sicheres Passwort
                <ul>
                    <li>Mindestens 8 Zeichen</li>
                    <li>Groß- und Kleinbuchstaben</li>
                    <li>Mindestens eine Zahl</li>
                    <li>Sonderzeichen empfohlen</li>
                </ul>
            </li>
            <li>Bestätigen Sie das neue Passwort durch erneute Eingabe</li>
            <li>Klicken Sie auf <strong>„Passwort ändern“</strong></li>
        </ol>

        <x-ui.alert tone="warning" title="Sicherheitshinweis">Nach der Passwortänderung werden Sie auf allen Geräten
            abgemeldet und müssen sich mit dem neuen Passwort anmelden.</x-ui.alert>
    </section>

    <section id="notifications">
        <h2>4. Benachrichtigungseinstellungen</h2>
        <p>
            Unter <strong>Einstellungen</strong> › <strong>Benachrichtigungen</strong> können Sie genau festlegen,
            worüber Sie informiert werden möchten.
        </p>

        <h3>Verfügbare Benachrichtigungstypen</h3>
        <ul>
            <li><strong>Neue Veranstaltungen in Lieblingskategorien:</strong> Benachrichtigung, wenn neue
                Veranstaltungen in Ihren favorisierten Themenbereichen erstellt werden</li>
            <li><strong>Veranstaltungs-Erinnerungen:</strong> Automatische Erinnerungen 24h und 3h vor
                Veranstaltungsbeginn</li>
            <li><strong>Buchungsbestätigungen:</strong> Bestätigung bei erfolgreichen Buchungen und Zahlungen</li>
            <li><strong>Veranstaltungs-Änderungen:</strong> Information über Änderungen an gebuchten
                Veranstaltungen</li>
            <li><strong>Neue Verbindungsanfragen:</strong> Benachrichtigung, wenn jemand Ihnen folgen möchte</li>
        </ul>
    </section>

    <section id="privacy">
        <h2>5. Privatsphäre & Sichtbarkeit</h2>
        <p>
            Unter <strong>Einstellungen</strong> › <strong>Datenschutz</strong> können Sie steuern, wer was von
            Ihrem Profil sehen kann: Vernetzung erlauben, öffentliches Profil sowie E-Mail-Adresse und Telefonnummer
            für Verbindungen zeigen.
        </p>
    </section>

    <section id="data">
        <h2>6. Daten exportieren/löschen</h2>
        <p>Gemäß DSGVO haben Sie volle Kontrolle über Ihre Daten.</p>

        <h3>Daten exportieren</h3>
        <ol>
            <li>Gehen Sie zu <strong>Einstellungen</strong> › <strong>Datenschutz</strong> und öffnen Sie <strong>„Meine
                    Daten“</strong></li>
            <li>Klicken Sie auf <strong>„Daten als JSON exportieren“</strong></li>
            <li>Sie erhalten eine JSON-Datei mit allen Ihren Daten zum Download</li>
        </ol>

        <h3>Konto löschen</h3>
        <ol>
            <li>Gehen Sie zu <strong>Einstellungen</strong> › <strong>Datenschutz</strong> und öffnen Sie <strong>„Meine
                    Daten“</strong></li>
            <li>Klicken Sie auf <strong>„Konto löschen beantragen“</strong></li>
            <li>Bestätigen Sie die Löschung durch Eingabe Ihres Passworts</li>
        </ol>

        <x-ui.alert tone="danger" title="Wichtig bei Kontolöschung:">
            <ul>
                <li>Alle Ihre Daten werden unwiderruflich gelöscht</li>
                <li>Aktive Buchungen müssen vorher storniert werden</li>
                <li>Die Löschung kann nicht rückgängig gemacht werden</li>
            </ul>
        </x-ui.alert>
    </section>

    <x-slot:related>
        <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'manage-bookings'])">Vorheriger Artikel:
            Buchungen verwalten</x-ui.button>
        <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'notifications'])">Nächster Artikel:
            Benachrichtigungen</x-ui.button>
    </x-slot:related>
</x-help.article>
