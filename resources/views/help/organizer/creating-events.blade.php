<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('help.index') }}" class="hover:text-blue-600">Hilfe</a> ·
                <a href="{{ route('help.category','organizer') }}" class="hover:text-blue-600">Veranstalter</a> ·
                <span>Events erstellen & verwalten</span>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Events erstellen & verwalten</h1>
            <p class="text-gray-700 dark:text-gray-300 mb-6">Leitfaden zur vollständigen Pflege von Veranstaltungseinträgen.</p>

            <div class="space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">1. Basisdaten</h2>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300">
                        <li>Titel & Beschreibung (klar, informativ, inkl. Lernziele)</li>
                        <li>Kategorie (passende Bildungsbereiche)</li>
                        <li>Bild & Video (Optionen: Titelbild, Galerie, Video-URL)</li>
                    </ul>
                </section>
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">2. Format & Ort</h2>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300">
                        <li>Präsenz: Venue-Felder inkl. Stadt, PLZ</li>
                        <li>Online: Zugangsdaten (Link, Code) – sichtbar nach Zahlung</li>
                        <li>Hybrid: Kombination aus Präsenz und Online</li>
                    </ul>
                </section>
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">3. Tickets & Kapazitäten</h2>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300">
                        <li>Ticketarten (Standard, Frühbucher, Studierende, etc.)</li>
                        <li>Preisstaffeln & Rabatte (Rabattcodes verwalten)</li>
                        <li>Max. Teilnehmerzahl & Warteliste</li>
                    </ul>
                </section>
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">4. Veröffentlichung & Sichtbarkeit</h2>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300">
                        <li>Entwurf vs. Veröffentlicht</li>
                        <li>Privat (Zugangscode) oder öffentlich</li>
                        <li>Featured Events – Hervorhebung gegen Gebühr</li>
                    </ul>
                </section>
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">5. Medien & Sharing</h2>
                    <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300">
                        <li>Galerie-Bilder hochladen</li>
                        <li>Social Share (Facebook, Twitter, LinkedIn, WhatsApp, E-Mail, Telegram)</li>
                        <li>SEO: Meta-Tags und strukturierte Daten</li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
