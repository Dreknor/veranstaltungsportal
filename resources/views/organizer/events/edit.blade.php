<x-layouts.app title="Veranstaltung bearbeiten">
    @php
        $cannotPublish = !auth()->user()->canPublishEvents() && !$event->is_published;
    @endphp

    <x-ui.page-header title="Veranstaltung bearbeiten" :subtitle="$event->title" :back-href="route('organizer.events.index')"
        back-label="Zu den Veranstaltungen">
        <x-slot:meta>
            @if ($event->is_cancelled)
                <x-ui.status tone="danger">Abgesagt</x-ui.status>
            @elseif ($event->is_published)
                <x-ui.status tone="success">Veröffentlicht</x-ui.status>
            @else
                <x-ui.status tone="neutral">Entwurf</x-ui.status>
            @endif
        </x-slot:meta>
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('events.show', $event->slug)" target="_blank" rel="noopener">
                <x-slot:icon><x-icon.external-link /></x-slot:icon>
                {{ $event->is_published ? 'Öffentlich ansehen' : 'Vorschau' }}
            </x-ui.button>
            <x-ui.button type="submit" form="event-form">Änderungen speichern</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($event->is_cancelled || !auth()->user()->canPublishEvents() || session('pending_featured_fee_id'))
        <div class="mb-8 flex flex-col gap-4">
            @if ($event->is_cancelled)
                <x-ui.alert tone="danger" title="Diese Veranstaltung wurde abgesagt">
                    <p class="tabular-nums">Abgesagt am {{ $event->cancelled_at->format('d.m.Y, H:i') }} Uhr</p>
                    @if ($event->cancellation_reason)
                        <p><span class="font-medium">Grund:</span> {{ $event->cancellation_reason }}</p>
                    @endif
                </x-ui.alert>
            @endif

            @if (!auth()->user()->canPublishEvents())
                <x-ui.alert tone="warning" title="Veröffentlichung nicht möglich">
                    <p>
                        Um Veranstaltungen zu veröffentlichen, müssen Sie zunächst Ihre
                        @if (!auth()->user()->hasCompleteBillingData() && !auth()->user()->hasCompleteBankAccount())
                            Rechnungsdaten und Bankverbindung
                        @elseif(!auth()->user()->hasCompleteBillingData())
                            Rechnungsdaten
                        @else
                            Bankverbindung
                        @endif
                        vervollständigen.
                        Diese Angaben sind notwendig, da bei Buchungen automatisch Rechnungen an Teilnehmende versendet
                        werden.
                    </p>
                    <p class="mt-2 flex flex-wrap gap-x-6 gap-y-2">
                        @if (!auth()->user()->hasCompleteBillingData())
                            <a href="{{ route('organizer.bank-account.billing-data') }}">Rechnungsdaten
                                vervollständigen</a>
                        @endif
                        @if (!auth()->user()->hasCompleteBankAccount())
                            <a href="{{ route('organizer.bank-account.index') }}">Bankverbindung vervollständigen</a>
                        @endif
                    </p>
                </x-ui.alert>
            @endif

            @if (session('pending_featured_fee_id'))
                <x-ui.alert tone="info" title="Hervorhebung noch nicht bezahlt">
                    <p>Die Gebühr für die Hervorhebung muss vor der Veröffentlichung bezahlt werden.</p>
                    <p class="mt-2">
                        <a href="{{ route('organizer.featured-events.payment', session('pending_featured_fee_id')) }}">Gebühr
                            für die Hervorhebung bezahlen</a>
                    </p>
                </x-ui.alert>
            @endif
        </div>
    @endif

    <div class="grid items-start gap-x-12 gap-y-8 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between">
        {{-- Linke Spalte: Hauptformular --}}
        <form id="event-form" method="POST" action="{{ route('organizer.events.update', $event) }}"
            enctype="multipart/form-data" class="min-w-0">
            @csrf
            @method('PUT')

            {{-- 1 Grunddaten --}}
            <section aria-labelledby="step-grunddaten" class="border-t border-line py-8">
                <x-ui.step-heading number="1" id="step-grunddaten" aside="* Pflichtfeld"
                    class="mb-6">Grunddaten</x-ui.step-heading>

                <div class="flex flex-col gap-y-5">
                    <x-ui.field label="Titel" name="title" :value="$event->title" required />

                    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.select label="Kategorie" name="event_category_id" :options="$categories->pluck('name', 'id')" :selected="$event->event_category_id"
                            required />

                        <x-ui.select label="Veranstaltungsart" name="event_type" :options="[
                            'physical' => 'Präsenzveranstaltung',
                            'online' => 'Online-Veranstaltung',
                            'hybrid' => 'Hybrid (Präsenz und Online)',
                            'external' => 'Externe Veranstaltung',
                        ]" :selected="$event->event_type ?? 'physical'"
                            required />
                    </div>

                    <x-ui.markdown-editor label="Beschreibung" name="description" :value="$event->description" required />

                    <div class="flex flex-col gap-3">
                        <x-ui.label as="span" class="mb-0">Titelbild</x-ui.label>

                        @if ($event->featured_image)
                            <div id="current-image-section" class="flex flex-wrap items-start gap-4">
                                <img src="{{ Storage::url($event->featured_image) }}" alt="Aktuelles Titelbild"
                                    class="h-40 w-auto max-w-full rounded-box border border-line object-cover">
                                <div class="min-w-0 flex-1 basis-60">
                                    <p class="m-0 text-small text-muted">
                                        Laden Sie ein neues Bild hoch, um das vorhandene zu ersetzen.
                                        Oder entfernen Sie das Bild, ohne ein neues hochzuladen.
                                    </p>
                                    <x-ui.checkbox name="delete_featured_image" id="delete_featured_image"
                                        :old="false" label="Bild entfernen"
                                        onchange="document.getElementById('current-image-section').classList.toggle('opacity-50', this.checked);" />
                                </div>
                            </div>
                        @else
                            <p class="m-0 text-small text-muted">Noch kein Titelbild vorhanden.</p>
                        @endif

                        <x-ui.field type="file" name="featured_image" accept="image/*" optional :label="$event->featured_image
                            ? 'Neues Bild hochladen (ersetzt das vorhandene)'
                            : 'Bild hochladen'"
                            hint="Erlaubte Formate: JPG, PNG, GIF, WEBP – höchstens 2 MB" />

                        <div id="image-preview-wrapper" class="hidden">
                            <p class="m-0 mb-1.5 text-small text-muted">Vorschau</p>
                            <img id="image-preview" src="#" alt="Vorschau"
                                class="h-32 w-auto max-w-full rounded-box border border-line">
                        </div>
                    </div>
                </div>
            </section>

            {{-- 2 Termin --}}
            <section aria-labelledby="step-termin" class="border-t border-line py-8">
                <x-ui.step-heading number="2" id="step-termin" class="mb-6">Datum &amp; Zeit</x-ui.step-heading>

                <div class="flex flex-col gap-y-5">
                    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.field label="Beginn" name="start_date" type="datetime-local" required
                            :value="$event->start_date->format('Y-m-d\TH:i')" />
                        <x-ui.field label="Ende" name="end_date" type="datetime-local" required :value="$event->end_date->format('Y-m-d\TH:i')" />
                    </div>

                    <div>
                        <x-ui.checkbox id="has_multiple_dates" :checked="$event->has_multiple_dates" data-event-id="{{ $event->id }}"
                            label="Veranstaltung hat mehrere Termine"
                            hint="Aktivieren Sie diese Option, wenn die Veranstaltung aus mehreren aufeinanderfolgenden Terminen besteht (z. B. ein 8-Wochen-Kurs). Teilnehmende buchen dann automatisch alle Termine gemeinsam." />

                        <div id="multiple-dates-info" class="mt-3"
                            style="display: {{ old('has_multiple_dates', $event->has_multiple_dates) ? 'block' : 'none' }}">
                            <x-ui.alert tone="info" title="Mehrere Termine aktiviert">
                                <p>
                                    @if ($event->has_multiple_dates && $event->dates->count() > 0)
                                        Diese Veranstaltung hat {{ $event->dates->count() }} Termine. Unter „Termine“
                                        können Sie weitere Termine hinzufügen oder bestehende verwalten.
                                    @elseif($event->has_multiple_dates)
                                        <strong class="font-semibold">Speichern Sie die Veranstaltung</strong>, um auf
                                        die
                                        Terminverwaltung zuzugreifen und weitere Termine hinzuzufügen.
                                        Beginn und Ende oben werden als erster Termin gespeichert.
                                    @else
                                        <strong class="font-semibold">Wichtig:</strong> Speichern Sie die Veranstaltung
                                        nach dem Aktivieren dieser Option. Danach erscheint die Terminverwaltung, mit
                                        der
                                        Sie weitere Termine hinzufügen können.
                                    @endif
                                </p>
                                <ul class="mt-2">
                                    <li>Alle Termine werden gemeinsam gebucht</li>
                                    <li>Die Kapazität gilt für die gesamte Serie</li>
                                    <li>Sie können jeden Termin einzeln absagen</li>
                                </ul>
                                @if (!$event->has_multiple_dates || $event->dates->count() == 0)
                                    <p class="mt-2">
                                        <strong class="font-semibold">Nächster Schritt:</strong> Klicken Sie unten auf
                                        „Änderungen speichern“, um die Terminverwaltung freizuschalten.
                                    </p>
                                @endif
                            </x-ui.alert>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 3 Ort und Zugang: Abschnitte werden je nach Veranstaltungsart per Skript ein- und ausgeblendet --}}
            <section aria-labelledby="step-ort" class="border-t border-line py-8">
                <x-ui.step-heading number="3" id="step-ort" class="mb-6">Ort und Zugang</x-ui.step-heading>

                <div class="flex flex-col gap-8">
                    {{-- Veranstaltungsort (nur Präsenz und Hybrid) --}}
                    <div id="venue-section">
                        <h3 class="m-0 mb-4 text-h3 text-ink">Veranstaltungsort</h3>

                        <div class="flex flex-col gap-y-5">
                            <div>
                                <x-ui.label for="venue_name">Name <span class="venue-required"
                                        aria-hidden="true">*</span></x-ui.label>
                                <x-ui.field name="venue_name" :value="$event->venue_name" />
                            </div>

                            <div>
                                <x-ui.label for="venue_address">Adresse <span class="venue-required"
                                        aria-hidden="true">*</span></x-ui.label>
                                <x-ui.field name="venue_address" :value="$event->venue_address" />
                            </div>

                            <div class="flex flex-wrap gap-x-4 gap-y-5">
                                <div class="max-w-40 flex-[1_1_112px]">
                                    <x-ui.label for="venue_postal_code">PLZ <span class="venue-required"
                                            aria-hidden="true">*</span></x-ui.label>
                                    <x-ui.field name="venue_postal_code" :value="$event->venue_postal_code" inputmode="numeric" />
                                </div>
                                <div class="min-w-0 flex-[3_1_200px]">
                                    <x-ui.label for="venue_city">Ort <span class="venue-required"
                                            aria-hidden="true">*</span></x-ui.label>
                                    <x-ui.field name="venue_city" :value="$event->venue_city" />
                                </div>
                            </div>

                            <div>
                                <x-ui.label for="venue_country">Land <span class="venue-required"
                                        aria-hidden="true">*</span></x-ui.label>
                                <x-ui.field name="venue_country" :value="\App\Support\Ui::country($event->venue_country)" />
                            </div>

                            <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-x-4 gap-y-5">
                                <x-ui.field label="Breitengrad" name="venue_latitude" type="number"
                                    step="0.0000001" :value="$event->venue_latitude" optional />
                                <x-ui.field label="Längengrad" name="venue_longitude" type="number"
                                    step="0.0000001" :value="$event->venue_longitude" optional />
                            </div>

                            <x-ui.textarea label="Anfahrtsbeschreibung" name="directions" :value="$event->directions" optional
                                rows="3" />
                        </div>
                    </div>

                    {{-- Online-Zugang (nur Online und Hybrid) --}}
                    <div id="online-section">
                        <h3 class="m-0 mb-4 text-h3 text-ink">Online-Zugang</h3>

                        <div class="flex flex-col gap-y-5">
                            <div>
                                <x-ui.label for="online_url">Meeting- bzw. Konferenz-URL <span class="online-required"
                                        aria-hidden="true">*</span></x-ui.label>
                                <x-ui.field name="online_url" type="url" :value="$event->online_url"
                                    placeholder="https://zoom.us/j/123456789 oder https://meet.google.com/..."
                                    hint="Sichtbar nur für bestätigte und bezahlte Buchungen: in der Bestätigungs-E-Mail, auf der Buchungsseite und in den Erinnerungen 24 h und 3 h vor Beginn. Wenn Sie den Link oder Code ändern, erhalten alle Teilnehmenden automatisch die neuen Zugangsdaten." />
                            </div>

                            <x-ui.field label="Zugangs- bzw. Meeting-Code" name="online_access_code" optional
                                :value="$event->online_access_code" placeholder="z. B. Meeting-ID, Passwort"
                                hint="Falls ein separater Zugangscode für die Online-Veranstaltung benötigt wird." />
                        </div>
                    </div>

                    {{-- Externe Buchung (nur externe Veranstaltungen) --}}
                    <div id="external-section"
                        style="display: {{ old('event_type', $event->event_type) === 'external' ? 'block' : 'none' }};">
                        <h3 class="m-0 mb-4 text-h3 text-ink">Externe Buchungsseite</h3>

                        <div class="flex flex-col gap-y-5">
                            <x-ui.alert tone="info">
                                Bei externen Veranstaltungen werden keine Tickets über diese Plattform verkauft.
                                Besucher werden stattdessen zur angegebenen externen Buchungsseite weitergeleitet.
                            </x-ui.alert>

                            <div>
                                <x-ui.label for="external_booking_url">Externe Buchungs-URL <span
                                        aria-hidden="true">*</span></x-ui.label>
                                <x-ui.field name="external_booking_url" type="url" :value="$event->external_booking_url"
                                    placeholder="https://www.beispiel.de/anmeldung"
                                    hint="Die URL, zu der Besucher für die Buchung bzw. Anmeldung weitergeleitet werden." />
                            </div>

                            <x-ui.field label="Button-Text" name="external_booking_button_text" optional
                                :value="$event->external_booking_button_text" placeholder="Zur Anmeldung"
                                hint="Text, der auf dem Weiterleitungs-Button angezeigt wird. Standard: „Zur Anmeldung“" />
                        </div>
                    </div>
                </div>
            </section>

            {{-- 4 Zusatzinformationen --}}
            <section aria-labelledby="step-zusatz" class="border-t border-line py-8">
                <x-ui.step-heading number="4" id="step-zusatz"
                    class="mb-6">Zusatzinformationen</x-ui.step-heading>

                <div class="flex flex-col gap-y-5">
                    <div id="pricing-section"
                        class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.field label="Preis ab (€)" name="price_from" type="number" step="0.01" optional
                            :value="$event->price_from" />
                        <x-ui.field label="Max. Teilnehmende" name="max_attendees" type="number" optional
                            :value="$event->max_attendees" />
                    </div>

                    @if (isset($publishingCosts) && ($publishingCosts['total'] > 0 || !$event->is_published))
                        <div id="cost-overview" class="rounded-box border border-line bg-zone p-4">
                            <h3 class="m-0 text-base font-semibold text-ink">Geschätzte Kosten bei Veröffentlichung
                            </h3>
                            <div data-cost-content class="mt-3 text-small text-ink">
                                @if (count($publishingCosts['breakdown']) > 0)
                                    <div class="flex flex-col gap-2">
                                        @foreach ($publishingCosts['breakdown'] as $item)
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="min-w-0">
                                                    <div class="font-medium">{{ $item['label'] }}</div>
                                                    @if (isset($item['description']))
                                                        <div class="text-muted">{{ $item['description'] }}</div>
                                                    @endif
                                                    @if (isset($item['status']) && $item['status'] === 'pending')
                                                        <x-ui.status tone="warning">Zahlung offen</x-ui.status>
                                                    @endif
                                                </div>
                                                <div class="font-medium whitespace-nowrap tabular-nums">
                                                    {{ \App\Support\Ui::money($item['amount']) }}
                                                </div>
                                            </div>
                                        @endforeach

                                        <div class="mt-2 border-t border-line pt-2">
                                            <div class="flex items-baseline justify-between gap-4 font-semibold">
                                                <span>Geschätzte Gesamtkosten (netto)</span>
                                                <span class="text-h3 whitespace-nowrap tabular-nums">
                                                    {{ \App\Support\Ui::money($publishingCosts['total']) }}
                                                </span>
                                            </div>
                                            <div class="mt-1 text-muted tabular-nums">
                                                + {{ \App\Support\Ui::money($publishingCosts['total'] * 0.19) }} MwSt.
                                                (19 %) =
                                                {{ \App\Support\Ui::money($publishingCosts['total'] * 1.19) }} brutto
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="m-0">Die Kosten werden nach Veranstaltungsende anhand der
                                        tatsächlichen Buchungen
                                        abgerechnet.</p>
                                @endif

                                <p class="m-0 mt-3 text-muted">
                                    Die Plattformgebühren sind Schätzungen auf Grundlage Ihrer Ticket-Einstellungen.
                                    Die tatsächliche Abrechnung erfolgt nach Veranstaltungsende anhand der realen
                                    Buchungen.
                                    @if ($publishingCosts['featured_fees']['active'] && $publishingCosts['featured_fees']['total'] > 0)
                                        Gebühren für die Hervorhebung müssen vor der Veröffentlichung bezahlt werden.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    <div>
                        <input type="hidden" name="is_published" value="0">
                        <x-ui.checkbox name="is_published" id="is_published" :old="false" :checked="old('is_published', $event->is_published)"
                            :disabled="$cannotPublish" label="Veranstaltung veröffentlichen" :hint="$cannotPublish ? 'Rechnungsdaten erforderlich' : null" />

                        <x-ui.checkbox name="is_featured" id="is_featured" :old="false" :checked="old('is_featured', $event->is_featured)"
                            label="Veranstaltung hervorheben"
                            hint="Zeitraum und Kosten wählen Sie im folgenden Dialog." />

                        <input type="hidden" id="featured_duration_type" name="featured_duration_type"
                            value="">
                        <input type="hidden" id="featured_custom_days" name="featured_custom_days" value="">
                        <input type="hidden" id="featured_start_date" name="featured_start_date" value="">

                        <x-ui.checkbox name="is_private" id="is_private" :old="false" :checked="old('is_private', $event->is_private)"
                            label="Private Veranstaltung (Zugangscode erforderlich)" />

                        <div id="access-code-field"
                            class="{{ old('is_private', $event->is_private) ? '' : 'hidden' }} mt-2 pl-8">
                            <x-ui.field label="Zugangscode" name="access_code" :value="$event->access_code"
                                input-class="max-w-80" />
                        </div>
                    </div>
                </div>
            </section>

            {{-- 5 Ticket-Einstellungen (bei externen Veranstaltungen ausgeblendet) --}}
            <section id="ticket-settings-section" aria-labelledby="step-tickets" class="border-t border-line py-8">
                <x-ui.step-heading number="5" id="step-tickets"
                    class="mb-6">Ticket-Einstellungen</x-ui.step-heading>

                <div class="flex flex-col gap-y-5">
                    <x-ui.textarea label="Hinweise auf dem Ticket" name="ticket_notes" :value="$event->ticket_notes" optional
                        rows="4" placeholder="z. B. Bitte Personalausweis mitbringen, Einlass ab 18:30 Uhr"
                        hint="Diese Hinweise werden auf allen Tickets dieser Veranstaltung angezeigt. Höchstens 1000 Zeichen." />

                    <div>
                        <input type="hidden" name="requires_ticket" value="0">
                        <x-ui.checkbox name="requires_ticket" id="requires_ticket" :old="false"
                            :checked="old('requires_ticket', $event->requires_ticket ?? true)" label="Ticket-Versand erforderlich"
                            hint="Wenn deaktiviert, werden keine Tickets per E-Mail versendet. Teilnehmende erhalten nur eine Buchungsbestätigung mit Link zur Buchungsseite." />

                        <div id="qr_code_option">
                            <input type="hidden" name="show_qr_code_on_ticket" value="0">
                            <x-ui.checkbox name="show_qr_code_on_ticket" :old="false" :checked="old('show_qr_code_on_ticket', $event->show_qr_code_on_ticket ?? true)"
                                label="QR-Code auf Tickets anzeigen"
                                hint="Wenn aktiviert, wird auf jedem Ticket ein QR-Code für den Check-in angezeigt." />
                        </div>
                    </div>

                    <div class="flex flex-col gap-y-5 border-t border-line pt-6">
                        <h3 class="m-0 text-h3 text-ink">Stornierungsrichtlinie</h3>

                        <div>
                            <input type="hidden" name="cancellation_allowed" value="0">
                            <x-ui.checkbox name="cancellation_allowed" id="cancellation_allowed_edit"
                                :old="false" :checked="old('cancellation_allowed', $event->cancellation_allowed)" label="Stornierung durch Teilnehmende erlauben"
                                hint="Wenn aktiviert, können Teilnehmende ihre Buchung selbst stornieren." />

                            <div id="cancellation_days_field_edit"
                                class="{{ old('cancellation_allowed', $event->cancellation_allowed) ? '' : 'hidden' }} mt-2 pl-8">
                                <x-ui.field label="Stornierungsfrist (Tage vor Veranstaltungsbeginn)"
                                    name="cancellation_days_before" id="cancellation_days_before_edit" type="number"
                                    min="0" placeholder="z. B. 3" :value="$event->cancellation_days_before" input-class="max-w-32"
                                    hint="Stornierung muss mindestens diese Anzahl an Tagen vor Beginn erfolgen. Leer lassen = bis Veranstaltungsbeginn." />
                            </div>
                        </div>

                        <x-ui.select label="Organisationsfeld bei Buchung und Personalisierung"
                            name="organization_field_mode" :options="[
                                'none' => 'Nicht anzeigen',
                                'optional' => 'Optional anzeigen',
                                'required' => 'Pflichtfeld',
                            ]" :selected="$event->organization_field_mode ?? 'none'"
                            hint="Legt fest, ob Teilnehmende bei der Buchung und Personalisierung ihre Organisation bzw. Einrichtung angeben müssen." />

                        <fieldset class="m-0 min-w-0 border-0 p-0">
                            <x-ui.label as="legend">Bestätigungsmodus für kostenfreie Anmeldungen</x-ui.label>
                            <x-ui.radio name="free_ticket_auto_confirm" value="1" :old="false"
                                :checked="(bool) old(
                                    'free_ticket_auto_confirm',
                                    $event->free_ticket_auto_confirm ?? true,
                                )" label="Automatisch bestätigen"
                                hint="Anmeldungen werden sofort bestätigt. Zugangsdaten und Tickets werden automatisch versendet." />
                            <x-ui.radio name="free_ticket_auto_confirm" value="0" :old="false"
                                :checked="!old('free_ticket_auto_confirm', $event->free_ticket_auto_confirm ?? true)" label="Manuelle Bestätigung erforderlich"
                                hint="Anmeldungen müssen erst durch den Veranstalter freigegeben werden. Zugangsdaten werden erst nach Bestätigung versendet." />
                        </fieldset>

                        @include('organizer.events.partials.invoice-ticket-mode', [
                            'organization' => $event->organization,
                            'value' => (bool) old('tickets_before_invoice', $event->tickets_before_invoice),
                        ])
                    </div>
                </div>
            </section>

            {{-- Kommunikationsplan (Hilfe, ohne Felder) --}}
            <div class="border-t border-line py-8">
                <details class="group rounded-box border border-line">
                    <summary
                        class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-4 px-4 py-3 [&::-webkit-details-marker]:hidden">
                        <span class="flex flex-col gap-0.5">
                            <span class="text-h3 text-ink">Kommunikation mit Teilnehmenden</span>
                            <span class="text-small text-muted">Welche E-Mails werden wann automatisch
                                versendet?</span>
                        </span>
                        <x-icon.chevron-down class="h-5 w-5 flex-none text-ink group-open:rotate-180" />
                    </summary>
                    <div class="border-t border-line p-4">
                        @include('organizer.partials.communication-plan')
                    </div>
                </details>
            </div>

            <div class="flex flex-wrap gap-3 border-t border-line pt-8">
                <x-ui.button type="submit">Änderungen speichern</x-ui.button>
                <x-ui.button variant="secondary" :href="route('organizer.events.index')">Abbrechen</x-ui.button>
            </div>
        </form>

        {{-- Rechte Spalte: Freigaben, Ticketarten und Termine (eigene Formulare außerhalb des Hauptformulars) --}}
        <div class="min-w-0">
            @php
                $pendingApprovalCount = $event->bookings()->where('status', 'pending_approval')->count();
            @endphp
            @if ($pendingApprovalCount > 0)
                <x-ui.alert tone="warning" class="mb-8"
                    title="{{ trans_choice('{1} :count Anmeldung wartet|[0,*] :count Anmeldungen warten', $pendingApprovalCount) }} auf Bestätigung">
                    <div class="mt-2 flex flex-wrap items-center gap-x-6 gap-y-2">
                        <a
                            href="{{ route('organizer.bookings.index', ['event_id' => $event->id, 'status' => 'pending_approval']) }}">Anzeigen</a>
                        <form method="POST" action="{{ route('organizer.events.approve-all-pending', $event) }}"
                            class="m-0">
                            @csrf
                            <button type="submit"
                                class="min-h-11 cursor-pointer font-medium text-current underline underline-offset-[3px]"
                                onclick="return confirm('{{ $pendingApprovalCount === 1 ? 'Die Anmeldung' : 'Alle ' . $pendingApprovalCount . ' Anmeldungen' }} bestätigen?')">
                                Alle bestätigen
                            </button>
                        </form>
                    </div>
                </x-ui.alert>
            @endif

            {{-- Früher nur sichtbar, wenn Rechnungsdaten fehlten (falsch platziertes @if) – jetzt immer --}}
            <section aria-labelledby="teilnehmende-titel" class="border-t border-line py-8">
                <h2 id="teilnehmende-titel" class="m-0 mb-2 text-h2-form text-ink">Teilnehmende</h2>
                <p class="m-0 mb-4 text-small text-muted tabular-nums">{{ $event->getAttendeesCount() }}
                    bestätigte
                    Teilnehmende</p>
                @php
                    $waitlistCount = \App\Models\EventWaitlist::where('event_id', $event->id)
                        ->where('status', 'waiting')
                        ->count();
                @endphp
                <div class="flex flex-col gap-3">
                    <x-ui.button variant="secondary" full :href="route('organizer.events.attendees.download', $event)">
                        <x-slot:icon><x-icon.download /></x-slot:icon>
                        Teilnehmerliste herunterladen
                    </x-ui.button>
                    <x-ui.button variant="secondary" full :href="route('organizer.events.attendees.contact', $event)">
                        <x-slot:icon><x-icon.mail /></x-slot:icon>
                        Teilnehmende kontaktieren
                    </x-ui.button>
                    <x-ui.button variant="secondary" full :href="route('organizer.events.waitlist.index', $event)">
                        <x-slot:icon><x-icon.clock /></x-slot:icon>
                        Warteliste verwalten
                        @if ($waitlistCount > 0)
                            <span class="tabular-nums">({{ $waitlistCount }})</span>
                        @endif
                    </x-ui.button>
                </div>
            </section>

            <section id="ticket-types-section" aria-labelledby="ticketarten-titel" class="border-t border-line py-8">
                <h2 id="ticketarten-titel" class="m-0 mb-6 text-h2-form text-ink">Ticketarten</h2>

                @if ($ticketTypes->isEmpty())
                    <p class="m-0 mb-6 text-base text-muted">Noch keine Ticketarten angelegt.</p>
                @else
                    <ul class="m-0 mb-6 list-none divide-y divide-line rounded-box border border-line p-0">
                        @foreach ($ticketTypes as $ticketType)
                            <li class="flex flex-col gap-3 p-4">
                                <div class="flex min-w-0 flex-col gap-1">
                                    <h3 class="m-0 text-base font-semibold text-ink">{{ $ticketType->name }}</h3>
                                    @if ($ticketType->description)
                                        <p class="m-0 text-small text-muted">{{ $ticketType->description }}</p>
                                    @endif
                                    <p class="m-0 text-small text-ink-soft tabular-nums">
                                        {{ \App\Support\Ui::money($ticketType->price) }}
                                        · verfügbar {{ $ticketType->availableQuantity() }} von
                                        {{ $ticketType->quantity ?? 'unbegrenzt' }}
                                        · verkauft {{ $ticketType->quantity_sold }}
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-ui.button variant="secondary" size="sm" :href="route('organizer.events.ticket-types.edit', [$event, $ticketType])">
                                        <x-slot:icon><x-icon.edit /></x-slot:icon>
                                        Bearbeiten
                                    </x-ui.button>
                                    @if (!$ticketType->hasActiveSales())
                                        <form
                                            action="{{ route('organizer.events.ticket-types.destroy', [$event, $ticketType]) }}"
                                            method="POST" onsubmit="return confirm('Wirklich löschen?')"
                                            class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="danger" size="sm">
                                                <x-slot:icon><x-icon.trash /></x-slot:icon>
                                                Löschen
                                            </x-ui.button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-small text-muted"
                                            title="Löschen nicht möglich: Es gibt noch aktive Buchungen.">
                                            <x-icon.lock class="h-4 w-4" />
                                            Löschen nicht möglich (aktive Buchungen)
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="flex flex-wrap gap-3">
                    <x-ui.button variant="secondary" :href="route('organizer.events.ticket-types.create', $event)">
                        <x-slot:icon><x-icon.plus /></x-slot:icon>
                        Ticketart anlegen
                    </x-ui.button>
                    <x-ui.button variant="secondary"
                        onclick="document.getElementById('add-ticket-form').classList.toggle('hidden')">
                        Schnell hinzufügen
                    </x-ui.button>
                </div>

                <x-ui.box id="add-ticket-form" tone="zone" padding="sm" class="mt-6 hidden">
                    <h3 class="m-0 mb-4 text-h3 text-ink">Neue Ticketart</h3>

                    <form action="{{ route('organizer.events.ticket-types.store', $event) }}" method="POST"
                        class="flex flex-col gap-y-5">
                        @csrf
                        <input type="hidden" name="redirect_to_edit" value="1">

                        <x-ui.field label="Name" name="name" id="ticket_name" required :old="false"
                            :error="false" placeholder="z. B. Standard, Ermäßigt" />
                        <x-ui.field label="Preis (€)" name="price" id="ticket_price" type="number" required
                            min="0" step="0.01" :old="false" :error="false" placeholder="0.00" />
                        <x-ui.textarea label="Beschreibung" name="description" id="ticket_description" optional
                            rows="2" :old="false" :error="false"
                            placeholder="Beschreibung der Ticketart" />
                        <x-ui.field label="Verfügbare Anzahl" name="quantity" id="ticket_quantity" type="number"
                            min="1" optional :old="false" :error="false"
                            placeholder="Leer = unbegrenzt" hint="Leer lassen für unbegrenzte Tickets" />
                        <x-ui.field label="Max. pro Buchung" name="max_per_order" id="ticket_max_per_order"
                            type="number" min="1" optional :old="false" :error="false"
                            placeholder="z. B. 10" hint="Höchstens buchbare Anzahl" />
                        <x-ui.checkbox name="is_available" id="ticket_is_available" checked :old="false"
                            :error="false" label="Ticketart aktivieren" />

                        <div class="flex flex-wrap gap-3 border-t border-line pt-4">
                            <x-ui.button type="submit">Ticketart speichern</x-ui.button>
                            <x-ui.button variant="secondary"
                                onclick="document.getElementById('add-ticket-form').classList.add('hidden')">
                                Abbrechen
                            </x-ui.button>
                        </div>
                    </form>
                </x-ui.box>
            </section>

            {{-- Terminverwaltung (außerhalb des Hauptformulars) --}}
            @include('organizer.events.partials.multiple-dates')
        </div>
    </div>

    <script>
        // Toggle Access Code field
        document.getElementById('is_private').addEventListener('change', function() {
            document.getElementById('access-code-field').classList.toggle('hidden', !this.checked);
        });

        // Live-Vorschau für neues Titelbild
        const featuredImageInput = document.getElementById('featured_image');
        const imagePreviewWrapper = document.getElementById('image-preview-wrapper');
        const imagePreview = document.getElementById('image-preview');
        if (featuredImageInput && imagePreview) {
            featuredImageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewWrapper.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                    // Deactivate delete checkbox if a new file is selected
                    const deleteCheckbox = document.getElementById('delete_featured_image');
                    if (deleteCheckbox) {
                        deleteCheckbox.checked = false;
                        const section = document.getElementById('current-image-section');
                        if (section) section.classList.remove('opacity-50');
                    }
                } else {
                    imagePreviewWrapper.classList.add('hidden');
                }
            });
        }

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

        // Toggle Multiple Dates Info Box via AJAX
        const multipleDatesCheckbox = document.getElementById('has_multiple_dates');
        const multipleDatesInfo = document.getElementById('multiple-dates-info');

        if (multipleDatesCheckbox && multipleDatesInfo) {
            // Initial state
            multipleDatesInfo.style.display = multipleDatesCheckbox.checked ? 'block' : 'none';

            multipleDatesCheckbox.addEventListener('change', async function() {
                const isChecked = this.checked;
                const eventId = this.dataset.eventId;

                // Bestätigungsdialog bei Deaktivierung mit existierenden Terminen
                @if ($event->has_multiple_dates && $event->dates->count() > 0)
                    if (!isChecked) {
                        if (!confirm(
                                'Achtung: Wenn Sie diese Option deaktivieren, werden ALLE {{ $event->dates->count() }} Termine gelöscht. Fortfahren?'
                            )) {
                            this.checked = true;
                            return;
                        }
                    }
                @endif

                // Disable checkbox während der Anfrage
                this.disabled = true;

                try {
                    const response = await fetch(`/organizer/events/${eventId}/multiple-dates/toggle`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            has_multiple_dates: isChecked
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Success-Message anzeigen
                        const alertDiv = document.createElement('div');
                        alertDiv.setAttribute('role', 'status');
                        alertDiv.className =
                            'fixed top-4 right-4 z-50 rounded-control border border-success-line bg-success-zone px-4 py-3 text-small text-success';
                        alertDiv.textContent = data.message;
                        document.body.appendChild(alertDiv);

                        setTimeout(() => alertDiv.remove(), 3000);

                        // Seite neu laden wenn angefordert
                        if (data.reload) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Info-Box aktualisieren
                            multipleDatesInfo.style.display = isChecked ? 'block' : 'none';
                        }
                    } else {
                        throw new Error(data.message || 'Fehler beim Speichern');
                    }
                } catch (error) {
                    console.error('Error:', error);

                    // Fehler-Message
                    const alertDiv = document.createElement('div');
                    alertDiv.setAttribute('role', 'alert');
                    alertDiv.className =
                        'fixed top-4 right-4 z-50 rounded-control border border-danger-line bg-danger-zone px-4 py-3 text-small text-danger';
                    alertDiv.textContent = 'Fehler: ' + error.message;
                    document.body.appendChild(alertDiv);

                    setTimeout(() => alertDiv.remove(), 5000);

                    // Checkbox zurücksetzen
                    this.checked = !isChecked;
                } finally {
                    this.disabled = false;
                }
            });
        }

        // Toggle Venue/Online/External sections based on event type
        const eventTypeSelect = document.getElementById('event_type');
        const venueSection = document.getElementById('venue-section');
        const onlineSection = document.getElementById('online-section');
        const externalSection = document.getElementById('external-section');
        const pricingSection = document.getElementById('pricing-section');
        const ticketSettingsSection = document.getElementById('ticket-settings-section');
        const ticketTypesSection = document.getElementById('ticket-types-section');

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
            if (externalSection) {
                externalSection.style.display = isExternal ? 'block' : 'none';
                const externalUrl = document.getElementById('external_booking_url');
                if (externalUrl) {
                    externalUrl.required = isExternal;
                }
            }

            // Hide pricing, ticket settings and ticket types for external events
            if (pricingSection) pricingSection.style.display = isExternal ? 'none' : '';
            if (ticketSettingsSection) ticketSettingsSection.style.display = isExternal ? 'none' : '';
            if (ticketTypesSection) ticketTypesSection.style.display = isExternal ? 'none' : '';
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

        // Dynamic cost calculation when featured checkbox changes
        const featuredCheckbox = document.getElementById('is_featured');
        const costOverview = document.getElementById('cost-overview');

        if (featuredCheckbox && costOverview) {
            const wasInitiallyFeatured = featuredCheckbox.checked;

            featuredCheckbox.addEventListener('change', function() {
                if (this.checked && !wasInitiallyFeatured) {
                    // Opening modal to book featured
                    window.dispatchEvent(new CustomEvent('featured-modal-open'));
                } else if (this.checked) {
                    // Already featured, just update costs
                    updateCostEstimate();
                } else {
                    // Unchecked, clear booking data and update costs
                    document.getElementById('featured_duration_type').value = '';
                    document.getElementById('featured_custom_days').value = '';
                    document.getElementById('featured_start_date').value = '';
                    updateCostEstimate();
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

            // Keep checkbox checked
            featuredCheckbox.checked = true;

            // Update cost estimate
            updateCostEstimate();
        });

        async function updateCostEstimate() {
            const isFeatured = featuredCheckbox.checked;
            const maxAttendees = document.getElementById('max_attendees')?.value || '';
            const priceFrom = document.getElementById('price_from')?.value || '';

            try {
                const response = await fetch('{{ route('organizer.events.calculate-costs', $event) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        is_featured: isFeatured,
                        max_attendees: maxAttendees,
                        price_from: priceFrom,
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                if (data.success && data.costs) {
                    updateCostOverviewUI(data.costs);
                }
            } catch (error) {
                console.error('Error updating cost estimate:', error);
            }
        }

        function updateCostOverviewUI(costs) {
            if (!costOverview) return;

            // Build the HTML for the breakdown (gleiche Struktur wie die serverseitige Ausgabe)
            let breakdownHTML = '';

            if (costs.breakdown && costs.breakdown.length > 0) {
                breakdownHTML = '<div class="flex flex-col gap-2">';

                costs.breakdown.forEach(item => {
                    let statusBadge = '';
                    if (item.status === 'pending') {
                        statusBadge =
                            '<span class="inline-flex items-center gap-1.5 text-small whitespace-nowrap text-warning"><span aria-hidden="true" class="h-2 w-2 flex-none rounded-full bg-warning"></span>Zahlung offen</span>';
                    }

                    breakdownHTML += `
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="font-medium">${item.label}</div>
                                ${item.description ? `<div class="text-muted">${item.description}</div>` : ''}
                                ${statusBadge}
                            </div>
                            <div class="font-medium whitespace-nowrap tabular-nums">
                                ${formatCurrency(item.amount)}
                            </div>
                        </div>
                    `;
                });

                const vatAmount = costs.total * 0.19;
                const totalWithVat = costs.total * 1.19;

                breakdownHTML += `
                    <div class="mt-2 border-t border-line pt-2">
                        <div class="flex items-baseline justify-between gap-4 font-semibold">
                            <span>Geschätzte Gesamtkosten (netto)</span>
                            <span class="text-h3 whitespace-nowrap tabular-nums">
                                ${formatCurrency(costs.total)}
                            </span>
                        </div>
                        <div class="mt-1 text-muted tabular-nums">
                            + ${formatCurrency(vatAmount)} MwSt. (19 %) = ${formatCurrency(totalWithVat)} brutto
                        </div>
                    </div>
                `;
                breakdownHTML += '</div>';
            } else {
                breakdownHTML =
                    '<p class="m-0">Die Kosten werden nach Veranstaltungsende anhand der tatsächlichen Buchungen abgerechnet.</p>';
            }

            // Find the content area and update it
            const contentArea = costOverview.querySelector('[data-cost-content]');
            if (contentArea) {
                contentArea.innerHTML = breakdownHTML + `
                    <p class="m-0 mt-3 text-muted">
                        Die Plattformgebühren sind Schätzungen auf Grundlage Ihrer Ticket-Einstellungen.
                        Die tatsächliche Abrechnung erfolgt nach Veranstaltungsende anhand der realen Buchungen.
                        ${costs.featured_fees && costs.featured_fees.active && costs.featured_fees.total > 0 ?
                            'Gebühren für die Hervorhebung müssen vor der Veröffentlichung bezahlt werden.' : ''}
                    </p>
                `;
            }

            // Show/hide the cost overview based on whether there are costs
            if (costs.total > 0 || !{{ $event->is_published ? 'true' : 'false' }}) {
                costOverview.style.display = 'block';
            }
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('de-DE', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        }
    </script>

    {{-- Dialog "Veranstaltung hervorheben" (window-Event featured-modal-open) --}}
    <x-featured-booking-modal :event="$event" />

    <script>
        // Toggle cancellation days field
        const cancellationCheckboxEdit = document.getElementById('cancellation_allowed_edit');
        const cancellationDaysFieldEdit = document.getElementById('cancellation_days_field_edit');
        if (cancellationCheckboxEdit && cancellationDaysFieldEdit) {
            cancellationCheckboxEdit.addEventListener('change', function() {
                cancellationDaysFieldEdit.classList.toggle('hidden', !this.checked);
            });
        }
    </script>
</x-layouts.app>
