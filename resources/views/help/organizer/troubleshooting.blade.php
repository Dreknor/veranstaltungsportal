<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('help.index') }}" class="hover:text-blue-600">Hilfe</a> ·
                <a href="{{ route('help.category','organizer') }}" class="hover:text-blue-600">Veranstalter</a> ·
                <span>Häufige Probleme & FAQ</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Häufige Probleme & FAQ</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">Lösungen für typische Fragen und Fehler.</p>

            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">❓ Warum kann ich mein Event nicht veröffentlichen?</h2>
                    <p class="text-gray-700 dark:text-gray-300">Das Event kann nur veröffentlicht werden, wenn:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Rechnungsdaten der Organisation vollständig sind</li>
                        <li>Bankverbindung hinterlegt ist</li>
                        <li>Mindestens ein Ticket-Typ erstellt wurde</li>
                        <li>Alle Pflichtfelder ausgefüllt sind (Titel, Datum, Ort bzw. Online-Link)</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2"><strong>Lösung:</strong> Prüfen Sie „Organisation" → „Rechnungsdaten" und „Bankverbindung". Ergänzen Sie fehlende Angaben.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">📧 Teilnehmer haben keine E-Mail erhalten</h2>
                    <p class="text-gray-700 dark:text-gray-300">Mögliche Ursachen:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>E-Mail im Spam-Ordner gelandet</li>
                        <li>Falsche E-Mail-Adresse bei Buchung angegeben</li>
                        <li>E-Mail-Server temporär nicht erreichbar</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2"><strong>Lösung:</strong> Prüfen Sie in der Buchungsdetails die E-Mail-Adresse. Nutzen Sie „Erneut versenden" bei der Buchung, um die Bestätigung nochmals zu schicken.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">💳 Zahlungsstatus aktualisiert sich nicht</h2>
                    <p class="text-gray-700 dark:text-gray-300">Bei manuellen Zahlungen (Überweisung, Barzahlung):</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Automatische Aktualisierung nur bei Online-Zahlung (Stripe, PayPal)</li>
                        <li>Bei Überweisung: Manuell auf „Paid" setzen nach Zahlungseingang</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2"><strong>Lösung:</strong> Gehen Sie zu „Buchungen" → Buchung öffnen → „Zahlungsstatus" → „Paid" auswählen. System versendet dann automatisch Tickets.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">🎟️ QR-Code funktioniert beim Check-In nicht</h2>
                    <p class="text-gray-700 dark:text-gray-300">Troubleshooting:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>QR-Code nur auf bezahlten Tickets gültig</li>
                        <li>Kamera-Berechtigung im Browser erteilt?</li>
                        <li>Ausreichend Licht für Scanner?</li>
                        <li>PDF korrekt geöffnet (nicht Screenshot)?</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2"><strong>Lösung:</strong> Nutzen Sie alternativ die manuelle Suche nach Buchungsnummer oder Name.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">👥 Ich sehe keine Teammitglieder</h2>
                    <p class="text-gray-700 dark:text-gray-300">Teammitglieder müssen die Einladung annehmen:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Einladungs-E-Mail wurde versendet → Postfach prüfen</li>
                        <li>Link in E-Mail anklicken und Einladung bestätigen</li>
                        <li>Danach erscheinen sie unter „Team"</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2"><strong>Lösung:</strong> Erneut einladen oder Person bitten, Spam-Ordner zu prüfen.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">💰 Wo finde ich meine Platform-Fee-Rechnungen?</h2>
                    <p class="text-gray-700 dark:text-gray-300">Rechnungen werden automatisch erstellt:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Nach Event-Ende (innerhalb von 1-2 Tagen)</li>
                        <li>Zugriff unter „Rechnungen" im Organizer-Bereich</li>
                        <li>PDF-Download und E-Mail-Versand an Ihre hinterlegte Adresse</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">🔒 Keine Berechtigung für Aktion</h2>
                    <p class="text-gray-700 dark:text-gray-300">Prüfen Sie Ihre Rolle in der Organisation:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Viewer:</strong> Kann nur ansehen, nichts bearbeiten</li>
                        <li><strong>Editor:</strong> Kann Events und Buchungen verwalten</li>
                        <li><strong>Admin/Owner:</strong> Volle Rechte</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2"><strong>Lösung:</strong> Kontaktieren Sie den Owner Ihrer Organisation für Rollenzuweisung.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">📊 Statistiken zeigen keine Daten</h2>
                    <p class="text-gray-700 dark:text-gray-300">Mögliche Gründe:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Noch keine Buchungen für dieses Event vorhanden</li>
                        <li>Zeitraum-Filter zu eng gewählt</li>
                        <li>Browser-Cache veraltet</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2"><strong>Lösung:</strong> Wählen Sie „Gesamtzeitraum" im Filter. Cache löschen (Strg+F5).</p>
                </section>

                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded p-4">
                    <p class="text-red-800 dark:text-red-200"><strong>Wichtig:</strong> Bei technischen Problemen, die Sie nicht lösen können, kontaktieren Sie den Support mit Screenshots und Fehlermeldung.</p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-4 mt-4">
                    <p class="text-gray-800 dark:text-gray-200"><strong>Weitere Hilfe benötigt?</strong> Durchsuchen Sie unsere <a href="{{ route('help.index') }}" class="text-blue-600 hover:text-blue-800">Hilfe-Artikel</a> oder kontaktieren Sie den Support.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

