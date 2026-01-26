<x-layouts.app title="PayPal-Einstellungen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">PayPal-Einstellungen</h1>
            <p class="mt-1 text-sm text-gray-600">
                Konfigurieren Sie PayPal als Zahlungsmethode für Ihre Veranstaltungen
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <form method="POST" action="{{ route('organizer.organization.paypal.update') }}">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">
                    <!-- PayPal aktivieren/deaktivieren -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label for="paypal_enabled" class="text-base font-medium text-gray-900">
                                PayPal-Zahlungen aktivieren
                            </label>
                            <p class="text-sm text-gray-600 mt-1">
                                Ermöglicht Ihren Kunden, direkt mit PayPal zu bezahlen
                            </p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="paypal_enabled"
                                       id="paypal_enabled"
                                       value="1"
                                       {{ old('paypal_enabled', $organization->paypal_enabled) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- PayPal Modus -->
                    <div>
                        <label for="paypal_mode" class="block text-sm font-medium text-gray-700 mb-2">
                            Modus *
                        </label>
                        <select name="paypal_mode"
                                id="paypal_mode"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required>
                            <option value="sandbox" {{ old('paypal_mode', $organization->paypal_mode) === 'sandbox' ? 'selected' : '' }}>
                                Sandbox (Testmodus)
                            </option>
                            <option value="live" {{ old('paypal_mode', $organization->paypal_mode) === 'live' ? 'selected' : '' }}>
                                Live (Produktiv)
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <strong>Sandbox:</strong> Für Tests. <strong>Live:</strong> Für echte Zahlungen.
                        </p>
                    </div>

                    <!-- Client ID -->
                    <div>
                        <label for="paypal_client_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Client ID *
                        </label>
                        @if($organization->paypal_client_id && !old('paypal_client_id'))
                            <div class="mb-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                <strong>Aktuell gespeichert:</strong> ••••••••
                                <span class="text-xs">(Lassen Sie das Feld leer, um den gespeicherten Wert zu behalten)</span>
                            </div>
                        @endif
                        <input type="text"
                               name="paypal_client_id"
                               id="paypal_client_id"
                               value="{{ old('paypal_client_id', '') }}"
                               placeholder="{{ $organization->paypal_client_id ? 'Leer lassen, um beizubehalten' : 'Ihre PayPal Client ID' }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               {{ $organization->paypal_client_id ? '' : 'required' }}>
                        <p class="mt-1 text-xs text-gray-500">
                            Erhältlich im <a href="https://developer.paypal.com/dashboard/" target="_blank" class="text-blue-600 hover:underline">PayPal Developer Dashboard</a>
                        </p>
                    </div>

                    <!-- Client Secret -->
                    <div>
                        <label for="paypal_client_secret" class="block text-sm font-medium text-gray-700 mb-2">
                            Client Secret *
                        </label>
                        @if($organization->paypal_client_secret && !old('paypal_client_secret'))
                            <div class="mb-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                <strong>Aktuell gespeichert:</strong> ••••••••
                                <span class="text-xs">(Lassen Sie das Feld leer, um den gespeicherten Wert zu behalten)</span>
                            </div>
                        @endif
                        <input type="password"
                               name="paypal_client_secret"
                               id="paypal_client_secret"
                               value="{{ old('paypal_client_secret', '') }}"
                               placeholder="{{ $organization->paypal_client_secret ? 'Leer lassen, um beizubehalten' : 'Ihr PayPal Client Secret' }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               {{ $organization->paypal_client_secret ? '' : 'required' }}>
                        <p class="mt-1 text-xs text-gray-500">
                            Wird verschlüsselt gespeichert
                        </p>
                    </div>

                    <!-- Webhook ID (optional) -->
                    <div>
                        <label for="paypal_webhook_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Webhook ID (optional)
                        </label>
                        <input type="text"
                               name="paypal_webhook_id"
                               id="paypal_webhook_id"
                               value="{{ old('paypal_webhook_id', $organization->paypal_webhook_id) }}"
                               placeholder="Ihre PayPal Webhook ID"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">
                            Erforderlich für Live-Modus zur Webhook-Verifizierung
                        </p>
                    </div>

                    <!-- Hinweis-Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    So richten Sie PayPal ein:
                                </h3>
                                <div class="mt-2 text-sm text-blue-700 space-y-1">
                                    <p>1. Erstellen Sie eine App im <a href="https://developer.paypal.com/dashboard/" target="_blank" class="underline">PayPal Developer Dashboard</a></p>
                                    <p>2. Kopieren Sie Client ID und Secret aus Ihrer App</p>
                                    <p>3. Konfigurieren Sie einen Webhook auf: <code class="bg-blue-100 px-1 rounded">{{ url('/paypal/webhook') }}</code></p>
                                    <p>4. Abonnieren Sie die Events: <code class="bg-blue-100 px-1 rounded">PAYMENT.CAPTURE.COMPLETED</code></p>
                                    <p>5. Kopieren Sie die Webhook-ID und tragen Sie sie oben ein</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <a href="{{ route('organizer.organization.edit') }}"
                       class="text-gray-600 hover:text-gray-800">
                        Zurück
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                        Einstellungen speichern
                    </button>
                </div>
            </form>
        </div>

        <!-- Status-Anzeige -->
        @if($organization->hasPayPalConfigured())
            <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">
                            PayPal ist konfiguriert
                        </h3>
                        <p class="mt-1 text-sm text-green-700">
                            Modus: <strong>{{ ucfirst($organization->paypal_mode) }}</strong> |
                            Status: <strong>{{ $organization->paypal_enabled ? 'Aktiviert' : 'Deaktiviert' }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Zeige/Verstecke Felder basierend auf Aktivierung
        document.getElementById('paypal_enabled').addEventListener('change', function() {
            const fields = document.querySelectorAll('#paypal_mode, #paypal_client_id, #paypal_client_secret');
            fields.forEach(field => {
                field.required = this.checked;
            });
        });
    </script>
    @endpush
</x-layouts.app>
