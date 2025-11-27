<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Häufig gestellte Fragen (FAQ)</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Antworten auf die wichtigsten Fragen zu Events mit mehreren Terminen</p>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">

                    <!-- Für Teilnehmer -->
                    <section class="mb-12">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Für Teilnehmer
                        </h2>
                        <div class="space-y-4">
                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition" open>
                                <summary class="font-semibold cursor-pointer text-lg">Was bedeutet "Event mit mehreren Terminen"?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Ein Event mit mehreren Terminen ist eine Veranstaltung, die aus mehreren aufeinanderfolgenden Terminen besteht,
                                    z.B. ein 8-Wochen-Yoga-Kurs oder ein 5-tägiger Workshop. Bei der Buchung erhalten Sie automatisch Zugang zu
                                    <strong>allen Terminen</strong> der Serie.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Kann ich nur einzelne Termine buchen?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Nein, bei Events mit mehreren Terminen ist das nicht möglich. Die Buchung gilt immer für alle Termine.
                                    Dies stellt sicher, dass alle Teilnehmer die komplette Serie besuchen können und z.B. bei Kursen
                                    der Lernfortschritt gewährleistet ist.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Wie erkenne ich, ob ein Event mehrere Termine hat?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Auf der Event-Detailseite sehen Sie eine Liste aller Termine mit einem Badge "X Termine".
                                    Zusätzlich gibt es einen Hinweis, dass die Buchung für alle Termine gilt.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Was kostet die Teilnahme?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Der angezeigte Preis gilt für <strong>alle Termine zusammen</strong>, nicht pro Termin.
                                    Ein 8-Wochen-Kurs für 120€ bedeutet 120€ für die komplette Serie (alle 8 Termine).
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Was passiert, wenn ein Termin ausfällt?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Wenn der Veranstalter einen einzelnen Termin absagen muss (z.B. wegen Krankheit), werden Sie per E-Mail informiert.
                                    Die anderen Termine finden normal statt. Der Veranstalter kann einen Nachholtermin anbieten oder den Termin später reaktivieren.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Was passiert, wenn ich einen Termin verpasse?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Ihre Buchung bleibt gültig und Sie können die anderen Termine besuchen.
                                    Kontaktieren Sie den Veranstalter, um ggf. Unterlagen oder Informationen zum verpassten Termin zu erhalten.
                                    Eine teilweise Rückerstattung ist normalerweise nicht möglich.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Kann ich stornieren?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Die Stornierungsbedingungen werden vom Veranstalter festgelegt. In der Regel können Sie bis 24 Stunden vor dem
                                    <strong>ersten Termin</strong> kostenlos stornieren. Bei einer Stornierung wird die gesamte Serie storniert,
                                    nicht nur einzelne Termine.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Wie erhalte ich meine Tickets?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Nach erfolgreicher Buchung und Bezahlung erhalten Sie per E-Mail Ihre Tickets.
                                    Das Ticket gilt für alle Termine der Serie. Sie müssen nicht für jeden Termin ein separates Ticket haben.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Finden alle Termine am gleichen Ort statt?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Normalerweise ja. In seltenen Fällen kann der Veranstalter für einzelne Termine einen abweichenden Ort angeben
                                    (z.B. Ausweichraum). Dies wird deutlich bei jedem Termin angezeigt.
                                </p>
                            </details>
                        </div>
                    </section>

                    <!-- Für Veranstalter -->
                    <section>
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Für Veranstalter
                        </h2>
                        <div class="space-y-4">
                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Wann sollte ich "Event mit mehreren Terminen" verwenden?</summary>
                                <div class="mt-3 text-gray-600 dark:text-gray-400">
                                    <p class="mb-2">Verwenden Sie diese Option für:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1">
                                        <li>Kurse, die über mehrere Wochen gehen (z.B. 8-Wochen-Yoga-Kurs)</li>
                                        <li>Mehrtägige Workshops (z.B. 3-Tages-Seminar)</li>
                                        <li>Fortbildungsreihen mit mehreren Modulen</li>
                                        <li>Alle Veranstaltungen, bei denen Teilnehmer alle Termine besuchen sollen</li>
                                    </ul>
                                </div>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Wie viele Termine kann ich erstellen?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Es gibt keine technische Begrenzung. Sie können beliebig viele Termine zu einem Event hinzufügen.
                                    Für eine gute Übersichtlichkeit empfehlen wir jedoch maximal 20-30 Termine pro Event.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Wie funktioniert die Kapazitätsverwaltung?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Die Kapazität (max. Teilnehmerzahl) gilt für die <strong>gesamte Serie</strong>.
                                    Wenn Sie 20 als Kapazität angeben, können maximal 20 Personen buchen und diese besuchen dann alle Termine.
                                    Es ist nicht "20 Plätze pro Termin".
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Kann ich nachträglich Termine hinzufügen?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Ja, Sie können jederzeit weitere Termine hinzufügen, auch wenn bereits Buchungen vorliegen.
                                    Bereits gebuchte Teilnehmer werden automatisch für die neuen Termine registriert und sollten manuell informiert werden.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Was passiert, wenn ich einen Termin absage?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Klicken Sie beim betreffenden Termin auf "Absagen" und geben Sie einen Grund an.
                                    Alle gebuchten Teilnehmer werden automatisch per E-Mail informiert. Die Buchungen bleiben bestehen und
                                    die Teilnehmer können die anderen Termine besuchen. Sie können den Termin später reaktivieren oder einen Nachholtermin anlegen.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Kann ich für einzelne Termine unterschiedliche Preise festlegen?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Nein, es gibt einen Gesamtpreis für alle Termine. Dieser Preis wird bei der Buchung einmalig fällig.
                                    Wenn Sie unterschiedliche Preise pro Termin benötigen, sollten Sie separate Events erstellen.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Kann ich Ticket-Typen nutzen (z.B. Ermäßigung)?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Ja, Sie können verschiedene Ticket-Typen erstellen (z.B. Standard, Ermäßigt, VIP).
                                    Jeder Ticket-Typ gilt dann für alle Termine der Serie.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Wie kommuniziere ich mit Teilnehmern?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Nutzen Sie die Funktion "Teilnehmer kontaktieren" in der Event-Verwaltung, um E-Mails an alle
                                    gebuchten Teilnehmer zu senden (z.B. Erinnerungen, Materialinfos, Änderungen).
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Kann ich zwischen normalen Events und Events mit mehreren Terminen wechseln?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Sie können die Option aktivieren/deaktivieren, solange keine Buchungen vorliegen.
                                    Nach der ersten Buchung ist eine Änderung nicht mehr möglich, um die Integrität der Buchungen zu gewährleisten.
                                </p>
                            </details>

                            <details class="border dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition">
                                <summary class="font-semibold cursor-pointer text-lg">Wie sieht die Teilnehmerliste aus?</summary>
                                <p class="mt-3 text-gray-600 dark:text-gray-400">
                                    Die Teilnehmerliste zeigt alle Personen, die die gesamte Serie gebucht haben.
                                    Sie können die Liste als Excel/CSV exportieren und für jeden Termin zur Anwesenheitskontrolle nutzen.
                                </p>
                            </details>
                        </div>
                    </section>

                </div>
            </div>

            <!-- Kontakt-Box -->
            <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200 mb-2">Weitere Fragen?</h3>
                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    Wenn Sie weitere Fragen haben, die hier nicht beantwortet werden, können Sie uns gerne kontaktieren.
                </p>
                <div class="flex gap-4">
                    <a href="{{ route('help.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Hilfe-Center
                    </a>
                    <a href="mailto:support@veranstaltungen.local" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Support kontaktieren
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

