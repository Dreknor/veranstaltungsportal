<x-layouts.app title="Veranstaltung anlegen">
    @php
        $canPublish = auth()->user()->canPublishEvents();
        // Label mit Sternchen, das das Skript je nach Veranstaltungsart ein- und ausblendet.
        $star = fn(string $label, string $class = '') => new \Illuminate\Support\HtmlString(
            e($label) . ' <span class="' . $class . '" aria-hidden="true">*</span>',
        );
    @endphp

    <x-ui.page-header title="Veranstaltung anlegen" :back-href="route('organizer.events.index')" back-label="Zu den Veranstaltungen" />

    @if (!$canPublish)
        <x-ui.alert tone="warning" title="Veröffentlichung nicht möglich" class="mb-8 max-w-form">
            <p class="mt-1">
                Um Veranstaltungen zu veröffentlichen, müssen Sie zunächst Ihre
                @if (!auth()->user()->hasCompleteBillingData() && !auth()->user()->hasCompleteBankAccount())
                    Rechnungsdaten und Bankverbindung
                @elseif(!auth()->user()->hasCompleteBillingData())
                    Rechnungsdaten
                @else
                    Bankverbindung
                @endif
                vervollständigen.
            </p>
            <p class="mt-1">
                Diese Angaben sind notwendig, da bei Buchungen automatisch Rechnungen an Teilnehmende versendet werden.
            </p>
            <p class="mt-2 flex flex-wrap gap-x-6 gap-y-1">
                @if (!auth()->user()->hasCompleteBillingData())
                    <a href="{{ route('organizer.bank-account.billing-data') }}">Rechnungsdaten vervollständigen</a>
                @endif
                @if (!auth()->user()->hasCompleteBankAccount())
                    <a href="{{ route('organizer.bank-account.index') }}">Bankverbindung vervollständigen</a>
                @endif
            </p>
        </x-ui.alert>
    @endif

    <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between lg:gap-x-12">
        <form id="event-create-form" method="POST" action="{{ route('organizer.events.store') }}"
            enctype="multipart/form-data" class="m-0 min-w-0">
            @csrf

            {{-- 1 Grunddaten --}}
            <section aria-labelledby="schritt-1" class="border-t border-line py-8">
                <x-ui.step-heading number="1" id="schritt-1" aside="* Pflichtfeld" class="mb-6">
                    Grunddaten
                </x-ui.step-heading>

                <div class="flex flex-col gap-y-5">
                    <x-ui.field label="Titel" name="title" required />

                    <x-ui.select label="Kategorie" name="event_category_id" required placeholder="Bitte wählen"
                        :options="$categories->pluck('name', 'id')" />

                    <x-ui.select label="Veranstaltungsart" name="event_type" required selected="physical"
                        :options="[
                            'physical' => 'Präsenzveranstaltung',
                            'online' => 'Online-Veranstaltung',
                            'hybrid' => 'Hybrid (Präsenz & Online)',
                            'external' => 'Externe Veranstaltung',
                        ]" />

                    <x-ui.markdown-editor label="Beschreibung" name="description" required />

                    <x-ui.field label="Titelbild" name="featured_image" type="file" accept="image/*" optional />
                </div>
            </section>

            {{-- 2 Datum & Zeit --}}
            <section aria-labelledby="schritt-2" class="border-t border-line py-8">
                <x-ui.step-heading number="2" id="schritt-2" class="mb-6">Datum &amp; Zeit</x-ui.step-heading>

                <div class="flex flex-col gap-y-5">
                    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.field label="Beginn" name="start_date" type="datetime-local" required />
                        <x-ui.field label="Ende" name="end_date" type="datetime-local" required />
                    </div>

                    <div>
                        {{-- Hidden field ensures false value is sent when checkbox is unchecked --}}
                        <input type="hidden" name="has_multiple_dates" value="0">
                        <x-ui.checkbox name="has_multiple_dates" id="has_multiple_dates"
                            label="Veranstaltung hat mehrere Termine"
                            hint="Aktivieren Sie diese Option, wenn die Veranstaltung aus mehreren aufeinanderfolgenden Terminen besteht (z. B. ein 8-Wochen-Kurs). Teilnehmende buchen dann automatisch alle Termine gemeinsam." />

                        <div id="multiple-dates-info" class="mt-3"
                            style="display: {{ old('has_multiple_dates') ? 'block' : 'none' }}">
                            <x-ui.alert tone="info" title="Mehrere Termine aktiviert">
                                <p class="mt-1">
                                    Das oben angegebene Start- und Enddatum wird als <strong
                                        class="font-semibold">erster
                                        Termin</strong> gespeichert. Nach dem Anlegen der Veranstaltung können Sie auf
                                    der
                                    Bearbeitungsseite weitere Termine hinzufügen.
                                </p>
                                <ul class="mt-2">
                                    <li>Alle Termine werden gemeinsam gebucht</li>
                                    <li>Die Kapazität gilt für die gesamte Serie</li>
                                    <li>Sie können jeden Termin einzeln absagen</li>
                                </ul>
                                <p class="mt-2">
                                    <strong class="font-semibold">So geht’s weiter:</strong> Klicken Sie unten auf
                                    „Veranstaltung anlegen“. Auf der nächsten Seite können Sie dann weitere Termine
                                    hinzufügen.
                                </p>
                            </x-ui.alert>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 3 Ort und Zugang: Veranstaltungsort (Präsenz, Hybrid), Online-Zugang (Online, Hybrid), externe Buchung --}}
            <section aria-labelledby="schritt-3" class="border-t border-line py-8">
                <x-ui.step-heading number="3" id="schritt-3" class="mb-6">Ort und Zugang</x-ui.step-heading>

                <div class="flex flex-col gap-8">
                    {{-- Veranstaltungsort (Nur für Präsenz & Hybrid) --}}
                    <div id="venue-section">
                        <h3 class="m-0 mb-4 text-h3 text-ink">Veranstaltungsort</h3>
                        <div class="flex flex-col gap-y-5">
                            <x-ui.field :label="$star('Name', 'venue-required')" name="venue_name" />
                            <x-ui.field :label="$star('Adresse', 'venue-required')" name="venue_address" />
                            <div class="flex flex-wrap gap-x-4 gap-y-5">
                                <x-ui.field :label="$star('PLZ', 'venue-required')" name="venue_postal_code" inputmode="numeric"
                                    class="max-w-40 flex-[1_1_112px]" />
                                <x-ui.field :label="$star('Ort', 'venue-required')" name="venue_city" class="min-w-0 flex-[3_1_200px]" />
                            </div>
                            <x-ui.field :label="$star('Land', 'venue-required')" name="venue_country" value="Deutschland" />
                            <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-x-4 gap-y-5">
                                <x-ui.field label="Breitengrad" name="venue_latitude" type="number" step="0.0000001"
                                    optional />
                                <x-ui.field label="Längengrad" name="venue_longitude" type="number" step="0.0000001"
                                    optional />
                            </div>
                            <x-ui.textarea label="Anfahrtsbeschreibung" name="directions" rows="3" optional />
                        </div>
                    </div>

                    {{-- Online-Zugang (Nur für Online & Hybrid) --}}
                    <div id="online-section" style="display: none;">
                        <h3 class="m-0 mb-4 text-h3 text-ink">Online-Zugang</h3>
                        <div class="flex flex-col gap-y-5">
                            <x-ui.field :label="$star('Meeting- bzw. Konferenz-URL', 'online-required')" name="online_url" type="url"
                                placeholder="https://zoom.us/j/123456789 oder https://meet.google.com/..."
                                hint="Sichtbar nur für bestätigte und bezahlte Buchungen: in der Bestätigungs-E-Mail, auf der Buchungsseite und in den Erinnerungen 24 h und 3 h vor Beginn." />
                            <x-ui.field label="Zugangs- bzw. Meeting-Code" name="online_access_code" optional
                                placeholder="z. B. Meeting-ID, Passwort"
                                hint="Falls ein separater Zugangscode für die Online-Veranstaltung benötigt wird." />
                        </div>
                    </div>

                    {{-- Externe Buchung (Nur für externe Veranstaltungen) --}}
                    <div id="external-section" style="display: none;">
                        <h3 class="m-0 mb-4 text-h3 text-ink">Externe Buchungsseite</h3>
                        <div class="flex flex-col gap-y-5">
                            <x-ui.alert tone="info">
                                Bei externen Veranstaltungen werden keine Tickets über diese Plattform verkauft.
                                Besucher werden stattdessen zur angegebenen externen Buchungsseite weitergeleitet.
                            </x-ui.alert>
                            <x-ui.field :label="$star('Externe Buchungs-URL')" name="external_booking_url" type="url"
                                placeholder="https://www.beispiel.de/anmeldung"
                                hint="Die URL, zu der Besucher für die Buchung/Anmeldung weitergeleitet werden." />
                            <x-ui.field label="Button-Text" name="external_booking_button_text" optional
                                placeholder="Zur Anmeldung"
                                hint="Text, der auf dem Weiterleitungs-Button angezeigt wird. Standard: „Zur Anmeldung“" />
                        </div>
                    </div>
                </div>
            </section>

            {{-- 4 Zusatzinformationen --}}
            <section aria-labelledby="schritt-4" class="border-t border-line py-8">
                <x-ui.step-heading number="4" id="schritt-4"
                    class="mb-6">Zusatzinformationen</x-ui.step-heading>

                <div class="flex flex-col gap-y-5">
                    <div id="pricing-section"
                        class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.field label="Preis ab (€)" name="price_from" type="number" step="0.01" optional />
                        <x-ui.field label="Max. Teilnehmende" name="max_attendees" type="number" optional />
                    </div>

                    <div>
                        <x-ui.checkbox name="is_published" label="Veranstaltung veröffentlichen" :disabled="!$canPublish"
                            :hint="$canPublish ? null : 'Rechnungsdaten erforderlich'" />

                        <x-ui.checkbox name="is_featured" id="is_featured" label="Veranstaltung hervorheben" />

                        <input type="hidden" id="featured_duration_type" name="featured_duration_type"
                            value="">
                        <input type="hidden" id="featured_custom_days" name="featured_custom_days" value="">
                        <input type="hidden" id="featured_start_date" name="featured_start_date" value="">

                        <x-ui.checkbox name="is_private" id="is_private"
                            label="Private Veranstaltung (Zugangscode erforderlich)" />
                    </div>

                    <div id="access_code_field" style="display: {{ old('is_private') ? 'block' : 'none' }}">
                        <x-ui.field label="Zugangscode" name="access_code" />
                    </div>
                </div>
            </section>

            {{-- 5 Ticket-Einstellungen --}}
            <section id="ticket-settings-section" aria-labelledby="schritt-5" class="border-t border-line py-8">
                <x-ui.step-heading number="5" id="schritt-5"
                    class="mb-6">Ticket-Einstellungen</x-ui.step-heading>

                <div class="flex flex-col gap-y-5">
                    <x-ui.textarea label="Hinweise auf dem Ticket" name="ticket_notes" rows="4" optional
                        placeholder="z. B. Bitte Personalausweis mitbringen, Einlass ab 18:30 Uhr, etc."
                        hint="Diese Hinweise werden auf allen Tickets dieser Veranstaltung angezeigt. Höchstens 1000 Zeichen." />

                    <div>
                        <input type="hidden" name="requires_ticket" value="0">
                        <x-ui.checkbox name="requires_ticket" id="requires_ticket" :checked="true"
                            label="Ticket-Versand erforderlich"
                            hint="Wenn deaktiviert, werden keine Tickets per E-Mail versendet. Teilnehmende erhalten nur eine Buchungsbestätigung mit Link zur Buchungsseite." />

                        <div id="qr_code_option">
                            <input type="hidden" name="show_qr_code_on_ticket" value="0">
                            <x-ui.checkbox name="show_qr_code_on_ticket" :checked="true"
                                label="QR-Code auf Tickets anzeigen"
                                hint="Wenn aktiviert, wird auf jedem Ticket ein QR-Code für den Check-in angezeigt." />
                        </div>
                    </div>

                    {{-- Stornierungsrichtlinie --}}
                    <div class="flex flex-col gap-y-5 border-t border-line pt-6">
                        <h3 class="m-0 text-h3 text-ink">Stornierungsrichtlinie</h3>

                        <div>
                            <input type="hidden" name="cancellation_allowed" value="0">
                            <x-ui.checkbox name="cancellation_allowed" id="cancellation_allowed_create"
                                label="Stornierung durch Teilnehmende erlauben"
                                hint="Wenn aktiviert, können Teilnehmende ihre Buchung selbst stornieren." />

                            <div id="cancellation_days_field_create"
                                class="{{ old('cancellation_allowed') ? '' : 'hidden' }} mt-3 pl-8">
                                <x-ui.field label="Stornierungsfrist (Tage vor Veranstaltungsbeginn)"
                                    name="cancellation_days_before" id="cancellation_days_before_create"
                                    type="number" min="0" placeholder="z. B. 3" input-class="max-w-32"
                                    hint="Stornierung muss mindestens diese Anzahl an Tagen vor Beginn erfolgen. Leer lassen = bis Veranstaltungsbeginn." />
                            </div>
                        </div>

                        {{-- Organisationsfeld --}}
                        <x-ui.select label="Organisationsfeld bei Buchung und Personalisierung"
                            name="organization_field_mode" selected="none" :options="[
                                'none' => 'Nicht anzeigen',
                                'optional' => 'Optional anzeigen',
                                'required' => 'Pflichtfeld',
                            ]"
                            hint="Legt fest, ob Teilnehmende bei der Buchung und Personalisierung ihre Organisation bzw. Einrichtung angeben müssen." />

                        {{-- Bestätigungsmodus für kostenfreie Anmeldungen --}}
                        <fieldset class="m-0 min-w-0 border-0 p-0">
                            <x-ui.label as="legend">Bestätigungsmodus für kostenfreie Anmeldungen</x-ui.label>
                            <x-ui.radio name="free_ticket_auto_confirm" value="1" :checked="true"
                                label="Automatisch bestätigen"
                                hint="Anmeldungen werden sofort bestätigt. Zugangsdaten und Tickets werden automatisch versendet." />
                            <x-ui.radio name="free_ticket_auto_confirm" value="0"
                                label="Manuelle Bestätigung erforderlich"
                                hint="Anmeldungen müssen erst durch den Veranstalter freigegeben werden. Zugangsdaten werden erst nach Bestätigung versendet." />
                        </fieldset>

                        @include('organizer.events.partials.invoice-ticket-mode', [
                            'organization' => $organization,
                            'value' => old('tickets_before_invoice') === '1',
                        ])
                    </div>
                </div>
            </section>

            <div class="flex flex-wrap gap-3 border-t border-line pt-8">
                <x-ui.button type="submit">Veranstaltung anlegen</x-ui.button>
                <x-ui.button variant="secondary" :href="route('organizer.events.index')">Abbrechen</x-ui.button>
            </div>
        </form>

        @if (isset($publishingCosts))
            <aside aria-labelledby="kosten-titel" class="min-w-0 lg:sticky lg:top-24">
                <x-ui.box tone="zone" id="cost-overview">
                    <h2 id="kosten-titel" class="m-0 mb-3 text-h3 text-ink">Geschätzte Kosten bei Veröffentlichung
                    </h2>
                    <div class="text-small text-ink" id="cost-breakdown-content">
                        <p class="m-0">Die Kosten werden nach Ende der Veranstaltung basierend auf den tatsächlichen
                            Buchungen abgerechnet.</p>
                        <p class="mt-3 mb-0 text-muted">
                            <strong class="font-semibold text-ink">Hinweis:</strong> Wenn Sie „Veranstaltung
                            hervorheben“ wählen, können Sie direkt den Zeitraum buchen.
                        </p>
                    </div>
                </x-ui.box>
            </aside>
        @endif
    </div>

    @push('scripts')
        <script>
            // Toggle Access Code field
            document.getElementById('is_private').addEventListener('change', function() {
                document.getElementById('access_code_field').style.display = this.checked ? 'block' : 'none';
            });

            // Toggle QR Code option based on requires_ticket
            const requiresTicketCheckbox = document.getElementById('requires_ticket');
            const qrCodeOption = document.getElementById('qr_code_option');

            if (requiresTicketCheckbox && qrCodeOption) {
                // Initial state
                qrCodeOption.style.display = requiresTicketCheckbox.checked ? 'block' : 'none';

                requiresTicketCheckbox.addEventListener('change', function() {
                    qrCodeOption.style.display = this.checked ? 'block' : 'none';
                });
            }

            // Toggle Multiple Dates Info Box
            const multipleDatesCheckbox = document.getElementById('has_multiple_dates');
            const multipleDatesInfo = document.getElementById('multiple-dates-info');

            if (multipleDatesCheckbox && multipleDatesInfo) {
                multipleDatesCheckbox.addEventListener('change', function() {
                    multipleDatesInfo.style.display = this.checked ? 'block' : 'none';
                });
            }

            // Toggle Venue/Online/External sections based on event type
            const eventTypeSelect = document.getElementById('event_type');
            const venueSection = document.getElementById('venue-section');
            const onlineSection = document.getElementById('online-section');
            const externalSection = document.getElementById('external-section');
            const pricingSection = document.getElementById('pricing-section');
            const ticketSettingsSection = document.getElementById('ticket-settings-section');

            function updateSections() {
                const eventType = eventTypeSelect.value;
                const isExternal = eventType === 'external';

                // Venue section
                if (eventType === 'physical' || eventType === 'hybrid') {
                    venueSection.style.display = 'block';
                    setVenueFieldsRequired(true);
                } else {
                    venueSection.style.display = 'none';
                    setVenueFieldsRequired(false);
                }

                // Online section
                if (eventType === 'online' || eventType === 'hybrid') {
                    onlineSection.style.display = 'block';
                    setOnlineFieldsRequired(true);
                } else {
                    onlineSection.style.display = 'none';
                    setOnlineFieldsRequired(false);
                }

                // External section
                externalSection.style.display = isExternal ? 'block' : 'none';
                const externalUrl = document.getElementById('external_booking_url');
                if (externalUrl) {
                    externalUrl.required = isExternal;
                }

                // Hide pricing & ticket settings for external events
                pricingSection.style.display = isExternal ? 'none' : '';
                ticketSettingsSection.style.display = isExternal ? 'none' : '';
            }

            function setVenueFieldsRequired(required) {
                ['venue_name', 'venue_address', 'venue_city', 'venue_postal_code', 'venue_country'].forEach(id => {
                    const field = document.getElementById(id);
                    if (field) {
                        field.required = required;
                    }
                });
                document.querySelectorAll('.venue-required').forEach(el => {
                    el.style.display = required ? 'inline' : 'none';
                });
            }

            function setOnlineFieldsRequired(required) {
                const onlineUrl = document.getElementById('online_url');
                if (onlineUrl) {
                    onlineUrl.required = required;
                }
                document.querySelectorAll('.online-required').forEach(el => {
                    el.style.display = required ? 'inline' : 'none';
                });
            }

            eventTypeSelect.addEventListener('change', updateSections);

            // Initialize on page load
            updateSections();

            // Featured Event Handling
            const featuredCheckbox = document.getElementById('is_featured');

            if (featuredCheckbox) {
                featuredCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Open modal for booking
                        window.dispatchEvent(new CustomEvent('featured-modal-open'));
                    } else {
                        // Clear hidden fields
                        document.getElementById('featured_duration_type').value = '';
                        document.getElementById('featured_custom_days').value = '';
                        document.getElementById('featured_start_date').value = '';
                    }
                });
            }

            // Listen for featured booking confirmation
            window.addEventListener('featured-booking-confirm', function(e) {
                const {
                    durationType,
                    customDays,
                    startDate
                } = e.detail;

                // Store in hidden fields
                document.getElementById('featured_duration_type').value = durationType;
                document.getElementById('featured_custom_days').value = customDays;
                document.getElementById('featured_start_date').value = startDate;

                // Update cost overview
                updateCostEstimateForFeatured(durationType, customDays);
            });

            function updateCostEstimateForFeatured(durationType, customDays) {
                const rates = {
                    'daily': {{ config('monetization.featured_event_rates.daily', 5.0) }},
                    'weekly': {{ config('monetization.featured_event_rates.weekly', 25.0) }},
                    'monthly': {{ config('monetization.featured_event_rates.monthly', 80.0) }},
                    'custom': customDays * {{ config('monetization.featured_event_rates.daily', 5.0) }}
                };

                const amount = rates[durationType];
                const vatAmount = amount * 0.19;
                const totalAmount = amount * 1.19;

                const durationLabels = {
                    'daily': '1 Tag',
                    'weekly': '7 Tage',
                    'monthly': '30 Tage',
                    'custom': customDays + ' Tage'
                };

                const content = document.getElementById('cost-breakdown-content');
                if (content) {
                    content.innerHTML = `
                        <div class="flex flex-col gap-3">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="font-medium">Gebühr für die Hervorhebung (geschätzt)</div>
                                    <div class="text-muted">Standardpreis für ${durationLabels[durationType]} Hervorhebung</div>
                                    <div class="text-muted">Wird beim Anlegen gebucht</div>
                                </div>
                                <div class="font-semibold tabular-nums whitespace-nowrap">${formatCurrency(amount)}</div>
                            </div>
                            <div class="border-t border-line pt-3">
                                <div class="flex items-baseline justify-between gap-4">
                                    <div class="font-semibold">Geschätzte Gesamtkosten (netto)</div>
                                    <div class="text-h3 tabular-nums whitespace-nowrap">${formatCurrency(amount)}</div>
                                </div>
                                <div class="mt-1 text-muted tabular-nums">
                                    + ${formatCurrency(vatAmount)} MwSt. (19 %) = ${formatCurrency(totalAmount)} brutto
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-muted">
                            <strong class="font-semibold text-ink">Hinweis:</strong> Die Gebühr für die Hervorhebung wird nach dem Anlegen der Veranstaltung zur Zahlung fällig.
                            Plattformgebühren für Buchungen werden nach Ende der Veranstaltung abgerechnet.
                        </p>
                    `;
                }
            }

            function formatCurrency(amount) {
                return new Intl.NumberFormat('de-DE', {
                    style: 'currency',
                    currency: 'EUR'
                }).format(amount);
            }

            // Toggle cancellation days field
            const cancellationCheckboxCreate = document.getElementById('cancellation_allowed_create');
            const cancellationDaysFieldCreate = document.getElementById('cancellation_days_field_create');
            if (cancellationCheckboxCreate && cancellationDaysFieldCreate) {
                cancellationCheckboxCreate.addEventListener('change', function() {
                    cancellationDaysFieldCreate.classList.toggle('hidden', !this.checked);
                });
            }
        </script>
    @endpush

    {{-- Dialog „Veranstaltung hervorheben“ --}}
    <x-featured-booking-modal :event="new \App\Models\Event()" />
</x-layouts.app>
