@php
    $onlyFreeTickets = $ticketTypes->isNotEmpty() && $ticketTypes->every(fn ($t) => $t->price == 0);
    $isExternalInvoicing = $event->organization?->hasExternalInvoicing() ?? false;
    $singleTicketType = $ticketTypes->count() === 1;
    $input = 'w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base';
    $card = 'bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6';
    $label = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1';
@endphp

<x-layouts.public :title="($onlyFreeTickets ? 'Anmelden - ' : 'Tickets buchen - ') . $event->title">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4 sm:py-8 pb-28 lg:pb-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('events.show', $event->slug) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center">
                    <x-icon.arrow-left class="w-4 h-4 mr-1" /> Zurück zur Veranstaltung
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 mt-3">{{ $onlyFreeTickets ? 'Anmelden' : 'Tickets buchen' }}</h1>
                <p class="text-gray-700 dark:text-gray-300 mt-1 font-medium">{{ $event->title }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $event->start_date->translatedFormat('D, d.m.Y · H:i') }} Uhr
                    @if($event->isOnline()) · Online @elseif($event->venue_city) · {{ $event->venue_city }} @endif
                </p>
            </div>

            @foreach(['error' => 'bg-red-50 border-red-300 text-red-800', 'warning' => 'bg-yellow-50 border-yellow-300 text-yellow-800'] as $key => $classes)
                @if(session($key))
                    <div class="{{ $classes }} border rounded-lg px-4 py-3 mb-4 text-sm" role="alert">{{ session($key) }}</div>
                @endif
            @endforeach

            @if($waitlistClaim ?? null)
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-300 dark:border-green-800 text-green-900 dark:text-green-100 rounded-lg px-4 py-3 mb-4 text-sm">
                    <strong>Für Sie reserviert:</strong> {{ $waitlistClaim->quantity }} {{ $waitlistClaim->quantity === 1 ? 'Platz' : 'Plätze' }}
                    bis {{ $waitlistClaim->expires_at->format('d.m.Y H:i') }} Uhr. Bitte schließen Sie die Buchung bis dahin ab.
                </div>
            @endif

            <form method="POST" action="{{ route('bookings.store', $event) }}" id="booking-form" data-recaptcha data-recaptcha-action="booking" novalidate>
                @csrf
                @if($waitlistClaim ?? null)
                    <input type="hidden" name="waitlist_token" value="{{ $waitlistClaim->claim_token }}">
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                    <div class="lg:col-span-2 space-y-4">
                        {{-- 1. Plätze --}}
                        <section class="{{ $card }}">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">1. {{ $onlyFreeTickets ? 'Plätze' : 'Tickets' }} wählen</h2>

                            @if($ticketTypes->isEmpty())
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800 text-sm">
                                    Für diese Veranstaltung sind derzeit keine Tickets verfügbar.
                                </div>
                            @else
                                <div class="space-y-3" id="ticket-selection">
                                    @foreach($ticketTypes as $index => $ticketType)
                                        @php $max = min($ticketType->availableQuantity(), $ticketType->max_per_order ?: 10); @endphp
                                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 sm:p-4 ticket-type" data-price="{{ $ticketType->price }}" data-id="{{ $ticketType->id }}" data-name="{{ $ticketType->name }}">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $ticketType->name }}</h3>
                                                    @if($ticketType->description)
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $ticketType->description }}</p>
                                                    @endif
                                                    @if($ticketType->quantity && $ticketType->availableQuantity() <= 10)
                                                        <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">Nur noch {{ $ticketType->availableQuantity() }} verfügbar</p>
                                                    @endif
                                                </div>
                                                <div class="text-right shrink-0 font-bold text-blue-700 dark:text-blue-400">
                                                    {{ $ticketType->price > 0 ? number_format($ticketType->price, 2, ',', '.') . ' €' : 'kostenfrei' }}
                                                </div>
                                            </div>
                                            <div class="mt-3 flex items-center justify-between">
                                                <label for="ticket_{{ $ticketType->id }}" class="text-sm text-gray-700 dark:text-gray-300">Anzahl</label>
                                                <div class="flex items-center">
                                                    <button type="button" class="quantity-minus h-11 w-11 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded-l-lg text-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-gray-100" data-ticket="{{ $ticketType->id }}" aria-label="Weniger">−</button>
                                                    <input type="number" inputmode="numeric"
                                                           id="ticket_{{ $ticketType->id }}"
                                                           name="tickets[{{ $index }}][quantity]"
                                                           value="{{ old('tickets.' . $index . '.quantity', $singleTicketType ? max(1, $ticketType->min_per_order) : 0) }}"
                                                           min="0" max="{{ $max }}"
                                                           class="quantity-input h-11 w-14 text-center border-y border-x-0 border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:outline-none"
                                                           data-ticket="{{ $ticketType->id }}">
                                                    <button type="button" class="quantity-plus h-11 w-11 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded-r-lg text-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:text-gray-100" data-ticket="{{ $ticketType->id }}" data-max="{{ $max }}" aria-label="Mehr">+</button>
                                                </div>
                                                <input type="hidden" name="tickets[{{ $index }}][ticket_type_id]" value="{{ $ticketType->id }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">Sie buchen für mehrere Personen? Die Namen der Teilnehmenden tragen Sie nach der Buchung ein – jede Person kann ihr Ticket dann direkt per E-Mail erhalten.</p>
                            @endif
                        </section>

                        {{-- 2. Kontaktdaten --}}
                        <section class="{{ $card }}">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">2. Ihre Daten</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label for="customer_name" class="{{ $label }}">Name *</label>
                                    <input type="text" id="customer_name" name="customer_name" required autocomplete="name"
                                           value="{{ old('customer_name', auth()->user()->name ?? '') }}" class="{{ $input }}">
                                    @error('customer_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="customer_email" class="{{ $label }}">E-Mail *</label>
                                    <input type="email" id="customer_email" name="customer_email" required autocomplete="email" inputmode="email"
                                           value="{{ old('customer_email', auth()->user()->email ?? '') }}" class="{{ $input }}">
                                    @error('customer_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hierhin senden wir Bestätigung{{ $onlyFreeTickets ? '' : ', Rechnung' }} und Tickets bzw. Zugangsdaten.</p>
                                </div>
                                <div>
                                    <label for="customer_phone" class="{{ $label }}">Telefon <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <input type="tel" id="customer_phone" name="customer_phone" autocomplete="tel" inputmode="tel"
                                           value="{{ old('customer_phone') }}" class="{{ $input }}">
                                    @error('customer_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                @if($event->showsOrganizationField())
                                    <div class="sm:col-span-2">
                                        <label for="customer_organization" class="{{ $label }}">
                                            Organisation / Einrichtung
                                            @if($event->requiresOrganizationField())<span class="text-red-500">*</span>@else<span class="text-gray-400 font-normal">(optional)</span>@endif
                                        </label>
                                        <input type="text" id="customer_organization" name="customer_organization" autocomplete="organization"
                                               value="{{ old('customer_organization') }}" placeholder="z.B. Evangelische Grundschule Dresden"
                                               @if($event->requiresOrganizationField()) required @endif class="{{ $input }}">
                                        @error('customer_organization')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                @endif
                            </div>
                        </section>

                        {{-- 3. Rechnung & Zahlung (nur kostenpflichtig) --}}
                        @unless($onlyFreeTickets)
                            <section class="{{ $card }}">
                                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">3. Rechnung & Zahlung</h2>
                                <div class="grid grid-cols-1 sm:grid-cols-6 gap-4">
                                    <div class="sm:col-span-6">
                                        <label for="billing_company" class="{{ $label }}">Rechnungsempfänger (Schule, Träger, Firma) <span class="text-gray-400 font-normal">(optional)</span></label>
                                        <input type="text" id="billing_company" name="billing_company" autocomplete="organization"
                                               value="{{ old('billing_company', auth()->user()->billing_company ?? '') }}" class="{{ $input }}">
                                        @error('billing_company')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="sm:col-span-6">
                                        <label for="billing_address" class="{{ $label }}">Straße und Hausnummer *</label>
                                        <input type="text" id="billing_address" name="billing_address" required autocomplete="street-address"
                                               value="{{ old('billing_address') }}" class="{{ $input }}">
                                        @error('billing_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="billing_postal_code" class="{{ $label }}">PLZ *</label>
                                        <input type="text" id="billing_postal_code" name="billing_postal_code" required autocomplete="postal-code" inputmode="numeric"
                                               value="{{ old('billing_postal_code') }}" class="{{ $input }}">
                                        @error('billing_postal_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="sm:col-span-4">
                                        <label for="billing_city" class="{{ $label }}">Ort *</label>
                                        <input type="text" id="billing_city" name="billing_city" required autocomplete="address-level2"
                                               value="{{ old('billing_city') }}" class="{{ $input }}">
                                        @error('billing_city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="sm:col-span-6">
                                        <label for="billing_country" class="{{ $label }}">Land *</label>
                                        <select id="billing_country" name="billing_country" required autocomplete="country-name" class="{{ $input }}">
                                            @foreach(['Germany' => 'Deutschland', 'Austria' => 'Österreich', 'Switzerland' => 'Schweiz', 'Other' => 'Anderes'] as $value => $name)
                                                <option value="{{ $value }}" @selected(old('billing_country', 'Germany') === $value)>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-6 mb-2">Zahlungsmethode</h3>
                                @if($isExternalInvoicing)
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-sm text-gray-800 dark:text-gray-200">
                                        <p class="font-medium">Rechnung (Überweisung)</p>
                                        <p class="mt-1">
                                            @if($event->releasesTicketsBeforeInvoice())
                                                Tickets bzw. Zugangsdaten erhalten Sie sofort nach der Buchung. Die Rechnung stellt Ihnen der Veranstalter separat zu – ggf. auch nach der Veranstaltung.
                                            @else
                                                Sie erhalten die Rechnung separat vom Veranstalter. Tickets bzw. Zugangsdaten folgen nach Rechnungsstellung.
                                            @endif
                                        </p>
                                    </div>
                                    <input type="hidden" name="payment_method" value="invoice">
                                @else
                                    <div class="space-y-2">
                                        <label class="flex items-start gap-3 p-3 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/20 transition">
                                            <input type="radio" name="payment_method" value="invoice" @checked(old('payment_method', 'invoice') === 'invoice') class="mt-1 w-4 h-4 text-blue-600">
                                            <span>
                                                <span class="block font-medium text-gray-900 dark:text-gray-100">Rechnung</span>
                                                <span class="block text-sm text-gray-600 dark:text-gray-400">Rechnung mit Bankverbindung per E-Mail. Tickets bzw. Zugangsdaten nach Zahlungseingang.</span>
                                            </span>
                                        </label>
                                        @if($event->organization?->hasPayPalConfigured())
                                            <label class="flex items-start gap-3 p-3 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/20 transition">
                                                <input type="radio" name="payment_method" value="paypal" @checked(old('payment_method') === 'paypal') class="mt-1 w-4 h-4 text-blue-600">
                                                <span>
                                                    <span class="block font-medium text-gray-900 dark:text-gray-100">PayPal</span>
                                                    <span class="block text-sm text-gray-600 dark:text-gray-400">Sofort bezahlen – Tickets bzw. Zugangsdaten kommen direkt nach der Zahlung.</span>
                                                </span>
                                            </label>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-6">
                                    <label for="discount_code" class="{{ $label }}">Rabattcode <span class="text-gray-400 font-normal">(optional)</span></label>
                                    <div class="flex gap-2">
                                        <input type="text" id="discount_code" name="discount_code" value="{{ old('discount_code') }}" autocomplete="off" class="{{ $input }} flex-1">
                                        <button type="button" id="apply-discount" class="px-4 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 font-medium">Anwenden</button>
                                    </div>
                                    <div id="discount-message" class="mt-2 text-sm" aria-live="polite"></div>
                                </div>
                            </section>
                        @else
                            <input type="hidden" name="payment_method" value="invoice">
                        @endunless
                    </div>

                    {{-- Zusammenfassung --}}
                    <aside class="lg:col-span-1" id="summary">
                        <div class="{{ $card }} lg:sticky lg:top-4">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">Zusammenfassung</h2>
                            <div id="order-summary" class="space-y-2 mb-3 text-sm">
                                <p class="text-gray-600 dark:text-gray-400">Bitte wählen Sie {{ $onlyFreeTickets ? 'Plätze' : 'Tickets' }} aus.</p>
                            </div>
                            <div class="border-t border-gray-100 dark:border-gray-700 pt-3 space-y-1 text-sm">
                                <div class="flex justify-between" id="subtotal-row"><span class="text-gray-600 dark:text-gray-400">Zwischensumme</span><span id="subtotal" class="text-gray-900 dark:text-gray-100">0,00 €</span></div>
                                <div class="flex justify-between" id="discount-row" style="display: none;"><span class="text-gray-600 dark:text-gray-400">Rabatt</span><span id="discount" class="text-green-700">-0,00 €</span></div>
                                <div class="flex justify-between text-base font-bold border-t border-gray-100 dark:border-gray-700 pt-2 text-gray-900 dark:text-gray-100"><span>Gesamt</span><span id="total">0,00 €</span></div>
                            </div>

                            <x-recaptcha action="booking" />

                            @guest
                                <label class="mt-4 flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="privacy_accepted" id="privacy_accepted" value="1" required @checked(old('privacy_accepted'))
                                           class="mt-1 h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 shrink-0">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        Ich habe die <a href="{{ route('datenschutz') }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline font-medium">Datenschutzerklärung</a>
                                        gelesen und stimme der Verarbeitung meiner Daten zur Buchungsabwicklung zu. <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                @error('privacy_accepted')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            @endguest

                            <button type="submit" id="submit-button" disabled
                                    class="w-full mt-5 px-6 py-3.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center">
                                <span id="submit-button-text">{{ $ticketTypes->isEmpty() ? 'Keine Tickets verfügbar' : ($onlyFreeTickets ? 'Verbindlich anmelden' : 'Kostenpflichtig buchen') }}</span>
                                <svg id="submit-spinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </button>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">* Pflichtfeld</p>
                        </div>
                    </aside>
                </div>
            </form>
        </div>

        {{-- Mobile: Summe und Sprung zum Abschluss immer sichtbar --}}
        @if($ticketTypes->isNotEmpty())
            <div class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white/95 dark:bg-gray-900/95 backdrop-blur border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400" id="mobile-count">Noch nichts ausgewählt</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100" id="mobile-total">0,00 €</p>
                </div>
                <a href="#summary" class="px-5 py-3 bg-blue-600 text-white rounded-lg font-semibold">Weiter</a>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        (function () {
            const euro = (v) => v.toFixed(2).replace('.', ',') + ' €';
            const onlyFree = @json($onlyFreeTickets);
            let subtotal = 0;
            let discountAmount = 0;

            function paymentMethod() {
                const checked = document.querySelector('input[name="payment_method"]:checked');
                return checked ? checked.value : (document.querySelector('input[name="payment_method"]')?.value || 'invoice');
            }

            function updateSubmitButton() {
                const text = document.getElementById('submit-button-text');
                const total = subtotal - discountAmount;
                if (onlyFree || total <= 0) {
                    text.textContent = 'Verbindlich anmelden';
                } else if (paymentMethod() === 'paypal') {
                    text.textContent = 'Weiter zu PayPal';
                } else {
                    text.textContent = 'Kostenpflichtig buchen';
                }
            }

            function updateSummary() {
                subtotal = 0;
                let count = 0;
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
                    row.className = 'flex justify-between';
                    const name = document.createElement('span');
                    name.className = 'text-gray-700 dark:text-gray-300';
                    name.textContent = quantity + '× ' + el.dataset.name;
                    const sum = document.createElement('span');
                    sum.className = 'font-medium text-gray-900 dark:text-gray-100';
                    sum.textContent = price > 0 ? euro(quantity * price) : 'kostenfrei';
                    row.append(name, sum);
                    summary.appendChild(row);
                });

                if (count === 0) {
                    summary.innerHTML = '<p class="text-gray-600 dark:text-gray-400">Bitte wählen Sie aus, wie viele Plätze Sie buchen möchten.</p>';
                }

                const total = Math.max(0, subtotal - discountAmount);
                document.getElementById('subtotal').textContent = euro(subtotal);
                document.getElementById('total').textContent = total > 0 ? euro(total) : 'kostenfrei';
                const mobileTotal = document.getElementById('mobile-total');
                if (mobileTotal) {
                    mobileTotal.textContent = total > 0 ? euro(total) : (count ? 'kostenfrei' : '0,00 €');
                    document.getElementById('mobile-count').textContent = count ? count + (count === 1 ? ' Platz ausgewählt' : ' Plätze ausgewählt') : 'Noch nichts ausgewählt';
                }
                document.getElementById('submit-button').disabled = count === 0;
                updateSubmitButton();
            }

            function step(ticketId, delta) {
                const input = document.getElementById('ticket_' + ticketId);
                const max = parseInt(input.max) || 10;
                input.value = Math.min(max, Math.max(0, (parseInt(input.value) || 0) + delta));
                updateSummary();
            }

            document.querySelectorAll('.quantity-minus').forEach(btn => btn.addEventListener('click', () => step(btn.dataset.ticket, -1)));
            document.querySelectorAll('.quantity-plus').forEach(btn => btn.addEventListener('click', () => step(btn.dataset.ticket, 1)));
            document.querySelectorAll('.quantity-input').forEach(input => input.addEventListener('input', updateSummary));
            document.querySelectorAll('input[name="payment_method"]').forEach(r => r.addEventListener('change', updateSubmitButton));

            document.getElementById('booking-form').addEventListener('submit', function (e) {
                const selected = Array.from(document.querySelectorAll('.quantity-input')).some(i => (parseInt(i.value) || 0) > 0);
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
            discountButton?.addEventListener('click', async function () {
                const code = document.getElementById('discount_code').value.trim();
                const message = document.getElementById('discount-message');
                const show = (text, ok) => { message.className = 'mt-2 text-sm ' + (ok ? 'text-green-700' : 'text-red-600'); message.textContent = text; };

                if (!code) return show('Bitte geben Sie einen Rabattcode ein.', false);
                if (subtotal === 0) return show('Bitte wählen Sie zuerst Tickets aus.', false);

                try {
                    const response = await fetch(@json(route('api.validate-discount-code')), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
                        body: JSON.stringify({ code, event_id: {{ $event->id }}, subtotal }),
                    });
                    const data = await response.json();
                    if (data.valid) {
                        discountAmount = parseFloat(data.discount) || 0;
                        document.getElementById('discount').textContent = '-' + euro(discountAmount);
                        document.getElementById('discount-row').style.display = 'flex';
                        show(data.message || ('Rabatt angewendet: ' + data.discount_formatted), true);
                    } else {
                        discountAmount = 0;
                        document.getElementById('discount-row').style.display = 'none';
                        show(data.message || 'Dieser Rabattcode ist ungültig.', false);
                    }
                    updateSummary();
                } catch (error) {
                    show('Der Rabattcode konnte nicht geprüft werden. Bitte versuchen Sie es erneut.', false);
                }
            });

            updateSummary();
        })();
    </script>
    @endpush
</x-layouts.public>
