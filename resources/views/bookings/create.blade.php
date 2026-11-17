@php
    use App\Support\Ui;

    $onlyFreeTickets = $ticketTypes->isNotEmpty() && $ticketTypes->every(fn($t) => $t->price == 0);
    $isExternalInvoicing = $event->organization?->hasExternalInvoicing() ?? false;
    $singleTicketType = $ticketTypes->count() === 1;

    // Freie Plätze für die Metazeile (wie in der Listenzeile): Veranstaltungskontingent oder Summe der Ticketkontingente
    $freePlaces = $event->max_attendees
        ? $event->availableTickets()
        : ($ticketTypes->isNotEmpty() && $ticketTypes->every(fn($t) => $t->quantity !== null)
            ? $ticketTypes->sum(fn($t) => $t->availableQuantity())
            : null);
@endphp

<x-layouts.public :title="($onlyFreeTickets ? 'Anmelden - ' : 'Tickets buchen - ') . $event->title">
    <div class="mx-auto max-w-page px-page pt-4 pb-[clamp(56px,7vw,96px)]">
        <x-ui.page-header :title="$onlyFreeTickets ? 'Anmelden' : 'Tickets buchen'" :back-href="route('events.show', $event->slug)" back-label="Zur Veranstaltung" />

        @if ($waitlistClaim ?? null)
            <x-ui.alert tone="success" title="Für Sie reserviert" class="mb-6">
                <p>
                    {{ $waitlistClaim->quantity }} {{ $waitlistClaim->quantity === 1 ? 'Platz' : 'Plätze' }}
                    bis {{ $waitlistClaim->expires_at->format('d.m.Y H:i') }} Uhr. Bitte schließen Sie die Buchung bis
                    dahin ab.
                </p>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('bookings.store', $event) }}" id="booking-form" data-recaptcha
            data-recaptcha-action="booking" novalidate
            class="m-0 grid items-start gap-10 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between lg:gap-x-12">
            @csrf
            @if ($waitlistClaim ?? null)
                <input type="hidden" name="waitlist_token" value="{{ $waitlistClaim->claim_token }}">
            @endif

            <div class="min-w-0">
                {{-- Gebuchte Veranstaltung --}}
                <x-ui.box tone="zone" padding="sm" class="mb-2 flex items-center gap-x-5 gap-y-4">
                    <x-ui.date-block :date="$event->start_date" :end="$event->end_date" tone="white" />
                    <div class="flex min-w-0 flex-col gap-1">
                        @if ($event->category)
                            <span class="text-small text-muted">{{ $event->category->name }}</span>
                        @endif
                        <span class="text-h3">{{ $event->title }}</span>
                        <span
                            class="flex flex-wrap items-center gap-x-4 gap-y-0.5 text-small text-ink-soft tabular-nums">
                            <span>{{ Ui::dateTime($event->start_date, $event->end_date) }}</span>
                            <span>{{ Ui::formatAndPlace($event) }}</span>
                            @if ($ticketTypes->isNotEmpty())
                                <x-ui.status
                                    tone="free">{{ $freePlaces !== null ? 'noch ' . $freePlaces . ' frei' : 'Plätze frei' }}</x-ui.status>
                            @endif
                        </span>
                    </div>
                </x-ui.box>

                {{-- 1. Plätze --}}
                <section aria-labelledby="schritt-1" class="py-8">
                    <x-ui.step-heading number="1" id="schritt-1" class="mb-2">
                        {{ $onlyFreeTickets ? 'Plätze' : 'Tickets' }} wählen
                    </x-ui.step-heading>

                    @if ($ticketTypes->isEmpty())
                        <x-ui.alert tone="warning" class="mt-4">Für diese Veranstaltung sind derzeit keine Tickets
                            verfügbar.</x-ui.alert>
                    @else
                        <div id="ticket-selection">
                            @foreach ($ticketTypes as $index => $ticketType)
                                @php $max = min($ticketType->availableQuantity(), $ticketType->max_per_order ?: 10); @endphp
                                <div class="ticket-type flex flex-wrap items-center justify-between gap-x-6 gap-y-3 border-b border-line py-4"
                                    data-price="{{ $ticketType->price }}" data-id="{{ $ticketType->id }}"
                                    data-name="{{ $ticketType->name }}">
                                    <div class="flex min-w-0 flex-[1_1_200px] flex-col gap-0.5">
                                        <span class="text-base font-semibold">{{ $ticketType->name }}</span>
                                        <span class="text-ui text-muted tabular-nums">
                                            {{ $ticketType->price > 0 ? Ui::money($ticketType->price) . ' pro Person' : 'kostenfrei' }}
                                        </span>
                                        @if ($ticketType->description)
                                            <span class="text-small text-muted">{{ $ticketType->description }}</span>
                                        @endif
                                        @if ($ticketType->quantity && $ticketType->availableQuantity() <= 10)
                                            <span class="text-small text-ink-soft">Nur noch
                                                {{ $ticketType->availableQuantity() }} verfügbar</span>
                                        @endif
                                    </div>
                                    <x-ui.stepper :name="'tickets[' . ($index) . '][quantity]'"
                                        :id="'ticket_' . ($ticketType->id)" :label="$ticketType->name" :value="$singleTicketType ? max(1, $ticketType->min_per_order) : 0"
                                        :max="$max" input-class="quantity-input"
                                        :data-ticket="($ticketType->id)" />
                                    <input type="hidden" name="tickets[{{ $index }}][ticket_type_id]"
                                        value="{{ $ticketType->id }}">
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-4 mb-0 text-small text-muted">Sie buchen für mehrere Personen? Die Namen der
                            Teilnehmenden tragen Sie nach der Buchung ein – jede Person kann ihr Ticket dann direkt per
                            E-Mail erhalten.</p>
                    @endif
                </section>

                {{-- 2. Kontaktdaten --}}
                <section aria-labelledby="schritt-2" class="border-t border-line py-8">
                    <x-ui.step-heading number="2" id="schritt-2" aside="* Pflichtfeld" class="mb-6">Ihre
                        Daten</x-ui.step-heading>
                    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.field label="Name" name="customer_name" :value="auth()->user()?->fullName()" required
                            autocomplete="name" />
                        <x-ui.field label="E-Mail" name="customer_email" type="email" :value="auth()->user()->email ?? ''" required
                            autocomplete="email" inputmode="email"
                            :hint="'Hierhin senden wir Bestätigung' . ($onlyFreeTickets ? '' : ', Rechnung') . ' und Tickets bzw. Zugangsdaten.'" />
                        <x-ui.field label="Telefon" name="customer_phone" type="tel" optional autocomplete="tel"
                            inputmode="tel" />
                        @if ($event->showsOrganizationField())
                            <x-ui.field label="Organisation / Einrichtung" name="customer_organization"
                                :required="$event->requiresOrganizationField()" :optional="!$event->requiresOrganizationField()" autocomplete="organization"
                                placeholder="z. B. Evangelische Grundschule Dresden" />
                        @endif
                    </div>
                </section>

                {{-- 3. Rechnung & Zahlung (nur kostenpflichtig) --}}
                @unless ($onlyFreeTickets)
                    <section aria-labelledby="schritt-3" class="border-t border-line pt-8">
                        <x-ui.step-heading number="3" id="schritt-3" class="mb-6">Rechnung &amp;
                            Zahlung</x-ui.step-heading>

                        <div class="flex flex-col gap-5">
                            <x-ui.field label="Rechnungsempfänger (Schule, Träger, Firma)" name="billing_company" optional
                                :value="auth()->user()->billing_company ?? ''" autocomplete="organization" />
                            <x-ui.field label="Straße und Hausnummer" name="billing_address" required
                                autocomplete="street-address" />
                            <div class="flex flex-wrap gap-x-4 gap-y-5">
                                <x-ui.field label="PLZ" name="billing_postal_code" required autocomplete="postal-code"
                                    inputmode="numeric" input-class="tabular-nums" class="max-w-40 flex-[1_1_112px]" />
                                <x-ui.field label="Ort" name="billing_city" required autocomplete="address-level2"
                                    class="min-w-0 flex-[3_1_200px]" />
                            </div>
                            <x-ui.select label="Land" name="billing_country" required autocomplete="country-name"
                                :options="[
                                    'Germany' => 'Deutschland',
                                    'Austria' => 'Österreich',
                                    'Switzerland' => 'Schweiz',
                                    'Other' => 'Anderes',
                                ]" selected="Germany" />

                            @if ($isExternalInvoicing)
                                <div class="mt-3">
                                    <x-ui.label as="span">Zahlungsmethode</x-ui.label>
                                    <x-ui.alert tone="info" title="Rechnung (Überweisung)">
                                        <p>
                                            @if ($event->releasesTicketsBeforeInvoice())
                                                Tickets bzw. Zugangsdaten erhalten Sie sofort nach der Buchung. Die
                                                Rechnung stellt Ihnen der Veranstalter separat zu – ggf. auch nach der
                                                Veranstaltung.
                                            @else
                                                Sie erhalten die Rechnung separat vom Veranstalter. Tickets bzw.
                                                Zugangsdaten folgen nach Rechnungsstellung.
                                            @endif
                                        </p>
                                    </x-ui.alert>
                                    <input type="hidden" name="payment_method" value="invoice">
                                </div>
                            @else
                                <fieldset class="m-0 mt-3 min-w-0 border-0 p-0">
                                    <x-ui.label as="legend" required class="mb-2">Zahlungsmethode</x-ui.label>
                                    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-3">
                                        <x-ui.choice name="payment_method" value="invoice" title="Rechnung" checked
                                            description="Rechnung mit Bankverbindung per E-Mail. Tickets bzw. Zugangsdaten nach Zahlungseingang." />
                                        @if ($event->organization?->hasPayPalConfigured())
                                            <x-ui.choice name="payment_method" value="paypal" title="PayPal"
                                                description="Sofort bezahlen – Tickets bzw. Zugangsdaten kommen direkt nach der Zahlung." />
                                        @endif
                                    </div>
                                    <x-ui.field-error name="payment_method" />
                                </fieldset>
                            @endif

                            <div class="mt-3">
                                <x-ui.label for="discount_code" optional>Rabattcode</x-ui.label>
                                <div class="flex max-w-[400px] flex-wrap gap-2">
                                    <x-ui.input name="discount_code" autocomplete="off" class="min-w-0 flex-[1_1_180px]"
                                        aria-describedby="discount-message" />
                                    <x-ui.button variant="secondary" size="lg"
                                        id="apply-discount">Einlösen</x-ui.button>
                                </div>
                                <x-ui.field-error name="discount_code" />
                                <div id="discount-message" class="mt-2 text-small" aria-live="polite"></div>
                            </div>
                        </div>
                    </section>
                @else
                    <input type="hidden" name="payment_method" value="invoice">
                @endunless
            </div>

            {{-- Zusammenfassung --}}
            <x-ui.summary id="summary-title">
                <x-slot:event>
                    <span>{{ $event->title }}</span>
                    <span>{{ Ui::dateTime($event->start_date, $event->end_date) }}</span>
                    <span>{{ Ui::formatAndPlace($event) }}</span>
                </x-slot:event>

                <x-ui.summary-lines>
                    <div id="order-summary" class="flex flex-col gap-2">
                        <p class="m-0 text-small text-muted">Bitte wählen Sie
                            {{ $onlyFreeTickets ? 'Plätze' : 'Tickets' }}
                            aus.</p>
                    </div>
                    <x-ui.summary-line id="subtotal-row" label="Zwischensumme" muted style="display: none;">
                        <span id="subtotal">0,00 €</span>
                    </x-ui.summary-line>
                    <x-ui.summary-line id="discount-row" label="Rabatt" muted style="display: none;">
                        <span id="discount">-0,00 €</span>
                    </x-ui.summary-line>
                </x-ui.summary-lines>

                <x-ui.summary-total>
                    <span id="total">{{ $onlyFreeTickets ? 'kostenfrei' : '–' }}</span>
                </x-ui.summary-total>

                <x-recaptcha action="booking" />

                @guest
                    <x-ui.checkbox name="privacy_accepted" id="privacy_accepted" required size="small">
                        Ich habe die <a href="{{ route('datenschutz') }}" target="_blank" rel="noopener"
                            class="font-medium underline underline-offset-[3px]">Datenschutzerklärung</a>
                        gelesen und stimme der Verarbeitung meiner Daten zur Buchungsabwicklung zu.
                    </x-ui.checkbox>
                @endguest

                <div class="flex flex-col gap-3">
                    <x-ui.button type="submit" id="submit-button" size="xl" full :disabled="true">
                        <span
                            id="submit-button-text">{{ $ticketTypes->isEmpty() ? 'Keine Tickets verfügbar' : ($onlyFreeTickets ? 'Verbindlich anmelden' : 'Kostenpflichtig buchen') }}</span>
                        <svg id="submit-spinner" class="hidden h-5 w-5 animate-spin"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </x-ui.button>
                    <p class="m-0 flex items-center justify-center gap-2 text-center text-small text-ink-soft">
                        <x-icon.mail class="h-4 w-4 flex-none" />
                        Sie erhalten eine Bestätigung per E-Mail.
                    </p>
                </div>
            </x-ui.summary>
        </form>
    </div>

    @push('scripts')
        <script>
            (function() {
                const euro = (v) => v.toFixed(2).replace('.', ',') + ' €';
                const onlyFree = @json($onlyFreeTickets);
                let subtotal = 0;
                let discountAmount = 0;
                let count = 0;

                function paymentMethod() {
                    const checked = document.querySelector('input[name="payment_method"]:checked');
                    return checked ? checked.value : (document.querySelector('input[name="payment_method"]')?.value ||
                        'invoice');
                }

                function updateSubmitButton() {
                    const text = document.getElementById('submit-button-text');
                    const total = subtotal - discountAmount;
                    // Ohne Auswahl bleibt bei kostenpflichtigen Veranstaltungen die kostenpflichtige Beschriftung.
                    if (onlyFree || (count > 0 && total <= 0)) {
                        text.textContent = 'Verbindlich anmelden';
                    } else if (paymentMethod() === 'paypal') {
                        text.textContent = 'Weiter zu PayPal';
                    } else {
                        text.textContent = 'Kostenpflichtig buchen';
                    }
                }

                function updateSummary() {
                    subtotal = 0;
                    count = 0;
                    const summary = document.getElementById('order-summary');
                    summary.innerHTML = '';

                    document.querySelectorAll('.ticket-type').forEach(el => {
                        const input = el.querySelector('.quantity-input');
                        const quantity = parseInt(input.value) || 0;
                        if (quantity <= 0) return;
                        const price = parseFloat(el.dataset.price);
                        subtotal += quantity * price;
                        count += quantity;
                        const row = document.createElement('div');
                        row.className = 'flex justify-between gap-4 text-ui tabular-nums';
                        const name = document.createElement('span');
                        name.className = 'min-w-0';
                        name.textContent = quantity + ' × ' + el.dataset.name;
                        const sum = document.createElement('span');
                        sum.className = 'whitespace-nowrap';
                        sum.textContent = price > 0 ? euro(quantity * price) : 'kostenfrei';
                        row.append(name, sum);
                        summary.appendChild(row);
                    });

                    if (count === 0) {
                        summary.innerHTML =
                            '<p class="m-0 text-small text-muted">Bitte wählen Sie aus, wie viele Plätze Sie buchen möchten.</p>';
                    }

                    const total = Math.max(0, subtotal - discountAmount);
                    document.getElementById('subtotal').textContent = euro(subtotal);
                    // Ohne Auswahl: "–" statt "kostenfrei", damit eine kostenpflichtige Veranstaltung nicht kostenlos wirkt.
                    document.getElementById('total').textContent = count === 0 && !onlyFree ? '–' : (total > 0 ? euro(
                        total) :
                        'kostenfrei');
                    document.getElementById('submit-button').disabled = count === 0;
                    updateSubmitButton();
                }

                // Die Mengenwahl (Stepper-Baustein) löst bei jedem Klick "input" am Feld aus.
                document.querySelectorAll('.quantity-input').forEach(input => input.addEventListener('input',
                    updateSummary));
                document.querySelectorAll('input[name="payment_method"]').forEach(r => r.addEventListener('change',
                    updateSubmitButton));

                document.getElementById('booking-form').addEventListener('submit', function(e) {
                    const selected = Array.from(document.querySelectorAll('.quantity-input')).some(i => (parseInt(i
                        .value) || 0) > 0);
                    if (!selected) {
                        e.preventDefault();
                        alert('Bitte wählen Sie mindestens einen Platz aus.');
                        return;
                    }
                    // Browser-Validierung trotz novalidate (für eigene Fehlermeldung beim Ticket-Check) ausführen
                    if (!this.checkValidity()) {
                        e.preventDefault();
                        this.reportValidity();
                        return;
                    }
                    // Nicht ausgewählte Ticketarten nicht mitsenden
                    document.querySelectorAll('.ticket-type').forEach(el => {
                        if ((parseInt(el.querySelector('.quantity-input').value) || 0) === 0) {
                            el.querySelectorAll('input').forEach(i => i.disabled = true);
                        }
                    });
                    document.getElementById('submit-button').disabled = true;
                    document.getElementById('submit-spinner').classList.remove('hidden');
                    document.getElementById('submit-button-text').textContent = 'Wird verarbeitet…';
                });

                const discountButton = document.getElementById('apply-discount');
                discountButton?.addEventListener('click', async function() {
                    const code = document.getElementById('discount_code').value.trim();
                    const message = document.getElementById('discount-message');
                    const show = (text, ok) => {
                        message.className = 'mt-2 text-small ' + (ok ? 'text-success' : 'text-danger');
                        message.textContent = text;
                    };

                    if (!code) return show('Bitte geben Sie einen Rabattcode ein.', false);
                    if (subtotal === 0) return show('Bitte wählen Sie zuerst Tickets aus.', false);

                    try {
                        const response = await fetch(@json(route('api.validate-discount-code')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token())
                            },
                            body: JSON.stringify({
                                code,
                                event_id: {{ $event->id }},
                                subtotal
                            }),
                        });
                        const data = await response.json();
                        if (data.valid) {
                            discountAmount = parseFloat(data.discount) || 0;
                            document.getElementById('discount').textContent = '-' + euro(discountAmount);
                            document.getElementById('discount-row').style.display = 'flex';
                            document.getElementById('subtotal-row').style.display = 'flex';
                            show(data.message || ('Rabatt angewendet: ' + data.discount_formatted), true);
                        } else {
                            discountAmount = 0;
                            document.getElementById('discount-row').style.display = 'none';
                            document.getElementById('subtotal-row').style.display = 'none';
                            show(data.message || 'Dieser Rabattcode ist ungültig.', false);
                        }
                        updateSummary();
                    } catch (error) {
                        show('Der Rabattcode konnte nicht geprüft werden. Bitte versuchen Sie es erneut.',
                            false);
                    }
                });

                updateSummary();
            })();
        </script>
    @endpush
</x-layouts.public>
