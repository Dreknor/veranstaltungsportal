<x-layouts.public :title="'Tickets buchen - ' . $event->title">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('events.show', $event->slug) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    <x-icon.arrow-left class="w-4 h-4 mr-2" />
                    Zurück zur Veranstaltung
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Tickets buchen</h1>
                <p class="text-lg text-gray-600 mt-2">{{ $event->title }}</p>
            </div>

            <form method="POST" action="{{ route('bookings.store', $event) }}" id="booking-form" data-recaptcha data-recaptcha-action="booking">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Hauptformular -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Ticket-Auswahl -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Tickets auswählen</h2>

                            @if($ticketTypes->isEmpty())
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <p class="text-yellow-800">
                                        <strong>Hinweis:</strong> Für diese Veranstaltung sind derzeit keine Tickets verfügbar.
                                        Bitte kontaktieren Sie den Veranstalter oder versuchen Sie es später erneut.
                                    </p>
                                </div>
                            @else
                                <div class="space-y-4" id="ticket-selection">
                                    @foreach($ticketTypes as $index => $ticketType)
                                        <div class="border rounded-lg p-4 ticket-type" data-price="{{ $ticketType->price }}" data-id="{{ $ticketType->id }}">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex-1">
                                                    <h3 class="font-semibold text-gray-900">{{ $ticketType->name }}</h3>
                                                    @if($ticketType->description)
                                                        <p class="text-sm text-gray-600 mt-1">{{ $ticketType->description }}</p>
                                                    @endif
                                                    @if($ticketType->quantity)
                                                        <p class="text-xs text-gray-500 mt-1">Verfügbar: {{ $ticketType->availableQuantity() }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right ml-4">
                                                    <div class="text-xl font-bold text-blue-600">{{ number_format($ticketType->price, 2, ',', '.') }} €</div>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-4">
                                                <label for="ticket_{{ $ticketType->id }}" class="text-sm font-medium text-gray-700">Anzahl:</label>
                                                <div class="flex items-center">
                                                    <button type="button" class="quantity-minus px-3 py-1 border border-gray-300 rounded-l-lg hover:bg-gray-50" data-ticket="{{ $ticketType->id }}">-</button>
                                                    <input type="number"
                                                           id="ticket_{{ $ticketType->id }}"
                                                           name="tickets[{{ $index }}][quantity]"
                                                           value="0"
                                                           min="0"
                                                           max="{{ $ticketType->availableQuantity() }}"
                                                           class="quantity-input w-16 text-center border-t border-b border-gray-300 focus:outline-none"
                                                           data-ticket="{{ $ticketType->id }}">
                                                    <button type="button" class="quantity-plus px-3 py-1 border border-gray-300 rounded-r-lg hover:bg-gray-50" data-ticket="{{ $ticketType->id }}" data-max="{{ $ticketType->availableQuantity() }}">+</button>
                                                </div>
                                                <input type="hidden" name="tickets[{{ $index }}][ticket_type_id]" value="{{ $ticketType->id }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Kundendaten -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Ihre Daten</h2>

                            <div class="space-y-4">
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                    <input type="text" id="customer_name" name="customer_name" required
                                           value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('customer_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">E-Mail *</label>
                                    <input type="email" id="customer_email" name="customer_email" required
                                           value="{{ old('customer_email', auth()->user()->email ?? '') }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           @guest readonly onfocus="this.removeAttribute('readonly')" @endguest>
                                    @error('customer_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    @guest
                                        <p class="mt-1 text-xs text-gray-500">Sie erhalten eine Bestätigungs-E-Mail zur Verifizierung</p>
                                    @endguest
                                </div>

                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                    <input type="tel" id="customer_phone" name="customer_phone"
                                           value="{{ old('customer_phone') }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('customer_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Rechnungsadresse -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Rechnungsadresse</h2>

                            <div class="space-y-4">
                                <div>
                                    <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Straße und Hausnummer *</label>
                                    <input type="text" id="billing_address" name="billing_address" required
                                           value="{{ old('billing_address') }}"
                                           placeholder="z.B. Hauptstraße 123"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('billing_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 mb-1">PLZ *</label>
                                        <input type="text" id="billing_postal_code" name="billing_postal_code" required
                                               value="{{ old('billing_postal_code') }}"
                                               placeholder="12345"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('billing_postal_code')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="col-span-2">
                                        <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-1">Stadt *</label>
                                        <input type="text" id="billing_city" name="billing_city" required
                                               value="{{ old('billing_city') }}"
                                               placeholder="z.B. Berlin"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('billing_city')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-1">Land *</label>
                                    <select id="billing_country" name="billing_country" required
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="Germany" {{ old('billing_country', 'Germany') == 'Germany' ? 'selected' : '' }}>Deutschland</option>
                                        <option value="Austria" {{ old('billing_country') == 'Austria' ? 'selected' : '' }}>Österreich</option>
                                        <option value="Switzerland" {{ old('billing_country') == 'Switzerland' ? 'selected' : '' }}>Schweiz</option>
                                        <option value="Other" {{ old('billing_country') == 'Other' ? 'selected' : '' }}>Anderes</option>
                                    </select>
                                    @error('billing_country')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Rabattcode -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Rabattcode</h2>

                            <div class="flex gap-2">
                                <input type="text" id="discount_code" name="discount_code"
                                       placeholder="Rabattcode eingeben"
                                       value="{{ old('discount_code') }}"
                                       class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <button type="button" id="apply-discount" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                    Anwenden
                                </button>
                            </div>
                            <div id="discount-message" class="mt-2 text-sm"></div>
                        </div>
                    </div>

                    <!-- Zusammenfassung -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Zusammenfassung</h2>

                            <div id="order-summary" class="space-y-3 mb-4">
                                <p class="text-gray-600 text-sm">Bitte wählen Sie Tickets aus</p>
                            </div>

                            <div class="border-t pt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Zwischensumme:</span>
                                    <span id="subtotal" class="font-medium">0,00 €</span>
                                </div>
                                <div class="flex justify-between text-sm" id="discount-row" style="display: none;">
                                    <span class="text-gray-600">Rabatt:</span>
                                    <span id="discount" class="font-medium text-green-600">-0,00 €</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t pt-2">
                                    <span>Gesamt:</span>
                                    <span id="total">0,00 €</span>
                                </div>
                            </div>

                            <!-- reCAPTCHA -->
                            <x-recaptcha action="booking" />

                            <button type="submit" id="submit-button" {{ $ticketTypes->isEmpty() ? 'disabled' : 'disabled' }}
                                    class="w-full mt-6 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed">
                                {{ $ticketTypes->isEmpty() ? 'Keine Tickets verfügbar' : 'Kostenpflichtig buchen' }}
                            </button>

                            <p class="text-xs text-gray-500 mt-4 text-center">
                                Mit der Buchung akzeptieren Sie unsere AGB
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let subtotal = 0;
        let discountAmount = 0;

        // Form submit handler um nur ausgewählte Tickets zu senden
        document.getElementById('booking-form').addEventListener('submit', function(e) {
            // Deaktiviere alle Ticket-Inputs mit quantity = 0, damit sie nicht gesendet werden
            document.querySelectorAll('.ticket-type').forEach(ticketType => {
                const quantityInput = ticketType.querySelector('.quantity-input');
                const quantity = parseInt(quantityInput.value) || 0;

                if (quantity === 0) {
                    // Deaktiviere alle inputs in diesem Ticket-Container
                    ticketType.querySelectorAll('input').forEach(input => {
                        input.disabled = true;
                    });
                }
            });

            // Prüfe ob mindestens ein Ticket ausgewählt wurde
            const hasTickets = Array.from(document.querySelectorAll('.quantity-input:not([disabled])')).some(input => {
                return parseInt(input.value) > 0;
            });

            if (!hasTickets) {
                e.preventDefault();
                // Reaktiviere alle Inputs für weitere Versuche
                document.querySelectorAll('.ticket-type input').forEach(input => {
                    input.disabled = false;
                });
                alert('Bitte wählen Sie mindestens ein Ticket aus.');
                return false;
            }
        });

        function updateSummary() {
            subtotal = 0;
            const summaryDiv = document.getElementById('order-summary');
            summaryDiv.innerHTML = '';

            let hasTickets = false;

            document.querySelectorAll('.quantity-input').forEach(input => {
                const quantity = parseInt(input.value) || 0;
                if (quantity > 0) {
                    hasTickets = true;
                    const ticketType = input.closest('.ticket-type');
                    const price = parseFloat(ticketType.dataset.price);
                    const name = ticketType.querySelector('h3').textContent;
                    const total = quantity * price;
                    subtotal += total;

                    const item = document.createElement('div');
                    item.className = 'flex justify-between text-sm';
                    item.innerHTML = `
                        <span class="text-gray-700">${quantity}x ${name}</span>
                        <span class="font-medium">${total.toFixed(2).replace('.', ',')} €</span>
                    `;
                    summaryDiv.appendChild(item);
                }
            });

            if (!hasTickets) {
                summaryDiv.innerHTML = '<p class="text-gray-600 text-sm">Bitte wählen Sie Tickets aus</p>';
            }

            document.getElementById('subtotal').textContent = subtotal.toFixed(2).replace('.', ',') + ' €';

            const total = subtotal - discountAmount;
            document.getElementById('total').textContent = total.toFixed(2).replace('.', ',') + ' €';

            document.getElementById('submit-button').disabled = !hasTickets;
        }

        document.querySelectorAll('.quantity-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = document.querySelector(`#ticket_${this.dataset.ticket}`);
                const current = parseInt(input.value) || 0;
                if (current > 0) {
                    input.value = current - 1;
                    updateSummary();
                }
            });
        });

        document.querySelectorAll('.quantity-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = document.querySelector(`#ticket_${this.dataset.ticket}`);
                const current = parseInt(input.value) || 0;
                const max = parseInt(this.dataset.max);
                if (current < max) {
                    input.value = current + 1;
                    updateSummary();
                }
            });
        });

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', updateSummary);
        });

        document.getElementById('apply-discount').addEventListener('click', async function() {
            const code = document.getElementById('discount_code').value;
            const messageDiv = document.getElementById('discount-message');

            if (!code) {
                messageDiv.innerHTML = '<span class="text-red-600">Bitte geben Sie einen Rabattcode ein.</span>';
                return;
            }

            if (subtotal === 0) {
                messageDiv.innerHTML = '<span class="text-red-600">Bitte wählen Sie zuerst Tickets aus.</span>';
                return;
            }

            try {
                const response = await fetch('{{ route("api.validate-discount-code") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        code: code,
                        event_id: {{ $event->id }},
                        subtotal: subtotal
                    })
                });

                const data = await response.json();

                if (data.valid) {
                    discountAmount = data.discount;
                    document.getElementById('discount').textContent = '-' + discountAmount.toFixed(2).replace('.', ',') + ' €';
                    document.getElementById('discount-row').style.display = 'flex';
                    messageDiv.innerHTML = '<span class="text-green-600">' + data.message + '</span>';
                    updateSummary();
                } else {
                    discountAmount = 0;
                    document.getElementById('discount-row').style.display = 'none';
                    messageDiv.innerHTML = '<span class="text-red-600">' + data.message + '</span>';
                    updateSummary();
                }
            } catch (error) {
                messageDiv.innerHTML = '<span class="text-red-600">Fehler beim Validieren des Rabattcodes.</span>';
            }
        });
    </script>
    @endpush
</x-layouts.public>

