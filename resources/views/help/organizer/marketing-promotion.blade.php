<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('help.index') }}" class="hover:text-blue-600">Hilfe</a> ·
                <a href="{{ route('help.category','organizer') }}" class="hover:text-blue-600">Veranstalter</a> ·
                <span>Marketing & Promotion</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Marketing & Promotion</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">Erhöhen Sie die Sichtbarkeit Ihrer Veranstaltungen.</p>

            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">1. Featured Events – Hervorhebung</h2>
                    <p class="text-gray-700 dark:text-gray-300">Markieren Sie Ihr Event als „Featured" für maximale Sichtbarkeit:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Featured Events erscheinen prominent auf der Startseite</li>
                        <li>Eigene Sektion mit visueller Hervorhebung</li>
                        <li>Höheres Ranking in Suchergebnissen</li>
                        <li>Gebühr: täglich, wöchentlich oder monatlich buchbar</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Gehen Sie zu „Featured Events" → „Antrag stellen" und wählen Sie die Dauer. Nach Zahlung ist Ihr Event sofort featured.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">2. Social Media Sharing</h2>
                    <p class="text-gray-700 dark:text-gray-300">Jedes Event hat Share-Buttons für:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Facebook:</strong> Teilen auf Ihrer Seite oder in Gruppen</li>
                        <li><strong>Twitter/X:</strong> Tweet mit Event-Link</li>
                        <li><strong>LinkedIn:</strong> Ideal für berufliche Fortbildungen</li>
                        <li><strong>WhatsApp:</strong> Direkt an Kontakte oder Gruppen</li>
                        <li><strong>E-Mail:</strong> Mailto-Link mit vorgefertigtem Text</li>
                        <li><strong>Telegram:</strong> Teilen in Kanälen oder Gruppen</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Zusätzlich: „Link kopieren" für eigene Kanäle (Newsletter, Website, etc.)</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">3. SEO & Sichtbarkeit</h2>
                    <p class="text-gray-700 dark:text-gray-300">Optimieren Sie Ihr Event für Suchmaschinen:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Aussagekräftiger Titel:</strong> Inkl. relevanter Keywords</li>
                        <li><strong>Detaillierte Beschreibung:</strong> Min. 200 Wörter, inkl. Lernziele</li>
                        <li><strong>Kategorie korrekt wählen:</strong> Für bessere Auffindbarkeit</li>
                        <li><strong>Bild hochladen:</strong> Ansprechendes Titelbild erhöht Klickrate</li>
                    </ul>
                    <p class="text-gray-700 dark:text-gray-300 mt-2">Das System generiert automatisch Meta-Tags, Open Graph und strukturierte Daten (Schema.org).</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">4. Netzwerk & Empfehlungen</h2>
                    <p class="text-gray-700 dark:text-gray-300">Nutzen Sie das Netzwerk der Plattform:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Events werden Nutzern basierend auf ihren Interessen empfohlen</li>
                        <li>Nutzer mit passenden Kategorien erhalten Benachrichtigungen bei neuen Events</li>
                        <li>Favoriten-Funktion: Nutzer können Events merken und später buchen</li>
                        <li>Bewertungen: Positive Reviews erhöhen Vertrauen und Sichtbarkeit</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">5. Rabattcodes für Marketing</h2>
                    <p class="text-gray-700 dark:text-gray-300">Nutzen Sie Rabattcodes strategisch:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li><strong>Partner-Codes:</strong> Für Kooperationen mit Schulen, Verbänden</li>
                        <li><strong>Newsletter-Codes:</strong> Exklusive Rabatte für Abonnenten</li>
                        <li><strong>Social-Media-Codes:</strong> Zeitlich begrenzte Aktionen</li>
                        <li><strong>Tracking:</strong> Sehen Sie in der Statistik, welche Codes genutzt wurden</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">6. Eigene Marketing-Kanäle</h2>
                    <p class="text-gray-700 dark:text-gray-300">Bewerben Sie Ihr Event zusätzlich über:</p>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 mt-2">
                        <li>Ihre Website (iFrame-Einbettung oder direkter Link)</li>
                        <li>Newsletter (Event-Link & Titelbild)</li>
                        <li>Flyer & Poster (QR-Code mit Event-Link)</li>
                        <li>Lokale Medien & Bildungsportale</li>
                    </ul>
                </section>

                <div class="bg-cyan-50 dark:bg-cyan-900/30 border border-cyan-200 dark:border-cyan-800 rounded p-4">
                    <p class="text-cyan-800 dark:text-cyan-200"><strong>Tipp:</strong> Kombinieren Sie Frühbucher-Rabatt mit Social-Media-Kampagne für maximale Reichweite in kurzer Zeit.</p>
                </div>

                <div class="bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 rounded p-4 mt-4">
                    <p class="text-rose-800 dark:text-rose-200"><strong>Best Practice:</strong> Investieren Sie in 1-2 Featured-Wochen kurz vor Event-Start für letzten Buchungsschub.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

