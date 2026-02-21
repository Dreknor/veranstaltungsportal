<x-layouts.public :title="__('Häufig gestellte Fragen - FAQ')">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- FAQ Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Häufig gestellte Fragen (FAQ)</h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">Finden Sie schnelle Antworten zu den wichtigsten Fragen</p>
        </div>

        <!-- Search Bar -->
        <div class="mb-8">
            <div class="relative">
                <input type="text"
                       id="faqSearch"
                       placeholder="FAQ durchsuchen..."
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- FAQ Tabs -->
        <div class="mb-12" x-data="{ activeTab: 'participants' }">
            <!-- Tab Navigation -->
            <div class="flex flex-wrap gap-2 sm:gap-4 mb-8 border-b border-gray-200 dark:border-gray-700">
                <button @click="activeTab = 'participants'"
                        :class="{ 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400': activeTab === 'participants' }"
                        class="pb-3 px-2 sm:px-4 font-semibold text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                    Für Teilnehmer
                </button>
                <button @click="activeTab = 'organizers'"
                        :class="{ 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400': activeTab === 'organizers' }"
                        class="pb-3 px-2 sm:px-4 font-semibold text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                    Für Veranstalter
                </button>
                <button @click="activeTab = 'technical'"
                        :class="{ 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400': activeTab === 'technical' }"
                        class="pb-3 px-2 sm:px-4 font-semibold text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                    Technisches
                </button>
            </div>

            <!-- Tab Content: Für Teilnehmer -->
            <div x-show="activeTab === 'participants'" class="faq-section space-y-6">
                <!-- Q1 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Wie kann ich mich für eine Veranstaltung anmelden?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Die Anmeldung ist einfach und unkompliziert:
                        </p>
                        <ol class="list-decimal list-inside text-gray-700 dark:text-gray-300 space-y-2">
                            <li>Suchen Sie die gewünschte Veranstaltung in der Veranstaltungsübersicht</li>
                            <li>Klicken Sie auf den Button "Jetzt buchen"</li>
                            <li>Geben Sie Ihre Daten ein (Name, E-Mail, ggf. Telefon)</li>
                            <li>Wählen Sie den gewünschten Termin und Tickettyp, falls zutreffend</li>
                            <li>Bezahlen Sie die Buchungsgebühr (falls vorhanden)</li>
                            <li>Sie erhalten eine Buchungsbestätigung per E-Mail</li>
                        </ol>
                    </div>
                </div>

                <!-- Q2 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Benötige ich ein Benutzerkonto?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            <strong>Nein, ein Benutzerkonto ist nicht erforderlich.</strong> Sie können Veranstaltungen auch ohne Anmeldung buchen. Allerdings bietet ein kostenloser Account mehrere Vorteile:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-2">
                            <li>Alle Ihre Buchungen übersichtlich an einem Ort verwalten</li>
                            <li>Schnellerer Checkout bei zukünftigen Buchungen</li>
                            <li>Automatisches Speichern von Unterlagen und Zertifikaten</li>
                            <li>Benachrichtigungen für neue Veranstaltungen Ihrer Interessensbereiche</li>
                        </ul>
                        <p class="text-gray-700 dark:text-gray-300 mt-4">
                            Sie können Ihre Gastbuchung auch nachträglich mit einem Konto verknüpfen.
                        </p>
                    </div>
                </div>

                <!-- Q3 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Wie erfolgt die Bezahlung?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Die Bezahlung erfolgt über Rechnung, falls die Veranstaltung kostenpflichtig ist. Nach der Buchung erhalten Sie eine Rechnung per E-Mail, die Sie bequem über die angegebene Zahlungsmethode begleichen können.
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            <strong>Wichtig:</strong> Die Tickets oder Buchungsbestätigungen werden versendet, sobald die Zahlung eingegangen ist.
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            Für kostenlose Veranstaltungen entfällt die Bezahlung – Sie erhalten direkt nach der Buchung eine Bestätigung.
                        </p>
                    </div>
                </div>

                <!-- Q4 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Wie erhalte ich die Tickets oder Zugangsinfos?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Die Art der Benachrichtigung hängt vom Veranstaltungstyp ab:
                        </p>
                        <div class="space-y-3">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Präsenzveranstaltungen:</h4>
                                <p class="text-gray-700 dark:text-gray-300">Sie erhalten Tickets per E-Mail, die Sie ausdrucken oder digital vorzeigen können.</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Online-Veranstaltungen:</h4>
                                <p class="text-gray-700 dark:text-gray-300">Sie erhalten den Zugangslink und ggf. Zugangsdaten per E-Mail.</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Hybrid-Veranstaltungen:</h4>
                                <p class="text-gray-700 dark:text-gray-300">Sie erhalten Tickets und Zugangsinfos für beide Varianten.</p>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mt-4">
                            <strong>Hinweis:</strong> Sie können alle Ihre Unterlagen jederzeit von Ihrer Buchungsbestätigungsseite herunterladen.
                        </p>
                    </div>
                </div>

                <!-- Q5 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Kann ich meine Buchung stornieren oder ändern?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            <strong>Änderungen:</strong> Änderungen an Ihren persönlichen Daten (Name, E-Mail, etc.) können Sie nach einer Registrierung selbst vornehmen. Ansonsten kontaktieren Sie bitte den Veranstalter, um Änderungen an Ihrer Buchung zu besprechen.
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            Bei Fragen kontaktieren Sie bitte den Veranstalter unter der angegebenen E-Mail-Adresse.
                        </p>
                    </div>
                </div>

                <!-- Q6 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Kann ich eine Bestätigung oder ein Zertifikat erhalten?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Ja! Nach erfolgter Veranstaltung können Sie Ihre Buchungsbestätigung und ggf. ein Teilnahmezertifikat von der Buchungsseite herunterladen.
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Diese Unterlagen werden vom Veranstalter bereitgestellt und sind im Bereich "Meine Buchungen" verfügbar.
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            Wenn Sie ein Konto haben, sind alle Ihre Unterlagen automatisch gespeichert und jederzeit abrufbar.
                        </p>
                    </div>
                </div>

                <!-- Q7 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Ich habe meine Buchungsnummer verloren – was kann ich tun?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Kein Problem! Sie können Ihre Buchungsdetails mit Ihrer E-Mail-Adresse und Veranstaltungsname wiederfinden.
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Suchen Sie in der E-Mail-Kopie von Ihrer Buchungsbestätigung – dort finden Sie die Buchungsnummer. Falls Sie die E-Mail nicht finden, suchen Sie im Spam-Ordner.
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            Wenn Sie ein Konto haben, können Sie alle Ihre Buchungen im Bereich "Meine Buchungen" einsehen.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Für Veranstalter -->
            <div x-show="activeTab === 'organizers'" class="faq-section space-y-6">
                <!-- Q1 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Ich möchte eine Veranstaltung veröffentlichen – wie geht das?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                            <p class="text-blue-900 dark:text-blue-100 flex items-start">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"></path>
                                </svg>
                                <span><strong>Wichtig:</strong> Veranstalter müssen sich direkt an die Betreiber des Portals wenden, um Veranstaltungen zu veröffentlichen.</span>
                            </p>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Falls Sie eine Veranstaltung organisieren möchten, kontaktieren Sie bitte die Betreiber unter der angegebenen Kontaktadresse. Wir prüfen Ihre Anfrage und unterstützen Sie bei der Einrichtung.
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            Das Portal richtet sich primär an Teilnehmer und Buchende. Für Veranstalter bieten wir spezielle Abläufe und Support.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Tab Content: Technisches -->
            <div x-show="activeTab === 'technical'" class="faq-section space-y-6">
                <!-- Q1 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Welche Browser werden unterstützt?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Das Portal funktioniert auf allen modernen Browsern:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-2">
                            <li>Chrome / Chromium (ab Version 90)</li>
                            <li>Firefox (ab Version 88)</li>
                            <li>Safari (ab Version 14)</li>
                            <li>Edge (ab Version 90)</li>
                        </ul>
                        <p class="text-gray-700 dark:text-gray-300 mt-4">
                            Empfohlen wird die aktuelle Version Ihres Browsers für die beste Kompatibilität und Sicherheit.
                        </p>
                    </div>
                </div>

                <!-- Q2 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Sind meine Daten auf dem Portal sicher?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Ja, wir nehmen den Datenschutz ernst:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-2 mb-4">
                            <li>Alle Datenübertragungen sind verschlüsselt (SSL/TLS)</li>
                            <li>Regelmäßige Sicherheitsupdates und Überprüfungen</li>
                            <li>Compliance mit DSGVO und anderen Datenschutzgesetzen</li>
                            <li>Sicher gehostete Infrastruktur</li>
                            <li>Keine Weitergabe von Daten an Dritte ohne Zustimmung</li>
                        </ul>
                        <p class="text-gray-700 dark:text-gray-300">
                            Weitere Informationen finden Sie in unserer Datenschutzerklärung.
                        </p>
                    </div>
                </div>

                <!-- Q3 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Wird das Portal auf mobilen Geräten unterstützt?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Ja! Das Portal ist vollständig responsive und funktioniert auf:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-2 mb-4">
                            <li>Smartphones (iOS und Android)</li>
                            <li>Tablets (iPad, Android-Tablets)</li>
                            <li>Desktopgeräte und Laptops</li>
                        </ul>
                        <p class="text-gray-700 dark:text-gray-300">
                            Die Benutzeroberfläche passt sich automatisch an Ihr Gerät an. Sie können bequem von überall aus buchen und Ihre Buchungen verwalten.
                        </p>
                    </div>
                </div>

                <!-- Q4 -->
                <div class="faq-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <span class="font-semibold text-gray-900 dark:text-white text-left">Ich habe ein technisches Problem – wie erhalte ich Hilfe?</span>
                        <svg class="w-5 h-5 text-gray-500 transform transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="px-6 py-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-gray-700 dark:text-gray-300 mb-3">
                            Besuchen Sie unser Hilfecenter oder kontaktieren Sie die Betreiber mit folgenden Informationen:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 space-y-2 mb-4">
                            <li>Detaillierte Beschreibung des Problems</li>
                            <li>Name des verwendeten Browsers und Version</li>
                            <li>Gerät (Smartphone, Tablet, Desktop)</li>
                            <li>Zeitpunkt des Fehlers</li>
                            <li>Fehlermeldung (falls vorhanden)</li>
                        </ul>
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Tipp:</strong> Versuchen Sie zuerst, den Browser-Cache zu leeren oder einen anderen Browser zu nutzen.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('faqSearch');
            const faqItems = document.querySelectorAll('.faq-item');

            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = this.value.toLowerCase();

                    faqItems.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</x-layouts.public>

