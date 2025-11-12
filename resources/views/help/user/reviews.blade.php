<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center mb-2">
                    <a href="{{ route('help.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Bewertungen schreiben</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Teilen Sie Ihre Erfahrungen mit anderen</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    <div class="mb-8">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Ihre Bewertungen helfen anderen Pädagogen bei der Auswahl passender Fortbildungen und geben Veranstaltern wertvolles Feedback.
                        </p>
                    </div>
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Inhalt</h2>
                        <ul class="space-y-2">
                            <li><a href="#write" class="text-blue-600 dark:text-blue-400 hover:underline">1. Bewertung schreiben</a></li>
                            <li><a href="#guidelines" class="text-blue-600 dark:text-blue-400 hover:underline">2. Bewertungs-Richtlinien</a></li>
                            <li><a href="#manage" class="text-blue-600 dark:text-blue-400 hover:underline">3. Bewertungen verwalten</a></li>
                            <li><a href="#helpful" class="text-blue-600 dark:text-blue-400 hover:underline">4. Hilfreiche Bewertungen</a></li>
                        </ul>
                    </div>
                    <section id="write" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                            Bewertung schreiben
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Wann kann ich bewerten?</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Sie können eine Veranstaltung bewerten, nachdem Sie daran teilgenommen haben. 
                                Nach Abschluss des Events erhalten Sie automatisch eine Einladung zur Bewertung.
                            </p>
                            <h3 class="text-xl font-semibold mt-6">Schritt-für-Schritt Anleitung</h3>
                            <ol class="list-decimal list-inside space-y-3 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Meine Buchungen</strong></li>
                                <li>Wählen Sie eine abgeschlossene Veranstaltung</li>
                                <li>Klicken Sie auf <strong>"Jetzt bewerten"</strong></li>
                                <li>Vergeben Sie Sterne in verschiedenen Kategorien:
                                    <ul class="list-disc list-inside ml-6 mt-2 space-y-1">
                                        <li>Inhalt & Qualität (1-5 Sterne)</li>
                                        <li>Referent/Dozent (1-5 Sterne)</li>
                                        <li>Organisation (1-5 Sterne)</li>
                                        <li>Preis-Leistungs-Verhältnis (1-5 Sterne)</li>
                                    </ul>
                                </li>
                                <li>Schreiben Sie einen Kommentar (optional, aber empfohlen)</li>
                                <li>Geben Sie an, ob Sie die Veranstaltung weiterempfehlen würden</li>
                                <li>Klicken Sie auf <strong>"Bewertung abschicken"</strong></li>
                            </ol>
                            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Belohnung:</strong> Für jede abgegebene Bewertung erhalten Sie Badge-Punkte!</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="guidelines" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                            Bewertungs-Richtlinien
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Was macht eine gute Bewertung aus?</h3>
                            <div class="space-y-3">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-green-600 text-2xl mr-2">✓</span> Konstruktiv und sachlich
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Beschreiben Sie konkret, was gut war und was verbessert werden könnte
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-green-600 text-2xl mr-2">✓</span> Detailliert und hilfreich
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Geben Sie Beispiele und Details, die anderen bei der Entscheidung helfen
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-green-600 text-2xl mr-2">✓</span> Ehrlich und fair
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Seien Sie ehrlich, aber fair. Positive und negative Aspekte benennen
                                    </p>
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold mt-6">Was ist nicht erlaubt?</h3>
                            <div class="space-y-3">
                                <div class="border border-red-300 dark:border-red-700 rounded-lg p-4 bg-red-50 dark:bg-red-900/20">
                                    <h4 class="font-semibold mb-2 flex items-center text-red-700 dark:text-red-400">
                                        <span class="text-2xl mr-2">✗</span> Beleidigende Sprache
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Keine Beleidigungen, Drohungen oder Diskriminierung
                                    </p>
                                </div>
                                <div class="border border-red-300 dark:border-red-700 rounded-lg p-4 bg-red-50 dark:bg-red-900/20">
                                    <h4 class="font-semibold mb-2 flex items-center text-red-700 dark:text-red-400">
                                        <span class="text-2xl mr-2">✗</span> Persönliche Angriffe
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Kritisieren Sie Inhalte, nicht Personen
                                    </p>
                                </div>
                                <div class="border border-red-300 dark:border-red-700 rounded-lg p-4 bg-red-50 dark:bg-red-900/20">
                                    <h4 class="font-semibold mb-2 flex items-center text-red-700 dark:text-red-400">
                                        <span class="text-2xl mr-2">✗</span> Spam oder Werbung
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Keine Werbung für eigene Produkte oder externe Links
                                    </p>
                                </div>
                            </div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Hinweis:</strong> Bewertungen, die gegen die Richtlinien verstoßen, werden moderiert und können gelöscht werden.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="manage" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                            Bewertungen verwalten
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Ihre Bewertungen ansehen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu Ihrem Profil</li>
                                <li>Klicken Sie auf <strong>"Meine Bewertungen"</strong></li>
                                <li>Sie sehen alle Ihre abgegebenen Bewertungen</li>
                            </ol>
                            <h3 class="text-xl font-semibold mt-6">Bewertung bearbeiten</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Sie können Ihre Bewertungen innerhalb von 30 Tagen nach Abgabe bearbeiten:
                            </p>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Öffnen Sie die Bewertung</li>
                                <li>Klicken Sie auf <strong>"Bearbeiten"</strong></li>
                                <li>Ändern Sie Sterne oder Text</li>
                                <li>Speichern Sie die Änderungen</li>
                            </ol>
                            <h3 class="text-xl font-semibold mt-6">Bewertung löschen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Öffnen Sie die Bewertung</li>
                                <li>Klicken Sie auf <strong>"Löschen"</strong></li>
                                <li>Bestätigen Sie die Löschung</li>
                            </ol>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Tipp:</strong> Nach 30 Tagen werden Bewertungen endgültig und können nicht mehr bearbeitet werden.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="helpful" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                            Hilfreiche Bewertungen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Bewertungen als hilfreich markieren</h3>
                            <p class="text-gray-700 dark:text-gray-300">
                                Sie können Bewertungen anderer Nutzer als hilfreich markieren, um besonders nützliche Reviews hervorzuheben.
                            </p>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Lesen Sie eine Bewertung auf einer Event-Seite</li>
                                <li>Klicken Sie auf den Daumen-hoch-Button 👍</li>
                                <li>Die Bewertung erhält einen Hilfreich-Punkt</li>
                            </ol>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🏆 Top-Reviewer Badge</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Erhalten Sie das "Top-Reviewer" Badge wenn 50 Ihrer Bewertungen als hilfreich markiert wurden
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">⭐ Bewertungs-Rang</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Hilfreiche Bewertungen werden weiter oben angezeigt
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
