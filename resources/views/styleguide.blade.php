@php
    use App\Support\Ui;

    $colors = [
        ['ink', '#1E3135', 'Text, Überschriften, Fokus'],
        ['primary', '#2B4347', 'Primärbutton, Marken'],
        ['ink-soft', '#3B5053', 'Sekundärtext mit Gewicht'],
        ['muted', '#55676A', 'Sekundärtext, Labels'],
        ['line', '#DCE3E2', 'Trennlinien, Container'],
        ['line-strong', '#7F9091', 'Kontur Bedienelemente'],
        ['disabled', '#9AA8A9', 'deaktiviert'],
        ['surface', '#FFFFFF', 'Seitengrund, Felder'],
        ['zone', '#F3F6F5', 'ruhige Zonen'],
        ['zone-hover', '#F6F8F8', 'Hover Listenzeile'],
        ['leaf', '#8DC11D', 'Statuspunkt „frei“'],
        ['success', '#2C5E1A', 'Erfolg (Text)'],
        ['success-zone', '#EEF6E0', 'Erfolg (Fläche)'],
        ['success-line', '#B9D98A', 'Erfolg (Kontur)'],
        ['warning', '#6B4A0A', 'Warnung (Text)'],
        ['warning-zone', '#FBF3DF', 'Warnung (Fläche)'],
        ['warning-line', '#E2C88A', 'Warnung (Kontur)'],
        ['danger', '#9B1C1C', 'Fehler, Löschen'],
        ['danger-zone', '#FCEEEE', 'Fehler (Fläche)'],
        ['danger-line', '#E3A9A9', 'Fehler (Kontur)'],
        ['danger-field', '#B42318', 'Kontur fehlerhaftes Feld'],
        ['danger-hover', '#7A1414', 'Hover Löschen-Button'],
    ];
    $swatch = [
        'ink' => 'bg-ink',
        'primary' => 'bg-primary',
        'ink-soft' => 'bg-ink-soft',
        'muted' => 'bg-muted',
        'line' => 'bg-line',
        'line-strong' => 'bg-line-strong',
        'disabled' => 'bg-disabled',
        'surface' => 'bg-surface',
        'zone' => 'bg-zone',
        'zone-hover' => 'bg-zone-hover',
        'leaf' => 'bg-leaf',
        'success' => 'bg-success',
        'success-zone' => 'bg-success-zone',
        'success-line' => 'bg-success-line',
        'warning' => 'bg-warning',
        'warning-zone' => 'bg-warning-zone',
        'warning-line' => 'bg-warning-line',
        'danger' => 'bg-danger',
        'danger-zone' => 'bg-danger-zone',
        'danger-line' => 'bg-danger-line',
        'danger-field' => 'bg-danger-field',
        'danger-hover' => 'bg-danger-hover',
    ];
    $type = [
        ['text-display', 'display', 'clamp(30–52px) / 1,08 / 600', 'Fortbildungen für Menschen, die Schule gestalten.'],
        ['text-title', 'title', 'clamp(30–44px) / 1,1 / 600', 'Tickets buchen'],
        ['text-h2', 'h2', '24px / 1,25 / 600', 'Kommende Veranstaltungen'],
        ['text-h2-form', 'h2-form', '22px / 1,25 / 600', 'Ihre Daten'],
        ['text-h3', 'h3', '18px / 1,3 / 600', 'Beziehung vor Stoff: Klassenführung, die trägt'],
        [
            'text-base',
            'base',
            '16px / 1,5 / 400',
            'Fließtext und Feldinhalt. Zahlen stehen ruhig: 26.11.2026, 14:00–17:30 Uhr, 45,00 €.',
        ],
        ['text-ui font-medium', 'ui', '15px / 1,4 / 500', 'Buttons, Navigation, Metazeile'],
        ['text-small', 'small', '14px / 1,4 / 400–500', 'Labels und Sekundärzeilen'],
        ['text-caption', 'caption', '13px / 1,3 / 400', 'Zellenlabels, Monat im Datumsblock'],
    ];
    $icons = collect(\Illuminate\Support\Facades\File::files(resource_path('views/components/icon')))
        ->map(fn($file) => \Illuminate\Support\Str::before($file->getFilename(), '.blade.php'))
        ->sort()
        ->values();
    $toc = [
        'farben' => 'Farben',
        'schrift' => 'Schrift',
        'wortmarke' => 'Wortmarke',
        'buttons' => 'Buttons',
        'felder' => 'Formularfelder',
        'auswahl' => 'Checkbox, Radio, Auswahlkarte',
        'stepper' => 'Stepper',
        'datum' => 'Datumsblock',
        'status' => 'Status',
        'suche' => 'Suchzeile',
        'listen' => 'Listenzeilen',
        'buchung' => 'Abschnittsmarke und Zusammenfassung',
        'meldungen' => 'Meldungen',
        'tabelle' => 'Tabelle',
        'kopf' => 'Seitenkopf, Abschnitt, Leer',
        'flaechen' => 'Zone und Kasten',
        'menues' => 'Dialog, Menü, Reiter',
        'kennzahlen' => 'Kennzahlen und Detail-Paare',
        'seiten' => 'Seitennavigation',
        'icons' => 'Icons',
    ];
