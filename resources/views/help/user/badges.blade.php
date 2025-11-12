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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 ml-4">Badges & Erfolge</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400 ml-10">Sammeln Sie Auszeichnungen für Ihre Teilnahme</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">
                    <div class="mb-8">
                        <p class="text-lg text-gray-700 dark:text-gray-300">
                            Sammeln Sie Badges für Ihre Fortbildungsaktivitäten und verfolgen Sie Ihren Lernfortschritt auf dem Weg zum lebenslangen Lernen.
                        </p>
                    </div>
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Inhalt</h2>
                        <ul class="space-y-2">
                            <li><a href="#what" class="text-blue-600 dark:text-blue-400 hover:underline">1. Was sind Badges?</a></li>
                            <li><a href="#earn" class="text-blue-600 dark:text-blue-400 hover:underline">2. Badges verdienen</a></li>
                            <li><a href="#types" class="text-blue-600 dark:text-blue-400 hover:underline">3. Badge-Kategorien</a></li>
                            <li><a href="#display" class="text-blue-600 dark:text-blue-400 hover:underline">4. Badges präsentieren</a></li>
                            <li><a href="#leaderboard" class="text-blue-600 dark:text-blue-400 hover:underline">5. Leaderboard</a></li>
                        </ul>
                    </div>
                    <section id="what" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">1</span>
                            Was sind Badges?
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Badges sind digitale Auszeichnungen, die Sie für bestimmte Aktivitäten und Meilensteine auf dem Bildungsportal erhalten. 
                                Sie dokumentieren Ihren Lernweg und motivieren zur kontinuierlichen Weiterbildung.
                            </p>
                            <h3 class="text-xl font-semibold mt-6">Vorteile von Badges</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📊 Fortschritt sichtbar machen</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Dokumentieren Sie Ihre Weiterbildungsaktivitäten auf einen Blick
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🎯 Motivation steigern</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Setzen Sie sich Ziele und feiern Sie Erfolge
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">👥 Profil aufwerten</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Zeigen Sie Ihr Engagement in Ihrem Profil
                                    </p>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🏆 Anerkennung erhalten</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Ihre Leistungen werden anerkannt und gewürdigt
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="earn" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">2</span>
                            Badges verdienen
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Badges werden automatisch vergeben, wenn Sie bestimmte Kriterien erfüllen. Sie müssen sich nicht separat bewerben.
                            </p>
                            <h3 class="text-xl font-semibold mt-6">Wie erhalte ich Badges?</h3>
                            <ol class="list-decimal list-inside space-y-3 text-gray-700 dark:text-gray-300">
                                <li>Nehmen Sie an Veranstaltungen teil</li>
                                <li>Das System prüft automatisch die Badge-Kriterien</li>
                                <li>Bei Erfüllung wird das Badge sofort vergeben</li>
                                <li>Sie erhalten eine Benachrichtigung über neue Badges</li>
                                <li>Das Badge erscheint in Ihrer Badge-Sammlung</li>
                            </ol>
                            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Tipp:</strong> Unter "Meine Badges" sehen Sie, welche Badges Sie noch erreichen können und was dafür nötig ist!</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="types" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">3</span>
                            Badge-Kategorien
                        </h2>
                        <div class="ml-11 space-y-4">
                            <div class="space-y-4">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-2xl mr-2">🎓</span> Teilnahme-Badges
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Für die Teilnahme an Veranstaltungen
                                    </p>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside ml-4">
                                        <li>Erste Veranstaltung - Nach dem ersten besuchten Event</li>
                                        <li>Fortbildungs-Enthusiast - 10 Veranstaltungen besucht</li>
                                        <li>Wissensdurstig - 25 Veranstaltungen besucht</li>
                                        <li>Lebenslanges Lernen - 50 Veranstaltungen besucht</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-2xl mr-2">⏱️</span> Stunden-Badges
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Basierend auf absolvierten Fortbildungsstunden
                                    </p>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside ml-4">
                                        <li>10 Stunden - Bronze</li>
                                        <li>25 Stunden - Silber</li>
                                        <li>50 Stunden - Gold</li>
                                        <li>100 Stunden - Platin</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-2xl mr-2">🎯</span> Themen-Badges
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Für Expertise in bestimmten Themenbereichen
                                    </p>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside ml-4">
                                        <li>Digitalisierungs-Experte - 5 Events zu Digitalisierung</li>
                                        <li>Inklusions-Spezialist - 5 Events zu Inklusion</li>
                                        <li>Pädagogik-Profi - 5 Events zu Pädagogik</li>
                                    </ul>
                                </div>
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2 flex items-center">
                                        <span class="text-2xl mr-2">🤝</span> Community-Badges
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        Für soziales Engagement auf der Plattform
                                    </p>
                                    <ul class="text-sm text-gray-600 dark:text-gray-400 list-disc list-inside ml-4">
                                        <li>Netzwerker - 10 Verbindungen aufgebaut</li>
                                        <li>Reviewer - 10 hilfreiche Bewertungen geschrieben</li>
                                        <li>Empfehler - Events mit Kollegen geteilt</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="display" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">4</span>
                            Badges präsentieren
                        </h2>
                        <div class="ml-11 space-y-4">
                            <h3 class="text-xl font-semibold">Ihre Badge-Sammlung ansehen</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Gehen Sie zu <strong>Meine Badges</strong> in der Navigation</li>
                                <li>Sie sehen alle verdienten Badges mit Beschreibung</li>
                                <li>Gesperrte Badges zeigen, was noch möglich ist</li>
                            </ol>
                            <h3 class="text-xl font-semibold mt-6">Badges im Profil hervorheben</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                <li>Klicken Sie auf ein Badge in Ihrer Sammlung</li>
                                <li>Wählen Sie <strong>"Als Highlight festlegen"</strong></li>
                                <li>Bis zu 3 Badges können prominent im Profil angezeigt werden</li>
                                <li>Diese Badges sehen andere Nutzer beim Besuch Ihres Profils</li>
                            </ol>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Tipp:</strong> Wählen Sie Badges, die Ihre Expertise am besten repräsentieren!</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="leaderboard" class="mb-12">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">5</span>
                            Leaderboard
                        </h2>
                        <div class="ml-11 space-y-4">
                            <p class="text-gray-700 dark:text-gray-300">
                                Im Leaderboard sehen Sie, wie Sie im Vergleich zu anderen Nutzern abschneiden.
                            </p>
                            <h3 class="text-xl font-semibold mt-6">Ranglisten-Kategorien</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🏆 Gesamt-Punkte</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Basierend auf allen verdienten Badges
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">📅 Diesen Monat</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Aktivste Nutzer im aktuellen Monat
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">⏱️ Fortbildungsstunden</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Meiste absolvierte Stunden
                                    </p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">🎯 Themen-Experten</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Führend in bestimmten Themenbereichen
                                    </p>
                                </div>
                            </div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mt-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <p><strong>Privatsphäre:</strong> Sie können in den Einstellungen wählen, ob Sie im Leaderboard erscheinen möchten.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
