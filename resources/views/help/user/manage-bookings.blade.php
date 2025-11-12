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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Buchungen verwalten</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Ihre Buchungen ansehen und verwalten</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Buchungsübersicht</h2>
                        <div class="grid gap-4 md:grid-cols-2 mb-6">
                            <div class="border dark:border-gray-700 rounded p-4">
                                <h3 class="font-semibold mb-2">📋 Buchungsdetails ansehen</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Klicken Sie auf eine Buchung, um alle Details, Tickets und Dokumente zu sehen.</p>
                            </div>
                            <div class="border dark:border-gray-700 rounded p-4">
                                <h3 class="font-semibold mb-2">📥 Downloads</h3>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside">
                                    <li>PDF-Ticket</li>
                                    <li>Rechnung</li>
                                    <li>Teilnahmezertifikat (nach Event)</li>
                                    <li>Kalender-Datei (.ics)</li>
                                </ul>
                            </div>
                        </div>
                    </section>
                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Buchung stornieren</h2>
                        <ol class="list-decimal list-inside space-y-2 ml-4">
                            <li>Öffnen Sie die Buchungsdetails</li>
                            <li>Klicken Sie auf "Buchung stornieren"</li>
                            <li>Bestätigen Sie die Stornierung</li>
                        </ol>
                        <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4">
                            <strong>Hinweis:</strong> Bitte beachten Sie die Stornierungsbedingungen des Veranstalters.
                        </div>
                    </section>
                    <section class="mb-8">
                        <h2 class="text-2xl font-bold mb-4">Zahlungsstatus</h2>
                        <div class="space-y-2">
                            <div class="flex items-center"><span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm mr-2">Ausstehend</span> Zahlung noch nicht eingegangen</div>
                            <div class="flex items-center"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm mr-2">Bezahlt</span> Zahlung bestätigt, Tickets verfügbar</div>
                            <div class="flex items-center"><span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm mr-2">Storniert</span> Buchung wurde storniert</div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
