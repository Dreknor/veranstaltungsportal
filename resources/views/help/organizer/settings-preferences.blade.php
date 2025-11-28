<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('help.index') }}" class="hover:text-blue-600">Hilfe</a> ·
                <a href="{{ route('help.category','organizer') }}" class="hover:text-blue-600">Veranstalter</a> ·
                <span>Einstellungen & Präferenzen</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Einstellungen & Präferenzen</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">Verwalten Sie Ihre Organisation, Rechnung, Team und Profil.</p>

            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">1. Organisationsdaten & Logo</h2>
                    <p class="text-gray-700 dark:text-gray-300">Unter „Organisation bearbeiten" pflegen Sie:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Name:</strong> Offizieller Organisationsname</li>
                        <li><strong>Beschreibung:</strong> Über Ihre Organisation (wird auf Event-Seiten angezeigt)</li>
                        <li><strong>Logo:</strong> Wird auf Events, Tickets, Rechnungen angezeigt (max. 2MB, JPG/PNG)</li>
                        <li><strong>Website & Kontakt:</strong> E-Mail, Telefon, Website-URL</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Das Logo erscheint automatisch auf allen Ihren Events und in E-Mails an Teilnehmer.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">2. Rechnungseinstellungen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Hinterlegen Sie vollständige Rechnungsdaten:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Firma/Institution:</strong> Offizieller Name für Rechnungen</li>
                        <li><strong>Rechnungsadresse:</strong> Straße, PLZ, Stadt, Bundesland, Land</li>
                        <li><strong>USt-IdNr./Steuernummer:</strong> Für steuerliche Zwecke</li>
                        <li><strong>Rechnungsnummernformat:</strong> Platzhalter {YEAR}, {MONTH}, {COUNTER}</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Ohne vollständige Rechnungsdaten können Sie keine Events veröffentlichen.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">3. Bankverbindung</h2>
                    <p class="text-gray-700 dark:text-gray-300">Hinterlegen Sie Ihre Bankdaten für Auszahlungen:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Kontoinhaber:</strong> Name des Kontoinhabers</li>
                        <li><strong>IBAN:</strong> Internationale Kontonummer</li>
                        <li><strong>BIC/SWIFT:</strong> Bank-Identifikationscode</li>
                        <li><strong>Bankname:</strong> Name Ihrer Bank</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Auszahlungen erfolgen automatisch nach Event-Ende abzgl. Platform-Fee.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">4. Teamverwaltung</h2>
                    <p class="text-gray-700 dark:text-gray-300">Laden Sie Teammitglieder ein und vergeben Sie Rollen:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Owner:</strong> Volle Rechte inkl. Löschung der Organisation</li>
                        <li><strong>Admin:</strong> Kann alles außer Organisation löschen</li>
                        <li><strong>Editor:</strong> Kann Events erstellen/bearbeiten, Buchungen verwalten</li>
                        <li><strong>Viewer:</strong> Kann nur ansehen, keine Änderungen</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Teammitglieder erhalten eine Einladungs-E-Mail und müssen diese bestätigen.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">5. Persönliches Profil (Organizer)</h2>
                    <p class="text-gray-700 dark:text-gray-300">Ihr persönliches Profil ist getrennt von der Organisation:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Vor- und Nachname:</strong> Ihr persönlicher Name</li>
                        <li><strong>Profilbild:</strong> Für Ihr Konto (nicht für Events)</li>
                        <li><strong>E-Mail & Passwort:</strong> Login-Daten</li>
                        <li><strong>Benachrichtigungseinstellungen:</strong> Was möchten Sie per E-Mail erhalten?</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">6. Monetarisierungs-Einstellungen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Einsehen, aber nicht ändern (Admin-Rechte erforderlich):</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Platform-Fee:</strong> Standardmäßig z.B. 5% auf Buchungen</li>
                        <li><strong>Featured-Event-Gebühren:</strong> Kosten für Hervorhebung</li>
                        <li><strong>Individuelle Gebühren:</strong> Können vom Admin angepasst werden</li>
                    </ul>
                </section>

                <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded p-4">
                    <p class="text-amber-800 dark:text-amber-200"><strong>Wichtig:</strong> Halten Sie Ihre Rechnungs- und Bankdaten aktuell, um Verzögerungen bei Auszahlungen zu vermeiden.</p>
                </div>

                <div class="bg-lime-50 dark:bg-lime-900/30 border border-lime-200 dark:border-lime-800 rounded p-4 mt-4">
                    <p class="text-lime-800 dark:text-lime-200"><strong>Tipp:</strong> Laden Sie mehrere Admins ein für Ausfallsicherheit – falls eine Person nicht verfügbar ist.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

