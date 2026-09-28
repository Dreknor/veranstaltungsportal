<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4 sm:py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('help.index') }}" class="hover:text-blue-600">Hilfe</a> ·
                <a href="{{ route('help.category','organizer') }}" class="hover:text-blue-600">Veranstalter</a> ·
                <span>Buchungen & Teilnehmer verwalten</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Buchungen & Teilnehmer verwalten</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">So behalten Sie den Überblick über Buchungen, Zahlungen und Check-Ins.</p>

            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">1. Der Ablauf einer Buchung</h2>
                    <p class="text-gray-700 dark:text-gray-300">Jede Buchung zeigt oben einen Fortschrittsbalken und einen Kasten <strong>„Nächster Schritt“</strong> – dort steht immer, ob und was Sie tun müssen:</p>
                    <ol class="list-decimal pl-6 text-gray-700 dark:text-gray-300 mt-2 space-y-1">
                        <li><strong>Wartet auf Freigabe</strong> (nur kostenfreie Veranstaltungen mit manueller Freigabe): Anmeldung bestätigen oder ablehnen.</li>
                        <li><strong>Zahlung ausstehend</strong>: Sobald das Geld eingegangen ist, klicken Sie auf „Zahlung eingegangen“. Bei externer Rechnungsstellung markieren Sie die Buchung als fakturiert. PayPal-Zahlungen werden automatisch verbucht.</li>
                        <li><strong>Bestätigt</strong>: Die Person erhält automatisch Tickets bzw. Online-Zugangsdaten. Bei mehreren Plätzen trägt sie vorher die Teilnehmenden ein.</li>
                        <li><strong>Storniert</strong>: Plätze werden automatisch wieder freigegeben, die Warteliste wird informiert.</li>
                    </ol>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Auf der Übersichtsseite „Buchungen“ zeigen Ihnen Hinweise oben an, wie viele Anmeldungen auf Freigabe warten und wie viele Zahlungen offen sind.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">2. Zahlungen, Erstattungen und erneuter Versand</h2>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2 space-y-1">
                        <li><strong>Zahlung eingegangen:</strong> bestätigt die Buchung und versendet Tickets bzw. Zugangsdaten – ein separates „Bestätigen“ ist nicht nötig.</li>
                        <li><strong>Erstattet / Fehlgeschlagen:</strong> Die Person wird automatisch per E-Mail informiert.</li>
                        <li><strong>Erneut senden:</strong> Ist eine E-Mail nicht angekommen, senden Sie im Kasten „Nächster Schritt“ die aktuelle Bestätigung erneut.</li>
                        <li><strong>E-Mail-Verlauf:</strong> Auf jeder Buchung sehen Sie, welche E-Mails wann an wen versendet wurden.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">3. Check-In am Event-Tag</h2>
                    <p class="text-gray-700 dark:text-gray-300">Nutzen Sie den QR-Code-Scanner:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Gehen Sie zu „Check-In" für das Event</li>
                        <li>Scannen Sie die Tickets der Teilnehmer (QR-Code auf PDF-Ticket)</li>
                        <li>Alternativ: Manuelle Suche nach Name/Buchungsnummer</li>
                        <li>Check-In kann rückgängig gemacht werden (falls Fehler)</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Die Check-In-Statistik zeigt live, wie viele Teilnehmer bereits anwesend sind.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">4. Teilnehmerlisten exportieren</h2>
                    <p class="text-gray-700 dark:text-gray-300">Exportieren Sie Daten als CSV oder Excel:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Buchungsnummer, Name, E-Mail, Telefon</li>
                        <li>Ticketart, Anzahl, Gesamtpreis</li>
                        <li>Zahlungs- und Check-In-Status</li>
                        <li>Buchungsdatum</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Nutzen Sie die Filter, um z.B. nur bezahlte oder eingecheckte Teilnehmer zu exportieren.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">5. Kommunikation mit Teilnehmern</h2>
                    <p class="text-gray-700 dark:text-gray-300">Kontaktieren Sie Teilnehmer über:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>„Teilnehmer kontaktieren" → E-Mail an alle oder gefilterte Gruppe</li>
                        <li>Automatische Erinnerungs-E-Mails (24h und 3h vor Event)</li>
                        <li>Event-Update-Benachrichtigungen bei Änderungen</li>
                    </ul>
                </section>

                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded p-4">
                    <p class="text-green-800 dark:text-green-200"><strong>Best Practice:</strong> Checken Sie Teilnehmer frühzeitig ein, um Wartezeiten zu vermeiden. Nutzen Sie Bulk-Check-In für Gruppen.</p>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded p-4 mt-4">
                    <p class="text-yellow-800 dark:text-yellow-200"><strong>Tipp:</strong> Exportieren Sie die Check-In-Liste nach dem Event für Ihre Statistik und Teilnahmebestätigungen.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

