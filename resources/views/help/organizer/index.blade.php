<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Hilfe für Veranstalter</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Leitfäden und Best Practices für Organisationen und Veranstalter</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('help.article', ['organizer','getting-started']) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Erste Schritte als Organisator</h3>
                    <p class="text-gray-600 dark:text-gray-400">Organisation anlegen, Einstellungen, Team und erstes Event.</p>
                </a>
                <a href="{{ route('help.article', ['organizer','creating-events']) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Events erstellen & verwalten</h3>
                    <p class="text-gray-600 dark:text-gray-400">Datenpflege, Online/Hybrid, Veröffentlichung und Medien.</p>
                </a>
                <a href="{{ route('help.article', ['organizer','managing-bookings']) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Buchungen & Teilnehmer verwalten</h3>
                    <p class="text-gray-600 dark:text-gray-400">Zahlungen, Check-In, Export und Kommunikation.</p>
                </a>
                <a href="{{ route('help.article', ['organizer','tickets-pricing']) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Tickets & Preisgestaltung</h3>
                    <p class="text-gray-600 dark:text-gray-400">Ticketarten, Preise, Rabatte und Kapazitäten.</p>
                </a>
                <a href="{{ route('help.article', ['organizer','analytics-reports']) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Statistiken & Berichte</h3>
                    <p class="text-gray-600 dark:text-gray-400">Kennzahlen, Trends und Exporte.</p>
                </a>
                <a href="{{ route('help.article', ['organizer','communication']) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Kommunikation mit Teilnehmern</h3>
                    <p class="text-gray-600 dark:text-gray-400">E-Mails, Erinnerungen und Datenschutz.</p>
                </a>
                <a href="{{ route('help.article', ['organizer','marketing-promotion']) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Marketing & Promotion</h3>
                    <p class="text-gray-600 dark:text-gray-400">Featured, Social Sharing und Sichtbarkeit.</p>
                </a>
                <a href="{{ route('help.article', ['organizer','settings-preferences']) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Einstellungen & Präferenzen</h3>
                    <p class="text-gray-600 dark:text-gray-400">Organisation, Rechnung, Team & Profil.</p>
                </a>
                <a href="{{ route('help.article', ['organizer','troubleshooting']) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Häufige Probleme & FAQ</h3>
                    <p class="text-gray-600 dark:text-gray-400">Typische Fehler und Lösungen.</p>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
