<x-layouts.public title="Impressum – {{ config('app.name') }}">
    @push('meta')
        <meta name="robots" content="noindex, follow">
    @endpush

    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-8 lg:p-12">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Impressum</h1>
                <p class="text-sm text-gray-500 mb-8">Angaben gemäß § 5 TMG</p>

                <div class="prose prose-gray max-w-none space-y-8">

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Anbieter</h2>
                        <address class="not-italic text-gray-700 space-y-1">
                            <p><strong>ESDI GmbH</strong></p>
                            <p>[Straße und Hausnummer]</p>
                            <p>[PLZ Ort]</p>
                            <p>Deutschland</p>
                        </address>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Kontakt</h2>
                        <p class="text-gray-700">
                            Telefon: [Telefonnummer]<br>
                            E-Mail: <a href="mailto:info@esdigmbh.de" class="text-blue-600 hover:underline">info@esdigmbh.de</a>
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Handelsregister</h2>
                        <p class="text-gray-700">
                            Registergericht: [Amtsgericht]<br>
                            Registernummer: [HRB-Nummer]
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Vertretungsberechtigte Geschäftsführung</h2>
                        <p class="text-gray-700">
                            [Name der Geschäftsführer]
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Umsatzsteuer-Identifikationsnummer</h2>
                        <p class="text-gray-700">
                            USt-IdNr.: [DE-Nummer gemäß §27a UStG]
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Inhaltlich verantwortlich gemäß § 18 Abs. 2 MStV</h2>
                        <p class="text-gray-700">
                            [Name und Anschrift der verantwortlichen Person]
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

