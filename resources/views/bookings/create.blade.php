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

                        <!-- Zahlungsmethode -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Zahlungsmethode</h2>

                            <div class="space-y-3">
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                    <input type="radio" name="payment_method" value="invoice" checked
                                           class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-gray-900">Rechnung</span>
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Sie erhalten eine Rechnung per E-Mail mit den Zahlungsinformationen.</p>
                                    </div>
                                </label>

                                @if($event->organization && $event->organization->hasPayPalConfigured())
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                    <input type="radio" name="payment_method" value="paypal"
                                           class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-gray-900">PayPal</span>
                                            <svg class="w-20 h-5" viewBox="0 0 124 33" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M46.211 6.749h-6.839a.95.95 0 0 0-.939.802l-2.766 17.537a.57.57 0 0 0 .564.658h3.265a.95.95 0 0 0 .939-.803l.746-4.73a.95.95 0 0 1 .938-.803h2.165c4.505 0 7.105-2.18 7.784-6.5.306-1.89.013-3.375-.872-4.415-.972-1.142-2.696-1.746-4.985-1.746zM47 13.154c-.374 2.454-2.249 2.454-4.062 2.454h-1.032l.724-4.583a.57.57 0 0 1 .563-.481h.473c1.235 0 2.4 0 3.002.704.359.42.469 1.044.332 1.906zM66.654 13.075h-3.275a.57.57 0 0 0-.563.481l-.145.916-.229-.332c-.709-1.029-2.29-1.373-3.868-1.373-3.619 0-6.71 2.741-7.312 6.586-.313 1.918.132 3.752 1.22 5.031.998 1.176 2.426 1.666 4.125 1.666 2.916 0 4.533-1.875 4.533-1.875l-.146.91a.57.57 0 0 0 .562.66h2.95a.95.95 0 0 0 .939-.803l1.77-11.209a.568.568 0 0 0-.561-.658zm-4.565 6.374c-.316 1.871-1.801 3.127-3.695 3.127-.951 0-1.711-.305-2.199-.883-.484-.574-.668-1.391-.514-2.301.295-1.855 1.805-3.152 3.67-3.152.93 0 1.686.309 2.184.892.499.589.697 1.411.554 2.317zM84.096 13.075h-3.291a.954.954 0 0 0-.787.417l-4.539 6.686-1.924-6.425a.953.953 0 0 0-.912-.678h-3.234a.57.57 0 0 0-.541.754l3.625 10.638-3.408 4.811a.57.57 0 0 0 .465.9h3.287a.949.949 0 0 0 .781-.408l10.946-15.8a.57.57 0 0 0-.468-.895z" fill="#253B80"/>
                                                <path d="M94.992 6.749h-6.84a.95.95 0 0 0-.938.802l-2.766 17.537a.569.569 0 0 0 .562.658h3.51a.665.665 0 0 0 .656-.562l.785-4.971a.95.95 0 0 1 .938-.803h2.164c4.506 0 7.105-2.18 7.785-6.5.307-1.89.012-3.375-.873-4.415-.971-1.142-2.694-1.746-4.983-1.746zm.789 6.405c-.373 2.454-2.248 2.454-4.062 2.454h-1.031l.725-4.583a.568.568 0 0 1 .562-.481h.473c1.234 0 2.4 0 3.002.704.359.42.468 1.044.331 1.906zM115.434 13.075h-3.273a.567.567 0 0 0-.562.481l-.145.916-.23-.332c-.709-1.029-2.289-1.373-3.867-1.373-3.619 0-6.709 2.741-7.311 6.586-.312 1.918.131 3.752 1.219 5.031 1 1.176 2.426 1.666 4.125 1.666 2.916 0 4.533-1.875 4.533-1.875l-.146.91a.57.57 0 0 0 .564.66h2.949a.95.95 0 0 0 .938-.803l1.771-11.209a.571.571 0 0 0-.565-.658zm-4.565 6.374c-.314 1.871-1.801 3.127-3.695 3.127-.949 0-1.711-.305-2.199-.883-.484-.574-.666-1.391-.514-2.301.297-1.855 1.805-3.152 3.67-3.152.93 0 1.686.309 2.184.892.501.589.699 1.411.554 2.317zM119.295 7.23l-2.807 17.858a.569.569 0 0 0 .562.658h2.822c.469 0 .867-.34.939-.803l2.768-17.536a.57.57 0 0 0-.562-.659h-3.16a.571.571 0 0 0-.562.482z" fill="#179BD7"/>
                                                <path d="M7.266 29.154l.523-3.322-1.165-.027H1.061L4.927 1.292a.316.316 0 0 1 .314-.268h9.38c3.114 0 5.263.648 6.385 1.927.526.6.861 1.227 1.023 1.917.17.724.173 1.589.007 2.644l-.012.077v.676l.526.298a3.69 3.69 0 0 1 1.065.812c.45.513.741 1.165.864 1.938.127.795.085 1.741-.123 2.812-.24 1.232-.628 2.305-1.152 3.183a6.547 6.547 0 0 1-1.825 2c-.696.494-1.523.869-2.458 1.109-.906.236-1.939.355-3.072.355h-.73c-.522 0-1.029.188-1.427.525a2.21 2.21 0 0 0-.744 1.328l-.055.299-.924 5.855-.042.215c-.011.068-.03.102-.058.125a.155.155 0 0 1-.096.035H7.266z" fill="#253B80"/>
                                                <path d="M23.048 7.667c-.028.179-.06.362-.096.55-1.237 6.351-5.469 8.545-10.874 8.545H9.326c-.661 0-1.218.48-1.321 1.132L6.596 26.83l-.399 2.533a.704.704 0 0 0 .695.814h4.881c.578 0 1.069-.42 1.16-.99l.048-.248.919-5.832.059-.32c.09-.572.582-.992 1.16-.992h.73c4.729 0 8.431-1.92 9.513-7.476.452-2.321.218-4.259-.978-5.622a4.667 4.667 0 0 0-1.336-1.03z" fill="#179BD7"/>
                                                <path d="M21.754 7.151a9.757 9.757 0 0 0-1.203-.267 15.284 15.284 0 0 0-2.426-.177h-7.352a1.172 1.172 0 0 0-1.159.992L8.05 17.605l-.045.289a1.336 1.336 0 0 1 1.321-1.132h2.752c5.405 0 9.637-2.195 10.874-8.545.037-.188.068-.371.096-.55a6.594 6.594 0 0 0-1.017-.429 9.045 9.045 0 0 0-.277-.087z" fill="#222D65"/>
                                                <path d="M9.614 7.699a1.169 1.169 0 0 1 1.159-.991h7.352c.871 0 1.684.057 2.426.177a9.757 9.757 0 0 1 1.481.353c.365.121.704.264 1.017.429.368-2.347-.003-3.945-1.272-5.392C20.378.682 17.853 0 14.622 0h-9.38c-.66 0-1.223.48-1.325 1.133L.01 25.898a.806.806 0 0 0 .795.932h5.791l1.454-9.225 1.564-9.906z" fill="#253B80"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Sofortige Zahlung via PayPal. Tickets werden direkt nach erfolgreicher Zahlung versendet.</p>
                                    </div>
                                </label>
                                @endif
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
                    class="w-full mt-6 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center">
                <span id="submit-button-text">{{ $ticketTypes->isEmpty() ? 'Keine Tickets verfügbar' : 'Kostenpflichtig buchen' }}</span>
                <svg id="submit-spinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
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

        // Update submit button text based on payment method and total
        function updateSubmitButton() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const submitButtonText = document.getElementById('submit-button-text');
            const totalAmount = subtotal - discountAmount;

            if (totalAmount === 0) {
                submitButtonText.textContent = 'Kostenlos buchen';
            } else if (paymentMethod === 'paypal') {
                submitButtonText.textContent = 'Mit PayPal bezahlen';
            } else {
                submitButtonText.textContent = 'Kostenpflichtig buchen';
            }
        }

        // Listen to payment method changes
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', updateSubmitButton);
        });

        // Form submit handler um nur ausgewählte Tickets zu senden
        document.getElementById('booking-form').addEventListener('submit', function(e) {
            // Show loading spinner
            const submitButton = document.getElementById('submit-button');
            const submitSpinner = document.getElementById('submit-spinner');
            const submitButtonText = document.getElementById('submit-button-text');

            submitButton.disabled = true;
            submitSpinner.classList.remove('hidden');
            submitButtonText.textContent = 'Wird verarbeitet...';

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

                // Reset button state
                submitButton.disabled = false;
                submitSpinner.classList.add('hidden');
                updateSubmitButton();

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
            updateSubmitButton();
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

