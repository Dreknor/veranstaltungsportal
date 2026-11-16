<x-help.article title="Veranstaltungen mit mehreren Terminen erstellen"
    subtitle="Anleitung für Kurse, Workshops und Serien-Veranstaltungen" :back-href="route('help.category', 'organizer')"
    back-label="Zur Veranstalter-Hilfe">
    <section>
        <h2>Was sind Veranstaltungen mit mehreren Terminen?</h2>
        <p>
            Mit diesem Feature können Sie Veranstaltungen erstellen, die aus mehreren aufeinanderfolgenden Terminen
            bestehen, z.B. ein 8-Wochen-Yoga-Kurs oder eine Workshop-Reihe mit 5 Terminen.
        </p>
        <x-ui.alert tone="info" title="Wichtige Merkmale">
            <ul>
                <li><strong>Eine Veranstaltung, mehrere Termine:</strong> Sie erstellen nur eine Veranstaltung, keine
                    separaten Veranstaltungen pro Termin</li>
                <li><strong>Gesamtbuchung:</strong> Teilnehmer buchen immer alle Termine zusammen</li>
                <li><strong>Eine Kapazität:</strong> Die max. Teilnehmerzahl gilt für die gesamte Serie</li>
                <li><strong>Ein Preis:</strong> Der Preis gilt für alle Termine zusammen</li>
            </ul>
        </x-ui.alert>
    </section>

    <section>
        <h2>Veranstaltung mit mehreren Terminen erstellen</h2>

        <h3>Schritt 1: Veranstaltung anlegen</h3>
        <p>
            Erstellen Sie Ihre Veranstaltung wie gewohnt über <strong>Organizer › Meine Veranstaltungen › Veranstaltung
                anlegen</strong>
        </p>
        <ol>
            <li>Füllen Sie alle Grunddaten aus (Titel, Beschreibung, etc.)</li>
            <li>Geben Sie Start- und Enddatum des <strong>ersten Termins</strong> ein</li>
            <li>Aktivieren Sie die Checkbox <strong>"Veranstaltung hat mehrere Termine"</strong></li>
            <li>Geben Sie Veranstaltungsort und Preis an (gilt für alle Termine)</li>
            <li>Setzen Sie die <strong>Gesamtkapazität</strong> (z.B. 20 Teilnehmer für alle Termine)</li>
            <li>Speichern Sie die Veranstaltung</li>
        </ol>

        <h3>Schritt 2: Weitere Termine hinzufügen</h3>
        <p>
            Nach dem Speichern erscheint ein Bereich "Termine" in der Bearbeitungsansicht der Veranstaltung
        </p>
        <ol>
            <li>Klicken Sie auf <strong>"+ Termin hinzufügen"</strong></li>
            <li>Geben Sie Start- und Enddatum ein</li>
            <li>Optional: Geben Sie einen abweichenden Veranstaltungsort an (z.B. für Ausweichtermine)</li>
            <li>Optional: Fügen Sie Hinweise zum Termin hinzu (z.B. "Dieser Termin findet online statt")</li>
            <li>Speichern Sie den Termin</li>
            <li>Wiederholen Sie dies für alle weiteren Termine</li>
        </ol>

        <h3>Schritt 3: Veranstaltung veröffentlichen</h3>
        <p>
            Wenn alle Termine eingetragen sind, können Sie die Veranstaltung über die Bearbeitungsansicht
            veröffentlichen
        </p>
    </section>

    <section>
        <h2>Termine verwalten</h2>
        <ul>
            <li><strong>Bearbeiten:</strong> Klicken Sie auf das Stift-Symbol neben einem Termin, um Datum, Uhrzeit oder
                Veranstaltungsort zu ändern</li>
            <li><strong>Absagen:</strong> Einzelne Termine können abgesagt werden (z.B. bei Dozentenausfall).
                Teilnehmer werden automatisch per E-Mail informiert</li>
            <li><strong>Reaktivieren:</strong> Abgesagte Termine können wieder reaktiviert werden, falls sich die
                Situation ändert</li>
            <li><strong>Löschen:</strong> Termine können gelöscht werden, solange mindestens ein Termin übrig bleibt
            </li>
        </ul>
    </section>

    <section>
        <h2>Abweichende Veranstaltungsorte</h2>
        <p>
            Normalerweise finden alle Termine am gleichen Ort statt (der Ort, den Sie bei der Veranstaltung angegeben
            haben). Sie können aber für einzelne Termine einen anderen Ort angeben.
        </p>
        <x-ui.alert tone="info" title="Beispiel">
            Ihr Yoga-Kurs findet normalerweise im "Studio Mitte" statt. Für den 3. Termin ist das Studio nicht
            verfügbar. Sie können dann beim 3. Termin einen alternativen Ort angeben, z.B. "Studio West". Dies wird den
            Teilnehmern deutlich angezeigt.
        </x-ui.alert>
    </section>

    <section>
        <h2>Wichtige Hinweise</h2>

        <h3>Kapazität</h3>
        <p>
            Die angegebene Kapazität (z.B. 20 Teilnehmer) gilt für die <strong>gesamte Serie</strong>, nicht pro
            Termin. Das heißt: Maximal 20 Personen können buchen und besuchen dann alle 5 Termine.
        </p>

        <h3>Preisgestaltung</h3>
        <p>
            Der angegebene Preis gilt für <strong>alle Termine zusammen</strong>. Beispiel: Ein 8-Wochen-Kurs für
            120€ bedeutet 120€ für alle 8 Termine (15€ pro Termin).
        </p>

        <h3>Buchungslogik</h3>
        <p>
            Teilnehmer können <strong>nicht einzelne Termine</strong> buchen. Bei der Buchung werden automatisch alle
            Termine gebucht. Dies stellt sicher, dass alle Teilnehmer den kompletten Kurs besuchen.
        </p>

        <h3>Kommunikation</h3>
        <p>
            Wenn Sie einen Termin absagen, werden alle gebuchten Teilnehmer automatisch per E-Mail informiert. Sie
            können zusätzlich die Funktion "Teilnehmer kontaktieren" nutzen, um weitere Informationen zu versenden.
        </p>
    </section>

    <section>
        <h2>Anwendungsbeispiele</h2>

        <h3>Yoga-Kurs (8 Wochen)</h3>
        <ul>
            <li>8 Termine à 90 Minuten</li>
            <li>Jeden Mittwoch, 18:00-19:30 Uhr</li>
            <li>Max. 20 Teilnehmer</li>
            <li>Preis: 120€ für alle 8 Termine</li>
        </ul>

        <h3>Programmier-Workshop (5 Tage)</h3>
        <ul>
            <li>5 Termine à 4 Stunden</li>
            <li>Mo-Fr, 14:00-18:00 Uhr</li>
            <li>Max. 15 Teilnehmer</li>
            <li>Preis: 350€ komplett</li>
        </ul>

        <h3>Mal-Kurs (6 Abende)</h3>
        <ul>
            <li>6 Termine à 2,5 Stunden</li>
            <li>Jeden Dienstag, 19:00-21:30 Uhr</li>
            <li>Max. 12 Teilnehmer</li>
            <li>Preis: 180€ inkl. Material</li>
        </ul>

        <h3>Fortbildungsreihe (3 Module)</h3>
        <ul>
            <li>3 Termine à ganztägig</li>
            <li>Samstags, 09:00-17:00 Uhr</li>
            <li>Max. 25 Teilnehmer</li>
            <li>Preis: 450€ mit Zertifikat</li>
        </ul>
    </section>

    <section>
        <h2>Häufig gestellte Fragen</h2>
        <details>
            <summary>Kann ich Termine nachträglich hinzufügen oder entfernen?</summary>
            <p>
                Ja, Sie können jederzeit weitere Termine hinzufügen. Bereits gebuchte Teilnehmer werden automatisch für
                neue Termine registriert. Termine können gelöscht werden, solange mindestens ein Termin übrig bleibt.
            </p>
        </details>

        <details>
            <summary>Was passiert, wenn ich einen Termin absage?</summary>
            <p>
                Der Termin wird als "abgesagt" markiert und alle Teilnehmer werden per E-Mail informiert. Die Buchungen
                bleiben bestehen und die Teilnehmer besuchen die anderen Termine. Sie können einen Nachholtermin
                einrichten oder den Termin später reaktivieren.
            </p>
        </details>

        <details>
            <summary>Kann ich für einzelne Termine unterschiedliche Preise festlegen?</summary>
            <p>
                Nein, es gibt einen Gesamtpreis für alle Termine zusammen. Wenn Sie unterschiedliche Preise benötigen,
                sollten Sie separate Veranstaltungen erstellen.
            </p>
        </details>

        <details>
            <summary>Können Teilnehmer nur einzelne Termine buchen?</summary>
            <p>
                Nein, die Buchung gilt immer für alle Termine. Dies stellt sicher, dass alle Teilnehmer die komplette
                Serie besuchen und der Lernfortschritt gewährleistet ist.
            </p>
        </details>

        <details>
            <summary>Wie funktioniert die Kapazitätsverwaltung?</summary>
            <p>
                Die Kapazität bezieht sich auf die Anzahl der Personen, die die gesamte Serie buchen können. Bei 20 max.
                Teilnehmern bedeutet das: 20 Personen besuchen alle Termine, nicht 20 Personen pro Termin.
            </p>
        </details>

        <details>
            <summary>Kann ich zwischen "Veranstaltung mit mehreren Terminen" und normalen Veranstaltungen wechseln?
            </summary>
            <p>
                Sie können die Option "Veranstaltung hat mehrere Termine" aktivieren oder deaktivieren, solange noch
                keine Buchungen vorliegen. Nach der ersten Buchung ist eine Änderung nicht mehr möglich.
            </p>
        </details>
    </section>
</x-help.article>
