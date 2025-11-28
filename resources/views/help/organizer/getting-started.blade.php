<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('help.index') }}" class="hover:text-blue-600">Hilfe</a> ·
                <a href="{{ route('help.category','organizer') }}" class="hover:text-blue-600">Veranstalter</a> ·
                <span>Erste Schritte</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Erste Schritte als Organisator</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">So richten Sie Ihre Organisation ein und starten mit dem Erstellen von Veranstaltungen.</p>

            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">1. Organisation auswählen oder erstellen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Gehen Sie im Organizer-Bereich zu „Organisation“ und wählen Sie eine bestehende Organisation aus oder erstellen Sie eine neue. Laden Sie ein Logo hoch und pflegen Sie die Beschreibung sowie Website und Kontakt.</p>
                </section>
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">2. Rechnungs- und Bankdaten hinterlegen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Unter „Kontoverbindung“ hinterlegen Sie Rechnungsadresse, USt-IdNr. und Bankverbindung. Diese Angaben sind notwendig für die automatisierte Rechnungsstellung und Auszahlungen.</p>
                </section>
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">3. Team einladen und Rollen vergeben</h2>
                    <p class="text-gray-700 dark:text-gray-300">Laden Sie Kolleginnen und Kollegen ein, Rollen (z. B. „Editor“, „Finanzen“) vergeben und verwalten Sie Berechtigungen. So können mehrere Personen Veranstaltungen gemeinsam betreuen.</p>
                </section>
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">4. Erstes Event erstellen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Nutzen Sie „Neues Event“ und pflegen Sie Titel, Beschreibung, Kategorie sowie Format (Präsenz, Online oder Hybrid). Fügen Sie Ticket-Typen hinzu und veröffentlichen Sie das Event, sobald alles vollständig ist.</p>
                </section>

                <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded p-4">
                    <p class="text-blue-800 dark:text-blue-200"><strong>Tipp:</strong> Für bessere Sichtbarkeit können Sie Ihr Event als „Featured“ markieren und so prominent hervorheben.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
