<x-layouts.public title="Datenschutzerklärung – {{ config('app.name') }}">
    @push('meta')
        <meta name="robots" content="noindex, follow">
    @endpush

    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-8 lg:p-12">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Datenschutzerklärung</h1>
                <p class="text-sm text-gray-500 mb-8">Stand: {{ date('F Y') }}</p>

                <div class="prose prose-gray max-w-none space-y-8">

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">1. Verantwortlicher</h2>
                        <p class="text-gray-700">
                            Verantwortlich für die Datenverarbeitung auf dieser Website ist:<br><br>
                            <strong>ESDI GmbH</strong><br>
                            [Straße und Hausnummer]<br>
                            [PLZ Ort]<br>
                            E-Mail: <a href="mailto:datenschutz@esdigmbh.de" class="text-blue-600 hover:underline">datenschutz@esdigmbh.de</a>
                        </p>
                    </section>



                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">6. Ihre Rechte</h2>
                        <p class="text-gray-700 mb-2">Sie haben das Recht auf:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
                            <li>Auskunft über Ihre gespeicherten Daten (Art. 15 DSGVO)</li>
                            <li>Berichtigung unrichtiger Daten (Art. 16 DSGVO)</li>
                            <li>Löschung Ihrer Daten (Art. 17 DSGVO)</li>
                            <li>Einschränkung der Verarbeitung (Art. 18 DSGVO)</li>
                            <li>Datenübertragbarkeit (Art. 20 DSGVO)</li>
                            <li>Widerspruch gegen die Verarbeitung (Art. 21 DSGVO)</li>
                            <li>Beschwerde bei der Aufsichtsbehörde</li>
                        </ul>
                        <p class="text-gray-700 mt-4">
                            Für Anfragen wenden Sie sich an:
                            <a href="mailto:info@esdigmbh.de" class="text-blue-600 hover:underline">datenschutz@esdigmbh.de</a>
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">7. Datenlöschung beantragen</h2>
                        <p class="text-gray-700">
                            @auth
                                Eingeloggte Nutzer können in den
                                <a href="{{ route('settings.show') }}" class="text-blue-600 hover:underline">Kontoeinstellungen</a>
                                die Löschung ihres Kontos beantragen.
                            @else
                                Nutzer können nach dem Einloggen in den Kontoeinstellungen die Löschung ihres Kontos beantragen.
                            @endauth
                        </p>
                    </section>

                </div>

                <div class="mt-10 pt-6 border-t border-gray-200">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Zurück zur Startseite
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>


