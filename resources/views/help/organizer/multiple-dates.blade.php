<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center mb-2">
                    <a href="{{ route('help.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Events mit mehreren Terminen erstellen</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Anleitung für Kurse, Workshops und Serien-Veranstaltungen</p>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">

                    <!-- Überblick -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Was sind Events mit mehreren Terminen?</h2>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Mit diesem Feature können Sie Veranstaltungen erstellen, die aus mehreren aufeinanderfolgenden Terminen bestehen,
                            z.B. ein 8-Wochen-Yoga-Kurs oder eine Workshop-Reihe mit 5 Terminen.
                        </p>
                        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600 p-4 rounded">
                            <p class="font-semibold text-blue-900 dark:text-blue-200 mb-2">Wichtige Merkmale:</p>
                            <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300">
                                <li><strong>Ein Event, mehrere Termine:</strong> Sie erstellen nur ein Event, keine separaten Events pro Termin</li>
                                <li><strong>Gesamtbuchung:</strong> Teilnehmer buchen immer alle Termine zusammen</li>
                                <li><strong>Eine Kapazität:</strong> Die max. Teilnehmerzahl gilt für die gesamte Serie</li>
                                <li><strong>Ein Preis:</strong> Der Preis gilt für alle Termine zusammen</li>
                            </ul>
                        </div>
                    </section>

                    <!-- Schritt-für-Schritt -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Event mit mehreren Terminen erstellen</h2>
                        <div class="space-y-6">
                            <div class="border-l-4 border-green-600 pl-4">
                                <h3 class="text-xl font-semibold mb-2">Schritt 1: Event anlegen</h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-2">
                                    Erstellen Sie Ihr Event wie gewohnt über <strong>Organizer → Events → Neues Event</strong>
                                </p>
                                <ol class="list-decimal list-inside ml-4 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>Füllen Sie alle Grunddaten aus (Titel, Beschreibung, etc.)</li>
                                    <li>Geben Sie Start- und Enddatum des <strong>ersten Termins</strong> ein</li>
                                    <li>Aktivieren Sie die Checkbox <strong>"Event hat mehrere Termine"</strong></li>
                                    <li>Geben Sie Veranstaltungsort und Preis an (gilt für alle Termine)</li>
                                    <li>Setzen Sie die <strong>Gesamtkapazität</strong> (z.B. 20 Teilnehmer für alle Termine)</li>
                                    <li>Speichern Sie das Event</li>
                                </ol>
                            </div>

                            <div class="border-l-4 border-green-600 pl-4">
                                <h3 class="text-xl font-semibold mb-2">Schritt 2: Weitere Termine hinzufügen</h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-2">
                                    Nach dem Speichern erscheint ein Bereich "Termine" in der Event-Bearbeitungsansicht
                                </p>
                                <ol class="list-decimal list-inside ml-4 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>Klicken Sie auf <strong>"+ Termin hinzufügen"</strong></li>
                                    <li>Geben Sie Start- und Enddatum ein</li>
                                    <li>Optional: Geben Sie einen abweichenden Veranstaltungsort an (z.B. für Ausweichtermine)</li>
                                    <li>Optional: Fügen Sie Hinweise zum Termin hinzu (z.B. "Dieser Termin findet online statt")</li>
                                    <li>Speichern Sie den Termin</li>
                                    <li>Wiederholen Sie dies für alle weiteren Termine</li>
                                </ol>
                            </div>

                            <div class="border-l-4 border-green-600 pl-4">
                                <h3 class="text-xl font-semibold mb-2">Schritt 3: Event veröffentlichen</h3>
                                <p class="text-gray-700 dark:text-gray-300">
                                    Wenn alle Termine eingetragen sind, können Sie das Event über die Bearbeitungsansicht veröffentlichen
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Termine verwalten -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Termine verwalten</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h3 class="font-semibold mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Bearbeiten
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Klicken Sie auf das Stift-Symbol neben einem Termin, um Datum, Uhrzeit oder Veranstaltungsort zu ändern
                                </p>
                            </div>

                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h3 class="font-semibold mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Absagen
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Einzelne Termine können abgesagt werden (z.B. bei Dozentenausfall). Teilnehmer werden automatisch per E-Mail informiert
                                </p>
                            </div>

                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h3 class="font-semibold mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Reaktivieren
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Abgesagte Termine können wieder reaktiviert werden, falls sich die Situation ändert
                                </p>
                            </div>

                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h3 class="font-semibold mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Löschen
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Termine können gelöscht werden, solange mindestens ein Termin übrig bleibt
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Abweichende Veranstaltungsorte -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Abweichende Veranstaltungsorte</h2>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Normalerweise finden alle Termine am gleichen Ort statt (der Ort, den Sie beim Event angegeben haben).
                            Sie können aber für einzelne Termine einen anderen Ort angeben.
                        </p>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded">
                            <p class="font-semibold text-yellow-900 dark:text-yellow-200 mb-2">Beispiel:</p>
                            <p class="text-gray-700 dark:text-gray-300">
                                Ihr Yoga-Kurs findet normalerweise im "Studio Mitte" statt.
                                Für den 3. Termin ist das Studio nicht verfügbar. Sie können dann beim 3. Termin einen alternativen Ort angeben,
                                z.B. "Studio West". Dies wird den Teilnehmern deutlich angezeigt.
                            </p>
                        </div>
                    </section>

                    <!-- Wichtige Hinweise -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Wichtige Hinweise</h2>
                        <div class="space-y-4">
                            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-600 p-4 rounded">
                                <p class="font-semibold text-red-900 dark:text-red-200 mb-2">⚠️ Kapazität</p>
                                <p class="text-gray-700 dark:text-gray-300">
                                    Die angegebene Kapazität (z.B. 20 Teilnehmer) gilt für die <strong>gesamte Serie</strong>,
                                    nicht pro Termin. Das heißt: Maximal 20 Personen können buchen und besuchen dann alle 5 Termine.
                                </p>
                            </div>

                            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600 p-4 rounded">
                                <p class="font-semibold text-blue-900 dark:text-blue-200 mb-2">💰 Preisgestaltung</p>
                                <p class="text-gray-700 dark:text-gray-300">
                                    Der angegebene Preis gilt für <strong>alle Termine zusammen</strong>.
                                    Beispiel: Ein 8-Wochen-Kurs für 120€ bedeutet 120€ für alle 8 Termine (15€ pro Termin).
                                </p>
                            </div>

                            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-600 p-4 rounded">
                                <p class="font-semibold text-green-900 dark:text-green-200 mb-2">✅ Buchungslogik</p>
                                <p class="text-gray-700 dark:text-gray-300">
                                    Teilnehmer können <strong>nicht einzelne Termine</strong> buchen.
                                    Bei der Buchung werden automatisch alle Termine gebucht. Dies stellt sicher,
                                    dass alle Teilnehmer den kompletten Kurs besuchen.
                                </p>
                            </div>

                            <div class="bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-600 p-4 rounded">
                                <p class="font-semibold text-purple-900 dark:text-purple-200 mb-2">📧 Kommunikation</p>
                                <p class="text-gray-700 dark:text-gray-300">
                                    Wenn Sie einen Termin absagen, werden alle gebuchten Teilnehmer automatisch per E-Mail informiert.
                                    Sie können zusätzlich die Funktion "Teilnehmer kontaktieren" nutzen, um weitere Informationen zu versenden.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Beispiele -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6">Anwendungsbeispiele</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition">
                                <div class="text-4xl mb-3">🧘</div>
                                <h3 class="text-xl font-semibold mb-2">Yoga-Kurs (8 Wochen)</h3>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• 8 Termine à 90 Minuten</li>
                                    <li>• Jeden Mittwoch, 18:00-19:30 Uhr</li>
                                    <li>• Max. 20 Teilnehmer</li>
                                    <li>• Preis: 120€ für alle 8 Termine</li>
                                </ul>
                            </div>

                            <div class="border dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition">
                                <div class="text-4xl mb-3">💻</div>
                                <h3 class="text-xl font-semibold mb-2">Programmier-Workshop (5 Tage)</h3>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• 5 Termine à 4 Stunden</li>
                                    <li>• Mo-Fr, 14:00-18:00 Uhr</li>
                                    <li>• Max. 15 Teilnehmer</li>
                                    <li>• Preis: 350€ komplett</li>
                                </ul>
                            </div>

                            <div class="border dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition">
                                <div class="text-4xl mb-3">🎨</div>
                                <h3 class="text-xl font-semibold mb-2">Mal-Kurs (6 Abende)</h3>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• 6 Termine à 2,5 Stunden</li>
                                    <li>• Jeden Dienstag, 19:00-21:30 Uhr</li>
                                    <li>• Max. 12 Teilnehmer</li>
                                    <li>• Preis: 180€ inkl. Material</li>
                                </ul>
                            </div>

                            <div class="border dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition">
                                <div class="text-4xl mb-3">📚</div>
                                <h3 class="text-xl font-semibold mb-2">Fortbildungsreihe (3 Module)</h3>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• 3 Termine à ganztägig</li>
                                    <li>• Samstags, 09:00-17:00 Uhr</li>
                                    <li>• Max. 25 Teilnehmer</li>
                                    <li>• Preis: 450€ mit Zertifikat</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Häufige Fragen -->
                    <section>
                        <h2 class="text-2xl font-bold mb-6">Häufig gestellte Fragen</h2>
                        <div class="space-y-4">
                            <details class="border dark:border-gray-700 rounded-lg p-4">
                                <summary class="font-semibold cursor-pointer">Kann ich Termine nachträglich hinzufügen oder entfernen?</summary>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Ja, Sie können jederzeit weitere Termine hinzufügen. Bereits gebuchte Teilnehmer werden automatisch für neue Termine registriert.
                                    Termine können gelöscht werden, solange mindestens ein Termin übrig bleibt.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-4">
                                <summary class="font-semibold cursor-pointer">Was passiert, wenn ich einen Termin absage?</summary>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Der Termin wird als "abgesagt" markiert und alle Teilnehmer werden per E-Mail informiert.
                                    Die Buchungen bleiben bestehen und die Teilnehmer besuchen die anderen Termine.
                                    Sie können einen Nachholtermin einrichten oder den Termin später reaktivieren.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-4">
                                <summary class="font-semibold cursor-pointer">Kann ich für einzelne Termine unterschiedliche Preise festlegen?</summary>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Nein, es gibt einen Gesamtpreis für alle Termine zusammen.
                                    Wenn Sie unterschiedliche Preise benötigen, sollten Sie separate Events erstellen.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-4">
                                <summary class="font-semibold cursor-pointer">Können Teilnehmer nur einzelne Termine buchen?</summary>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Nein, die Buchung gilt immer für alle Termine. Dies stellt sicher, dass alle Teilnehmer
                                    die komplette Serie besuchen und der Lernfortschritt gewährleistet ist.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-4">
                                <summary class="font-semibold cursor-pointer">Wie funktioniert die Kapazitätsverwaltung?</summary>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Die Kapazität bezieht sich auf die Anzahl der Personen, die die gesamte Serie buchen können.
                                    Bei 20 max. Teilnehmern bedeutet das: 20 Personen besuchen alle Termine, nicht 20 Personen pro Termin.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-4">
                                <summary class="font-semibold cursor-pointer">Kann ich zwischen "Event mit mehreren Terminen" und normalen Events wechseln?</summary>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Sie können die Option "Event hat mehrere Termine" aktivieren oder deaktivieren,
                                    solange noch keine Buchungen vorliegen. Nach der ersten Buchung ist eine Änderung nicht mehr möglich.
                                </p>
                            </details>
                        </div>
                    </section>

                </div>
            </div>

            <!-- Zurück-Link -->
            <div class="mt-8">
                <a href="{{ route('help.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Zurück zur Hilfe-Übersicht
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>

