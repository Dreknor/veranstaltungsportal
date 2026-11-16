<x-layouts.public :title="__('Häufig gestellte Fragen - FAQ')">
    <div class="mx-auto max-w-page px-page py-[clamp(32px,6vw,64px)]">
        <div class="max-w-prose">
            <x-ui.page-header title="Häufig gestellte Fragen (FAQ)"
                subtitle="Finden Sie schnelle Antworten zu den wichtigsten Fragen" />

            <!-- Suche -->
            <x-ui.field label="FAQ durchsuchen" id="faqSearch" type="search" class="mb-8" />

            <!-- FAQ-Bereiche -->
            <div x-data="{ activeTab: 'participants' }">
                <x-ui.tabs label="Bereiche" variant="toggle" class="mb-6">
                    <x-ui.tab x-on:click="activeTab = 'participants'" x-bind:aria-pressed="(activeTab === 'participants').toString()" active>Für Teilnehmer</x-ui.tab>
                    <x-ui.tab x-on:click="activeTab = 'organizers'" x-bind:aria-pressed="(activeTab === 'organizers').toString()">Für Veranstalter</x-ui.tab>
                    <x-ui.tab x-on:click="activeTab = 'technical'" x-bind:aria-pressed="(activeTab === 'technical').toString()">Technisches</x-ui.tab>
                </x-ui.tabs>

            <!-- Tab Content: Für Teilnehmer -->
            <div x-show="activeTab === 'participants'" x-cloak class="faq-section border-t border-line">
                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Wie kann ich mich für eine Veranstaltung anmelden?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    Die Anmeldung ist einfach und unkompliziert:
                    </p>
                    <ol>
                    <li>Suchen Sie die gewünschte Veranstaltung in der Veranstaltungsübersicht</li>
                    <li>Klicken Sie auf den Button „Buchen“</li>
                    <li>Geben Sie Ihre Daten ein (Name, E-Mail, ggf. Telefon)</li>
                    <li>Wählen Sie den gewünschten Termin und Tickettyp, falls zutreffend</li>
                    <li>Bezahlen Sie die Buchungsgebühr (falls vorhanden)</li>
                    <li>Sie erhalten eine Buchungsbestätigung per E-Mail</li>
                    </ol>
                    </x-legal.prose>
                </div>

                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Benötige ich ein Benutzerkonto?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    <strong>Nein, ein Benutzerkonto ist nicht erforderlich.</strong> Sie können Veranstaltungen auch ohne Anmeldung buchen. Allerdings bietet ein kostenloser Account mehrere Vorteile:
                    </p>
                    <ul>
                    <li>Alle Ihre Buchungen übersichtlich an einem Ort verwalten</li>
                    <li>Schnellerer Checkout bei zukünftigen Buchungen</li>
                    <li>Automatisches Speichern von Unterlagen und Zertifikaten</li>
                    <li>Benachrichtigungen für neue Veranstaltungen Ihrer Interessensbereiche</li>
                    </ul>
                    <p>
                    Sie können Ihre Gastbuchung auch nachträglich mit einem Konto verknüpfen.
                    </p>
                    </x-legal.prose>
                </div>

                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Wie erfolgt die Bezahlung?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    Die Bezahlung erfolgt über Rechnung, falls die Veranstaltung kostenpflichtig ist. Nach der Buchung erhalten Sie eine Rechnung per E-Mail, die Sie bequem über die angegebene Zahlungsmethode begleichen können.
                    </p>
                    <p>
                    <strong>Wichtig:</strong> Die Tickets oder Buchungsbestätigungen werden versendet, sobald die Zahlung eingegangen ist.
                    </p>
                    <p>
                    Für kostenlose Veranstaltungen entfällt die Bezahlung – Sie erhalten direkt nach der Buchung eine Bestätigung.
                    </p>
                    </x-legal.prose>
                </div>

                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Wie erhalte ich die Tickets oder Zugangsinfos?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    Die Art der Benachrichtigung hängt vom Veranstaltungstyp ab:
                    </p>
                    <div>
                    <div>
                    <h4>Präsenzveranstaltungen:</h4>
                    <p>Sie erhalten Tickets per E-Mail, die Sie ausdrucken oder digital vorzeigen können.</p>
                    </div>
                    <div>
                    <h4>Online-Veranstaltungen:</h4>
                    <p>Sie erhalten den Zugangslink und ggf. Zugangsdaten per E-Mail.</p>
                    </div>
                    <div>
                    <h4>Hybrid-Veranstaltungen:</h4>
                    <p>Sie erhalten Tickets und Zugangsinfos für beide Varianten.</p>
                    </div>
                    </div>
                    <p>
                    <strong>Hinweis:</strong> Sie können alle Ihre Unterlagen jederzeit von Ihrer Buchungsbestätigungsseite herunterladen.
                    </p>
                    </x-legal.prose>
                </div>

                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Kann ich meine Buchung stornieren oder ändern?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    <strong>Änderungen:</strong> Änderungen an Ihren persönlichen Daten (Name, E-Mail, etc.) können Sie nach einer Registrierung selbst vornehmen. Ansonsten kontaktieren Sie bitte den Veranstalter, um Änderungen an Ihrer Buchung zu besprechen.
                    </p>
                    <p>
                    Bei Fragen kontaktieren Sie bitte den Veranstalter unter der angegebenen E-Mail-Adresse.
                    </p>
                    </x-legal.prose>
                </div>

                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Kann ich eine Bestätigung oder ein Zertifikat erhalten?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    Ja! Nach erfolgter Veranstaltung können Sie Ihre Buchungsbestätigung und ggf. ein Teilnahmezertifikat von der Buchungsseite herunterladen.
                    </p>
                    <p>
                    Diese Unterlagen werden vom Veranstalter bereitgestellt und sind im Bereich "Meine Buchungen" verfügbar.
                    </p>
                    <p>
                    Wenn Sie ein Konto haben, sind alle Ihre Unterlagen automatisch gespeichert und jederzeit abrufbar.
                    </p>
                    </x-legal.prose>
                </div>

                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Ich habe meine Buchungsnummer verloren – was kann ich tun?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    Kein Problem! Sie können Ihre Buchungsdetails mit Ihrer E-Mail-Adresse und Veranstaltungsname wiederfinden.
                    </p>
                    <p>
                    Suchen Sie in der E-Mail-Kopie von Ihrer Buchungsbestätigung – dort finden Sie die Buchungsnummer. Falls Sie die E-Mail nicht finden, suchen Sie im Spam-Ordner.
                    </p>
                    <p>
                    Wenn Sie ein Konto haben, können Sie alle Ihre Buchungen im Bereich "Meine Buchungen" einsehen.
                    </p>
                    </x-legal.prose>
                </div>
            </div>

            <!-- Tab Content: Für Veranstalter -->
            <div x-show="activeTab === 'organizers'" x-cloak class="faq-section border-t border-line">
                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Ich möchte eine Veranstaltung veröffentlichen – wie geht das?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <x-ui.alert tone="info" class="mb-4"><strong>Wichtig:</strong> Veranstalter müssen sich direkt an die Betreiber des Portals wenden, um Veranstaltungen zu veröffentlichen.</x-ui.alert>                        <p>
                    Falls Sie eine Veranstaltung organisieren möchten, kontaktieren Sie bitte die Betreiber unter der angegebenen Kontaktadresse. Wir prüfen Ihre Anfrage und unterstützen Sie bei der Einrichtung.
                    </p>
                    <p>
                    Das Portal richtet sich primär an Teilnehmer und Buchende. Für Veranstalter bieten wir spezielle Abläufe und Support.
                    </p>
                    </x-legal.prose>
                </div>
            </div>

            <!-- Tab Content: Technisches -->
            <div x-show="activeTab === 'technical'" x-cloak class="faq-section border-t border-line">
                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Welche Browser werden unterstützt?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    Das Portal funktioniert auf allen modernen Browsern:
                    </p>
                    <ul>
                    <li>Chrome / Chromium (ab Version 90)</li>
                    <li>Firefox (ab Version 88)</li>
                    <li>Safari (ab Version 14)</li>
                    <li>Edge (ab Version 90)</li>
                    </ul>
                    <p>
                    Empfohlen wird die aktuelle Version Ihres Browsers für die beste Kompatibilität und Sicherheit.
                    </p>
                    </x-legal.prose>
                </div>

                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Sind meine Daten auf dem Portal sicher?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    Ja, wir nehmen den Datenschutz ernst:
                    </p>
                    <ul>
                    <li>Alle Datenübertragungen sind verschlüsselt (SSL/TLS)</li>
                    <li>Regelmäßige Sicherheitsupdates und Überprüfungen</li>
                    <li>Compliance mit DSGVO und anderen Datenschutzgesetzen</li>
                    <li>Sicher gehostete Infrastruktur</li>
                    <li>Keine Weitergabe von Daten an Dritte ohne Zustimmung</li>
                    </ul>
                    <p>
                    Weitere Informationen finden Sie in unserer Datenschutzerklärung.
                    </p>
                    </x-legal.prose>
                </div>

                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Wird das Portal auf mobilen Geräten unterstützt?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    Ja! Das Portal ist vollständig responsive und funktioniert auf:
                    </p>
                    <ul>
                    <li>Smartphones (iOS und Android)</li>
                    <li>Tablets (iPad, Android-Tablets)</li>
                    <li>Desktopgeräte und Laptops</li>
                    </ul>
                    <p>
                    Die Benutzeroberfläche passt sich automatisch an Ihr Gerät an. Sie können bequem von überall aus buchen und Ihre Buchungen verwalten.
                    </p>
                    </x-legal.prose>
                </div>

                <div class="faq-item border-b border-line" x-data="{ open: false }">
                    <h2 class="m-0">
                        <button type="button" x-on:click="open = !open" :aria-expanded="open"
                            class="flex min-h-14 w-full cursor-pointer items-center justify-between gap-4 border-0 bg-transparent px-0 py-4 text-left text-base font-semibold text-ink">
                            <span>Ich habe ein technisches Problem – wie erhalte ich Hilfe?</span>
                            <x-icon.chevron-down class="h-5 w-5 flex-none text-muted" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                    </h2>
                    <x-legal.prose x-show="open" x-cloak class="pb-6">
                    <p>
                    Besuchen Sie unser Hilfecenter oder kontaktieren Sie die Betreiber mit folgenden Informationen:
                    </p>
                    <ul>
                    <li>Detaillierte Beschreibung des Problems</li>
                    <li>Name des verwendeten Browsers und Version</li>
                    <li>Gerät (Smartphone, Tablet, Desktop)</li>
                    <li>Zeitpunkt des Fehlers</li>
                    <li>Fehlermeldung (falls vorhanden)</li>
                    </ul>
                    <p>
                    <strong>Tipp:</strong> Versuchen Sie zuerst, den Browser-Cache zu leeren oder einen anderen Browser zu nutzen.
                    </p>
                    </x-legal.prose>
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