@endphp
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Styleguide – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface font-sans text-base text-ink antialiased">
    <header class="border-b border-line bg-surface">
        <div class="mx-auto flex min-h-18 max-w-page flex-wrap items-center justify-between gap-x-4 px-page">
            <x-wordmark />
            <span class="text-small text-muted">Styleguide · interne Prüfvorlage</span>
        </div>
    </header>

    <x-ui.zone>
        <div class="flex flex-col gap-6 py-[clamp(40px,6vw,64px)]">
            <x-ui.page-header title="Styleguide"
                subtitle="Jeder Baustein aus resources/views/components/ui in jedem Zustand. Nachschlagewerk mit Props und Beispielen: docs/design/BAUSTEINE.md."
                class="mb-0" />
            <nav aria-label="Inhalt">
                <ul class="m-0 grid list-none grid-cols-[repeat(auto-fill,minmax(220px,1fr))] gap-x-8 p-0">
                    @foreach ($toc as $anchor => $label)
                        <li><a href="#{{ $anchor }}"
                                class="flex min-h-11 items-center justify-between gap-4 border-t border-line text-ui font-medium text-ink no-underline hover:underline">{{ $label }}<x-icon.arrow-right
                                    class="h-4 w-4 text-muted" /></a></li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </x-ui.zone>

    <main class="mx-auto flex max-w-page flex-col gap-16 px-page py-[clamp(40px,5vw,64px)]">

        {{-- Farben --}}
        <x-ui.section title="Farben" id="farben"
            description="Nur diese Töne. Klassen: bg-…, text-…, border-… mit dem Token-Namen.">
            <ul class="m-0 grid list-none grid-cols-[repeat(auto-fill,minmax(180px,1fr))] gap-4 p-0">
                @foreach ($colors as [$token, $hex, $use])
                    <li class="flex flex-col gap-2">
                        <span class="block h-16 rounded-control border border-line {{ $swatch[$token] }}"></span>
                        <span class="text-small font-medium">{{ $token }}</span>
                        <span class="text-caption text-muted tabular-nums">{{ $hex }} ·
                            {{ $use }}</span>
                    </li>
                @endforeach
            </ul>
        </x-ui.section>

        {{-- Schrift --}}
        <x-ui.section title="Schrift" id="schrift"
            description="Geist Variable, Gewichte 400/500/600. Klassen: text-display, text-title, text-h2 … text-caption.">
            <div class="divide-y divide-line border-y border-line">
                @foreach ($type as [$class, $token, $spec, $sample])
                    <div class="grid gap-2 py-4 md:grid-cols-[200px_minmax(0,1fr)] md:gap-6">
                        <div class="flex flex-col">
                            <span class="text-small font-medium">{{ $token }}</span>
                            <span class="text-caption text-muted">{{ $spec }}</span>
                        </div>
                        <p class="m-0 {{ $class }}">{{ $sample }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.section>

        {{-- Wortmarke --}}
        <x-ui.section title="Wortmarke" id="wortmarke">
            <div class="flex flex-wrap items-center gap-8">
                <x-wordmark />
                <div class="rounded-box border border-line bg-zone px-6 py-2"><x-wordmark size="sm" /></div>
                <x-wordmark :href="false" />
            </div>
        </x-ui.section>

        {{-- Buttons --}}
        <x-ui.section title="Buttons" id="buttons" description="Pro Abschnitt höchstens ein Primärbutton.">
            <div class="flex flex-col gap-6">
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button>Buchen</x-ui.button>
                    <x-ui.button variant="secondary">Auf die Warteliste</x-ui.button>
                    <x-ui.button variant="danger">Buchung stornieren</x-ui.button>
                    <x-ui.button variant="link" href="#buttons" arrow>Alle Veranstaltungen</x-ui.button>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button size="sm">Speichern</x-ui.button>
                    <x-ui.button size="sm" variant="secondary">Bearbeiten</x-ui.button>
                    <x-ui.button size="sm" variant="danger">Löschen</x-ui.button>
                    <x-ui.button size="sm" variant="link" href="#buttons">Details</x-ui.button>
                    <span class="text-small text-muted">size="sm" – nur in Tabellenzeilen</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button size="lg" variant="secondary" href="#buttons" arrow>Veranstaltung
                        anlegen</x-ui.button>
                    <x-ui.button size="xl">Kostenpflichtig buchen</x-ui.button>
                    <span class="text-small text-muted">size="lg" (48 px), size="xl" (52 px, Abschluss der
                        Buchung)</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button variant="secondary">
                        <x-slot:icon><x-icon.download /></x-slot:icon>
                        Ticket herunterladen
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="#buttons">
                        <x-slot:icon><x-icon.calendar /></x-slot:icon>
                        In den Kalender
                    </x-ui.button>
                    <x-ui.button>
                        <x-slot:icon><x-icon.plus /></x-slot:icon>
                        Veranstaltung anlegen
                    </x-ui.button>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button disabled>Buchen</x-ui.button>
                    <x-ui.button variant="secondary" disabled>Auf die Warteliste</x-ui.button>
                    <x-ui.button variant="danger" disabled>Löschen</x-ui.button>
                    <x-ui.button variant="secondary" href="#buttons" disabled>Link deaktiviert</x-ui.button>
                    <x-ui.button variant="link" disabled>Textlink deaktiviert</x-ui.button>
                </div>
                <div class="max-w-summary">
                    <x-ui.button full>Volle Breite (full)</x-ui.button>
                </div>
            </div>
        </x-ui.section>

        {{-- Formularfelder --}}
        <x-ui.section title="Formularfelder" id="felder"
            description="Felder 48 px, Label 6 px darüber, Raster minmax(248px, 1fr), Abstand 20/16 px.">
            <div class="flex max-w-form flex-col gap-8">
                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="Name" name="sg_name" value="Maria Muster" required autocomplete="name" />
                    <x-ui.field label="E-Mail" name="sg_email" type="email" value="maria.muster@beispiel" required
                        error="Bitte geben Sie eine gültige E-Mail-Adresse ein." />
                    <x-ui.field label="Telefon" name="sg_phone" type="tel" optional />
                    <x-ui.field label="Schule / Einrichtung" name="sg_school" optional
                        hint="Erscheint auf dem Teilnahmezertifikat."
                        placeholder="z. B. Evangelische Schule Radebeul" />
                    <x-ui.field label="Buchungsnummer" name="sg_number" value="BK-6735A1F2" disabled />
                    <x-ui.field label="Kundennummer" name="sg_customer" value="100245" readonly
                        hint="Wird automatisch vergeben." />
                </div>

                <div class="flex flex-wrap gap-x-4 gap-y-5">
                    <x-ui.field label="PLZ" name="sg_zip" inputmode="numeric" required
                        class="max-w-40 flex-[1_1_112px]" />
                    <x-ui.field label="Ort" name="sg_city" required class="min-w-0 flex-[3_1_200px]" />
                </div>

                <x-ui.select label="Land" name="sg_country" required :options="['DE' => 'Deutschland', 'AT' => 'Österreich', 'CH' => 'Schweiz']" selected="DE" />
                <x-ui.select label="Kategorie" name="sg_category" placeholder="Bitte wählen"
                    error="Bitte wählen Sie eine Kategorie.">
                    <option value="1">Hauptfach Mensch</option>
                    <option value="2">Schulentwicklung</option>
                </x-ui.select>
                <x-ui.select label="Format" name="sg_format" disabled :options="['physical' => 'Präsenz']" />

                <x-ui.textarea label="Nachricht an den Veranstalter" name="sg_message" optional
                    hint="Höchstens 1000 Zeichen." />
                <x-ui.textarea label="Beschreibung" name="sg_description" required
                    error="Die Beschreibung muss mindestens 50 Zeichen lang sein.">Zu kurz.</x-ui.textarea>

                <div>
                    <x-ui.label for="sg_discount" optional>Rabattcode</x-ui.label>
                    <div class="flex max-w-[400px] flex-wrap gap-2">
                        <x-ui.input name="sg_discount" class="min-w-0 flex-[1_1_180px]" autocomplete="off"
                            aria-describedby="sg_discount-error" :invalid="true" value="HERBST" />
                        <x-ui.button variant="secondary" size="lg">Einlösen</x-ui.button>
                    </div>
                    <x-ui.field-error id="sg_discount-error" message="Dieser Rabattcode ist abgelaufen." />
                    <p class="mt-2 mb-0 text-caption text-muted">Sonderfall mit &lt;x-ui.label&gt;, &lt;x-ui.input&gt;
                        und &lt;x-ui.field-error&gt;.</p>
                </div>

                <div class="flex flex-wrap items-end gap-3">
                    <x-ui.input name="sg_filter" size="sm" placeholder="Name oder E-Mail"
                        aria-label="Teilnehmende filtern" class="max-w-64" />
                    <x-ui.button variant="secondary">Filtern</x-ui.button>
                    <span class="text-small text-muted">&lt;x-ui.input size="sm"&gt; (44 px) für
                        Filterzeilen</span>
                </div>
            </div>
        </x-ui.section>

        {{-- Checkbox, Radio, Auswahlkarte --}}
        <x-ui.section title="Checkbox, Radio, Auswahlkarte" id="auswahl">
            <div class="flex max-w-form flex-col gap-8">
                <div>
                    <x-ui.checkbox name="sg_newsletter" label="Newsletter abonnieren"
                        hint="Höchstens einmal im Monat." />
                    <x-ui.checkbox name="sg_terms" checked required>Ich habe die <a href="#auswahl"
                            class="font-medium underline underline-offset-[3px]">Datenschutzerklärung</a> gelesen und
                        akzeptiere sie.</x-ui.checkbox>
                    <x-ui.checkbox name="sg_agb" required error="Bitte stimmen Sie den Teilnahmebedingungen zu."
                        label="Ich akzeptiere die Teilnahmebedingungen." />
                    <x-ui.checkbox name="sg_locked" checked disabled
                        label="Pflichtbenachrichtigungen (nicht abwählbar)" />
                </div>

                <fieldset class="m-0 min-w-0 border-0 p-0">
                    <x-ui.label as="legend" required>Anrede</x-ui.label>
                    <x-ui.radio name="sg_salutation" value="frau" label="Frau" checked />
                    <x-ui.radio name="sg_salutation" value="herr" label="Herr" />
                    <x-ui.radio name="sg_salutation" value="keine" label="Keine Angabe"
                        hint="Die Anrede entfällt in E-Mails." />
                    <x-ui.radio name="sg_salutation" value="divers" label="Nicht verfügbar" disabled />
                </fieldset>

                <fieldset class="m-0 min-w-0 border-0 p-0">
                    <x-ui.label as="legend" required class="mb-2">Zahlungsmethode</x-ui.label>
                    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-3">
                        <x-ui.choice name="sg_payment" value="invoice" title="Rechnung"
                            description="Sie erhalten die Rechnung per E-Mail" checked />
                        <x-ui.choice name="sg_payment" value="paypal" title="PayPal"
                            description="Sie werden nach dem Absenden zu PayPal weitergeleitet" />
                        <x-ui.choice name="sg_payment" value="card" title="Kreditkarte"
                            description="Derzeit nicht verfügbar" disabled />
                        <x-ui.choice name="sg_ticket" value="early" title="Frühbucher" description="bis 31.12.2026"
                            aside="35,00 €" />
                    </div>
                </fieldset>
            </div>
        </x-ui.section>

        {{-- Stepper --}}
        <x-ui.section title="Stepper" id="stepper"
            description="46 px Rahmen, Buttons 44 × 44 px; löst input/change am Feld aus. Minus ist beim Minimum deaktiviert, Plus beim Maximum.">
            <div class="max-w-form">
                @foreach ([['Teilnahme', '45,00 € pro Person', 'sg_qty_1', 2, 10, false], ['Ermäßigt (Referendariat, Studium)', '30,00 € pro Person', 'sg_qty_2', 0, 10, false], ['Gruppe (max. 3)', '120,00 € pro Gruppe', 'sg_qty_3', 3, 3, false], ['Ausverkauft', '45,00 € pro Person', 'sg_qty_4', 0, 0, true]] as [$ticketName, $ticketPrice, $ticketField, $ticketValue, $ticketMax, $ticketDisabled])
                    <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 border-b border-line py-4">
                        <div class="flex flex-[1_1_200px] flex-col gap-0.5">
                            <span class="text-base font-semibold">{{ $ticketName }}</span>
                            <span class="text-ui text-muted tabular-nums">{{ $ticketPrice }}</span>
                        </div>
                        <x-ui.stepper :name="$ticketField" :label="$ticketName" :value="$ticketValue" :max="$ticketMax"
                            :disabled="$ticketDisabled" />
                    </div>
                @endforeach
            </div>
        </x-ui.section>

        {{-- Datumsblock --}}
        <x-ui.section title="Datumsblock" id="datum"
            description="80 × 80 px, als <time>. Ein Tag, mehrtägig, über Monats- und Jahresgrenzen, auf Zonen-Fläche.">
            <div class="flex flex-wrap items-center gap-4">
                <x-ui.date-block :date="\Illuminate\Support\Carbon::parse('2026-11-26 14:00')" :end="\Illuminate\Support\Carbon::parse('2026-11-26 17:30')" />
                <x-ui.date-block :date="\Illuminate\Support\Carbon::parse('2027-01-22')" :end="\Illuminate\Support\Carbon::parse('2027-01-23')" />
                <x-ui.date-block :date="\Illuminate\Support\Carbon::parse('2026-11-30')" :end="\Illuminate\Support\Carbon::parse('2026-12-02')" />
                <x-ui.date-block :date="\Illuminate\Support\Carbon::parse('2026-12-30')" :end="\Illuminate\Support\Carbon::parse('2027-01-02')" />
                <div class="rounded-box border border-line bg-zone p-4"><x-ui.date-block date="2026-12-04"
                        tone="white" /></div>
            </div>
            <p class="mt-4 mb-0 text-small text-muted">Formate (App\Support\Ui): {{ Ui::date(now()) }} ·
                {{ Ui::time(now()->setTime(14, 0), now()->setTime(17, 30)) }} · {{ Ui::price(45) }} ·
                {{ Ui::price(0) }} · {{ Ui::money(45) }}</p>
        </x-ui.section>

        {{-- Status --}}
        <x-ui.section title="Status" id="status"
            description="Punkt plus Text, nie Farbe allein, keine gefüllten Pillen.">
            <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
                <x-ui.status tone="free">noch 6 frei</x-ui.status>
                <x-ui.status tone="waitlist">Warteliste</x-ui.status>
                <x-ui.status tone="neutral">Entwurf</x-ui.status>
                <x-ui.status tone="success">Bestätigt</x-ui.status>
                <x-ui.status tone="warning">Zahlung ausstehend</x-ui.status>
                <x-ui.status tone="danger">Storniert</x-ui.status>
                <x-ui.status tone="info">Neu</x-ui.status>
            </div>
        </x-ui.section>

        {{-- Suchzeile --}}
        <x-ui.section title="Suchzeile" id="suche"
            description="Ein Rahmen, vier Zellen; unter 768 px gestapelt. Weitere Filter hinter dem Button „Filter“.">
            <div class="flex flex-col gap-8">
                <x-ui.zone :contained="false" border="none" class="rounded-box p-[clamp(16px,4vw,32px)]">
                    <x-ui.search-bar :action="route('events.index')" class="max-w-[960px]">
                        <x-ui.search-cell label="Format" name="event_type" :options="['' => 'Alle', 'physical' => 'Präsenz', 'online' => 'Online', 'hybrid' => 'Hybrid']" />
                        <x-ui.search-cell label="Zeitraum" name="period" wide :options="[
                            '' => 'Beliebig',
                            'month' => 'Diesen Monat',
                            '3months' => 'Nächste 3 Monate',
                            'schoolyear' => 'Dieses Schuljahr',
                        ]" />
                    </x-ui.search-bar>
                </x-ui.zone>

                <x-ui.search-bar :action="route('events.index')" name="q2" :filter-count="1"
                    aria-label="Veranstaltungen filtern">
                    <x-ui.search-cell label="Format" name="format2" :options="['' => 'Alle', 'online' => 'Online']" />
                    <x-slot:filters>
                        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                            <x-ui.select label="Kategorie" name="sg_filter_category" :options="['' => 'Alle', 'schule' => 'Schulentwicklung']"
                                selected="schule" />
                            <x-ui.field label="Ort" name="sg_filter_city" />
                        </div>
                    </x-slot:filters>
                    <x-slot:active>Kategorie: Schulentwicklung · <a href="#suche"
                            class="font-medium text-ink underline underline-offset-[3px]">Zurücksetzen</a></x-slot:active>
                </x-ui.search-bar>
            </div>
        </x-ui.section>

        {{-- Listenzeilen --}}
        <x-ui.section title="Listenzeilen" id="listen"
            description="ui.event-row mit echten Veranstaltungen: buchbar, Warteliste, mehrtägig, abgesagt, beendet."
            :link-href="route('events.index')" link-label="Alle Veranstaltungen">
            <div class="flex flex-col gap-8">
                @if ($events->isEmpty())
                    <x-ui.empty title="Keine Veranstaltungen" :action-href="route('events.index')" action-label="Zur Übersicht">
                        In der Datenbank sind noch keine veröffentlichten Veranstaltungen vorhanden.
                    </x-ui.empty>
                @else
                    <x-ui.row-list>
                        @foreach ($events as $event)
                            <x-ui.event-row :event="$event" />
                        @endforeach
                    </x-ui.row-list>
                @endif

                @if ($booking)
                    <div class="flex flex-col gap-3">
                        <h3 class="m-0 text-h3">Variante „Meine Buchungen“ (Slots aside und action)</h3>
                        <x-ui.row-list>
                            <x-ui.event-row :event="$booking->event" :href="route('bookings.show', $booking->booking_number)">
                                <span class="text-small text-muted tabular-nums">Buchung
                                    {{ $booking->booking_number }}</span>
                                <x-slot:aside>
                                    <span
                                        class="text-base font-semibold whitespace-nowrap">{{ Ui::money($booking->total) }}</span>
                                    <x-ui.status :tone="Ui::bookingTone($booking->status)">{{ $booking->statusLabel() }}</x-ui.status>
                                </x-slot:aside>
                                <x-slot:action>
                                    <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)" full>Buchung
                                        ansehen</x-ui.button>
                                </x-slot:action>
                            </x-ui.event-row>
                        </x-ui.row-list>
                    </div>
                @endif

                <div class="flex flex-col gap-3">
                    <h3 class="m-0 text-h3">Generische Zeile &lt;x-ui.row&gt;</h3>
                    <x-ui.row-list>
                        <x-ui.row>
                            <span class="text-small text-muted">Organisation</span>
                            <span class="text-h3">Evangelische Schulstiftung mit einem sehr langen Namen, der in zwei
                                Zeilen umbrechen darf, ohne das Raster zu sprengen</span>
                            <x-slot:meta><span class="text-ui font-medium">3 Mitglieder</span><span
                                    class="text-small text-muted">seit 12.03.2025</span></x-slot:meta>
                            <x-slot:aside><x-ui.status tone="success">Aktiv</x-ui.status></x-slot:aside>
                            <x-slot:action><x-ui.button variant="secondary" full>Verwalten</x-ui.button></x-slot:action>
                        </x-ui.row>
                        <x-ui.row>
                            <span class="text-h3">Zeile ohne Datumsblock und ohne Aktion</span>
                            <x-slot:meta><span class="text-small text-muted">nur Hauptspalte und
                                    Meta</span></x-slot:meta>
                        </x-ui.row>
                    </x-ui.row-list>
                </div>
            </div>
        </x-ui.section>

        {{-- Buchung: Abschnittsmarke + Zusammenfassung --}}
        <x-ui.section title="Abschnittsmarke und Zusammenfassung" id="buchung"
            description="Aufbau der Buchungsseite: Formularspalte 640 px, Zusammenfassung 368 px, ab 1024 px mitlaufend.">
            @php $sample = $events->first(); @endphp
            <div
                class="grid items-start gap-10 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between lg:gap-x-12">
                <div class="min-w-0">
                    <section aria-labelledby="sg-step-1" class="pb-8">
                        <x-ui.step-heading number="1" id="sg-step-1" class="mb-2">Tickets
                            wählen</x-ui.step-heading>
                        <div
                            class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 border-b border-line py-4">
                            <div class="flex flex-[1_1_200px] flex-col gap-0.5">
                                <span class="text-base font-semibold">Teilnahme</span>
                                <span class="text-ui text-muted tabular-nums">45,00 € pro Person</span>
                            </div>
                            <x-ui.stepper name="sg_book_qty" label="Teilnahme" :value="2"
                                :max="10" />
                        </div>
                    </section>
                    <section aria-labelledby="sg-step-2" class="border-t border-line py-8">
                        <x-ui.step-heading number="2" id="sg-step-2" class="mb-6" aside="* Pflichtfeld">Ihre
                            Daten</x-ui.step-heading>
                        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                            <x-ui.field label="Name" name="sg_book_name" required value="Maria Muster" />
                            <x-ui.field label="E-Mail" name="sg_book_email" type="email" required
                                value="maria.muster@beispielschule.de" />
                        </div>
                    </section>
                    <section aria-labelledby="sg-step-3" class="border-t border-line pt-8">
                        <x-ui.step-heading number="3" id="sg-step-3" class="mb-6">Rechnung &amp;
                            Zahlung</x-ui.step-heading>
                        <x-ui.field label="Straße und Hausnummer" name="sg_book_street" required />
                    </section>
                </div>

                <x-ui.summary>
                    <x-slot:event>
                        <span>{{ $sample?->title ?? 'Beziehung vor Stoff: Klassenführung, die trägt' }}</span>
                        <span>{{ $sample ? Ui::dateTime($sample->start_date, $sample->end_date) : 'Do, 26.11.2026, 14:00–17:30 Uhr' }}</span>
                        <span>{{ $sample ? Ui::formatAndPlace($sample) : 'Präsenz · Radebeul' }}</span>
                    </x-slot:event>
                    <x-ui.summary-lines>
                        <x-ui.summary-line label="2 × Teilnahme" amount="90,00 €" />
                        <x-ui.summary-line label="Rabatt HERBST" amount="−9,00 €" muted />
                    </x-ui.summary-lines>
                    <x-ui.summary-total amount="81,00 €" />
                    <x-ui.checkbox name="sg_privacy" required size="small">Ich habe die <a href="#buchung"
                            class="font-medium underline underline-offset-[3px]">Datenschutzerklärung</a> gelesen und
                        akzeptiere sie.</x-ui.checkbox>
                    <div class="flex flex-col gap-3">
                        <x-ui.button size="xl" full>Kostenpflichtig buchen</x-ui.button>
                        <p class="m-0 flex items-center justify-center gap-2 text-center text-small text-ink-soft">
                            <x-icon.mail class="h-4 w-4 flex-none" />
                            Sie erhalten eine Bestätigung per E-Mail.
                        </p>
                    </div>
                </x-ui.summary>
            </div>
        </x-ui.section>

        {{-- Meldungen --}}
        <x-ui.section title="Meldungen" id="meldungen"
            description="ui.alert; Session-Meldungen rendert ui.flash einmal im Layout.">
            <div class="flex max-w-prose flex-col gap-3">
                <x-ui.alert>Die Anmeldung schließt drei Tage vor Beginn.</x-ui.alert>
                <x-ui.alert tone="success" dismissible>Ihre Buchung wurde gespeichert.</x-ui.alert>
                <x-ui.alert tone="warning" title="Warteliste">Diese Veranstaltung ist ausgebucht. Wir benachrichtigen
                    Sie, sobald ein Platz frei wird.</x-ui.alert>
                <x-ui.alert tone="danger" title="Bitte prüfen Sie Ihre Eingaben.">
                    <ul>
                        <li>Bitte geben Sie eine gültige E-Mail-Adresse ein.</li>
                        <li>Bitte stimmen Sie der <a href="#meldungen">Datenschutzerklärung</a> zu.</li>
                    </ul>
                </x-ui.alert>
                <x-ui.alert tone="info" size="base" title="Hinweis in Fließtextgröße">size="base" für Hinweise
                    im Lesetext.</x-ui.alert>
            </div>
        </x-ui.section>

        {{-- Tabelle --}}
        <x-ui.section title="Tabelle" id="tabelle"
            description="ui.table mit normalem Tabellen-Markup; Zellenklassen num und actions.">
            <div class="flex flex-col gap-6">
                <x-ui.table label="Buchungen">
                    <thead>
                        <tr>
                            <th scope="col">Buchung</th>
                            <th scope="col">Teilnehmende Person</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="num">Datum</th>
                            <th scope="col" class="num">Betrag</th>
                            <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ([['BK-6735A1F2', 'Maria Muster', 'confirmed', 'Bestätigt', '26.11.2026', '90,00 €'], ['BK-6735A1F3', 'Dr. Johannes Beispielmann-Langername', 'pending', 'Zahlung ausstehend', '27.11.2026', '45,00 €'], ['BK-6735A1F4', 'Erika Mustermann', 'cancelled', 'Storniert', '01.12.2026', '0,00 €']] as [$no, $person, $st, $stLabel, $date, $sum])
                            <tr>
                                <th scope="row"><a href="#tabelle"
                                        class="text-ink underline-offset-[3px] hover:underline">{{ $no }}</a>
                                </th>
                                <td>{{ $person }}</td>
                                <td><x-ui.status :tone="Ui::bookingTone($st)">{{ $stLabel }}</x-ui.status></td>
                                <td class="num">{{ $date }}</td>
                                <td class="num">{{ $sum }}</td>
                                <td class="actions">
                                    <x-ui.button size="sm" variant="secondary"
                                        href="#tabelle">Ansehen</x-ui.button>
                                    <x-ui.dropdown label="Weitere Aktionen" icon-only>
                                        <x-ui.dropdown-item href="#tabelle"><x-slot:icon><x-icon.download /></x-slot:icon> Rechnung
                                            herunterladen</x-ui.dropdown-item>
                                        <x-ui.dropdown-item><x-slot:icon><x-icon.mail /></x-slot:icon>
                                            Bestätigung erneut senden</x-ui.dropdown-item>
                                        <x-ui.dropdown-item danger><x-slot:icon><x-icon.trash /></x-slot:icon> Stornieren</x-ui.dropdown-item>
                                    </x-ui.dropdown>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th scope="row" colspan="4">Summe</th>
                            <td class="num">135,00 €</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </x-ui.table>

                <x-ui.table label="Rabattcodes (leer)" dense>
                    <thead>
                        <tr>
                            <th scope="col">Code</th>
                            <th scope="col" class="num">Rabatt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-transparent">
                            <td colspan="2" class="p-0">
                                <x-ui.empty :bordered="false" action-href="#tabelle"
                                    action-label="Rabattcode anlegen">Es gibt noch keine Rabattcodes.</x-ui.empty>
                            </td>
                        </tr>
                    </tbody>
                </x-ui.table>
            </div>
        </x-ui.section>

        {{-- Seitenkopf, Abschnitt, Leer --}}
        <x-ui.section title="Seitenkopf, Abschnitt, Leer" id="kopf">
            <div class="flex flex-col gap-10">
                <div class="rounded-box border border-dashed border-line p-6">
                    <x-ui.page-header title="Veranstaltungen verwalten"
                        subtitle="Alle Veranstaltungen Ihrer Organisation, auch Entwürfe." back-href="#kopf"
                        back-label="Zur Übersicht" class="mb-0">
                        <x-slot:actions>
                            <x-ui.button variant="secondary">Exportieren</x-ui.button>
                            <x-ui.button href="#kopf">
                                <x-slot:icon><x-icon.plus /></x-slot:icon>
                                Veranstaltung anlegen
                            </x-ui.button>
                        </x-slot:actions>
                    </x-ui.page-header>
                </div>
                <div class="rounded-box border border-dashed border-line p-6">
                    <x-ui.section title="Kommende Veranstaltungen" id="sg-section-demo" link-href="#kopf"
                        link-label="Alle Veranstaltungen" level="h3">
                        <p class="m-0 text-muted">Inhalt des Abschnitts.</p>
                    </x-ui.section>
                </div>
                <x-ui.empty title="Noch keine Buchungen" action-href="#kopf" action-label="Veranstaltungen entdecken"
                    action-variant="primary">
                    Sobald Sie eine Veranstaltung buchen, erscheint sie hier.
                </x-ui.empty>
            </div>
        </x-ui.section>

        {{-- Zone und Kasten --}}
        <x-ui.section title="Zone und Kasten" id="flaechen">
            <div class="flex flex-col gap-6">
                <x-ui.box tone="zone" padding="lg" class="flex flex-wrap items-start gap-x-16 gap-y-6">
                    <div class="flex flex-[1_1_280px] flex-col gap-3">
                        <span class="inline-flex items-center gap-2 text-small font-medium text-ink-soft"><span
                                aria-hidden="true" class="h-2 w-2 rounded-full bg-leaf"></span>Schwerpunkt</span>
                        <p class="m-0 text-[clamp(26px,3vw,36px)] leading-[1.15] font-semibold tracking-[-0.02em]">
                            Hauptfach Mensch</p>
                    </div>
                    <div class="flex flex-[2_1_400px] flex-col items-start gap-4">
                        <p class="m-0 max-w-form text-[18px] leading-[1.55]">&lt;x-ui.box tone="zone"
                            padding="lg"&gt; – Kasten auf Zonen-Fläche für Schwerpunkte.</p>
                        <x-ui.button variant="link" size="lg" href="#flaechen" arrow>Zur
                            Themenreihe</x-ui.button>
                    </div>
                </x-ui.box>
                <x-ui.box class="flex flex-wrap items-center justify-between gap-x-12 gap-y-6">
                    <div class="flex flex-[1_1_320px] flex-col gap-1">
                        <p class="m-0 text-h2-form">Sie bieten selbst Fortbildungen an?</p>
                        <p class="m-0 text-muted">&lt;x-ui.box&gt; (weiß, Polster 24 px) als Aufrufzeile.</p>
                    </div>
                    <x-ui.button variant="secondary" size="lg" href="#flaechen" arrow>Veranstaltung
                        anlegen</x-ui.button>
                </x-ui.box>
                <x-ui.box padding="sm" tone="zone">
                    <p class="m-0 text-small">padding="sm" (16 px)</p>
                </x-ui.box>
                <x-ui.zone class="rounded-box border-t" border="bottom">
                    <p class="m-0 py-6 text-small text-muted">&lt;x-ui.zone&gt; ist eigentlich vollbreit (Einstieg,
                        Fuß); hier zur Ansicht eingerahmt.</p>
                </x-ui.zone>
            </div>
        </x-ui.section>

        {{-- Dialog, Menü, Reiter --}}
        <x-ui.section title="Dialog, Menü, Reiter" id="menues">
            <div class="flex flex-col gap-8">
                <div class="flex flex-wrap items-start gap-4">
                    <x-ui.modal name="sg-delete" title="Veranstaltung löschen?">
                        <x-slot:trigger>
                            <x-ui.button variant="danger">Veranstaltung löschen</x-ui.button>
                        </x-slot:trigger>
                        <p class="m-0">„Beziehung vor Stoff“ wird mit allen Ticketarten gelöscht. Das lässt sich
                            nicht rückgängig machen.</p>
                        <x-slot:footer>
                            <x-ui.button variant="secondary" x-on:click="close()">Abbrechen</x-ui.button>
                            <x-ui.button variant="danger" x-on:click="close()">Endgültig löschen</x-ui.button>
                        </x-slot:footer>
                    </x-ui.modal>

                    <x-ui.button variant="secondary" x-data x-on:click="$dispatch('open-modal', 'sg-info')">Dialog per
                        Ereignis öffnen</x-ui.button>
                    <x-ui.modal name="sg-info" title="Teilnahmezertifikat">
                        <x-ui.field label="Name auf dem Zertifikat" name="sg_modal_name" value="Maria Muster" />
                        <x-slot:footer>
                            <x-ui.button variant="secondary"
                                x-on:click="$dispatch('close-modal', 'sg-info')">Abbrechen</x-ui.button>
                            <x-ui.button x-on:click="$dispatch('close-modal', 'sg-info')">Speichern</x-ui.button>
                        </x-slot:footer>
                    </x-ui.modal>

                    <x-ui.dropdown size="md" align="left">
                        <x-ui.dropdown-item href="#menues"><x-slot:icon><x-icon.edit /></x-slot:icon>
                            Bearbeiten</x-ui.dropdown-item>
                        <x-ui.dropdown-item><x-slot:icon><x-icon.copy /></x-slot:icon>
                            Duplizieren</x-ui.dropdown-item>
                        <x-ui.dropdown-item danger><x-slot:icon><x-icon.trash /></x-slot:icon>
                            Löschen</x-ui.dropdown-item>
                    </x-ui.dropdown>
                </div>

                <x-ui.tabs label="Bereiche der Veranstaltung">
                    <x-ui.tab href="#menues" active>Übersicht</x-ui.tab>
                    <x-ui.tab href="#menues" :count="24">Buchungen</x-ui.tab>
                    <x-ui.tab href="#menues" :count="3">Warteliste</x-ui.tab>
                    <x-ui.tab href="#menues">Ticketarten</x-ui.tab>
                    <x-ui.tab href="#menues">Rabattcodes</x-ui.tab>
                    <x-ui.tab href="#menues">Check-in</x-ui.tab>
                </x-ui.tabs>
            </div>
        </x-ui.section>

        {{-- Kennzahlen und Detail-Paare --}}
        <x-ui.section title="Kennzahlen und Detail-Paare" id="kennzahlen">
            <div class="flex flex-col gap-10">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <x-ui.stat label="Kommende Veranstaltungen" :value="$events->count()" />
                    <x-ui.stat label="Buchungen (30 Tage)" value="128" sub="davon 6 offen" />
                    <x-ui.stat label="Umsatz (30 Tage)" value="5.760,00 €" />
                    <x-ui.stat label="Warteliste" value="3" sub="Zur Warteliste" href="#kennzahlen" />
                </div>
                <x-ui.dl class="max-w-prose">
                    <x-ui.dl-item label="Buchungsnummer" num>BK-6735A1F2</x-ui.dl-item>
                    <x-ui.dl-item label="Veranstaltung">Beziehung vor Stoff: Klassenführung, die trägt</x-ui.dl-item>
                    <x-ui.dl-item label="Datum" num>Do, 26.11.2026, 14:00–17:30 Uhr</x-ui.dl-item>
                    <x-ui.dl-item label="Status"><x-ui.status tone="success">Bestätigt</x-ui.status></x-ui.dl-item>
                    <x-ui.dl-item label="Telefon"></x-ui.dl-item>
                </x-ui.dl>
                <x-ui.dl layout="stack">
                    <x-ui.dl-item label="Format">Präsenz</x-ui.dl-item>
                    <x-ui.dl-item label="Ort">Radebeul, Ev. Schulzentrum</x-ui.dl-item>
                    <x-ui.dl-item label="Preis" num>45,00 €</x-ui.dl-item>
                </x-ui.dl>
            </div>
        </x-ui.section>

        {{-- Seitennavigation --}}
        <x-ui.section title="Seitennavigation" id="seiten"
            description="Veröffentlichte Laravel-Vorlagen (vendor/pagination) und ui.pagination.">
            <div class="flex flex-col gap-8">
                <x-ui.pagination :paginator="$paginated" class="mt-0" />
                {{ $paginated->links('pagination::simple-tailwind') }}
            </div>
        </x-ui.section>

        {{-- Icons --}}
        <x-ui.section title="Icons" id="icons"
            description="Linien-Icons unter components/icon (viewBox 24, Strich 1,6). Ältere Icons mit Strich 2 werden beim Umbau angeglichen.">
            <ul class="m-0 grid list-none grid-cols-[repeat(auto-fill,minmax(150px,1fr))] gap-x-4 gap-y-3 p-0">
                @foreach ($icons as $icon)
                    <li class="flex items-center gap-3 text-small">
                        <x-dynamic-component :component="'icon.' . $icon" class="h-5 w-5 flex-none" />
                        <span class="min-w-0 truncate">{{ $icon }}</span>
                    </li>
                @endforeach
            </ul>
        </x-ui.section>
    </main>

    <x-ui.zone as="footer" border="top">
        <div class="flex flex-wrap items-center justify-between gap-4 py-8">
            <x-wordmark size="sm" />
            <span class="text-small text-muted">© {{ now()->year }} {{ config('app.name') }} · Styleguide</span>
        </div>
    </x-ui.zone>
</body>

</html>
