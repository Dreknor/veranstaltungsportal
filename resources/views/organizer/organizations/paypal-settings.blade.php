<x-layouts.app title="PayPal-Einstellungen">
    <x-ui.page-header title="PayPal-Einstellungen"
        subtitle="Konfigurieren Sie PayPal als Zahlungsmethode für Ihre Veranstaltungen."
        :back-href="route('organizer.organization.edit')" back-label="Zurück zur Organisation" />

    <div class="max-w-form">
        @if ($organization->hasPayPalConfigured())
            <x-ui.alert tone="success" title="PayPal ist konfiguriert" class="mb-8">
                Modus: <strong>{{ ucfirst($organization->paypal_mode) }}</strong> ·
                Status: <strong>{{ $organization->paypal_enabled ? 'Aktiviert' : 'Deaktiviert' }}</strong>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('organizer.organization.paypal.update') }}">
            @csrf
            @method('PUT')

            <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

            <x-ui.section title="Aktivierung" class="border-t border-line py-8">
                <x-ui.checkbox name="paypal_enabled" id="paypal_enabled" :checked="(bool) $organization->paypal_enabled"
                    label="PayPal-Zahlungen aktivieren"
                    hint="Ermöglicht Ihren Kunden, direkt mit PayPal zu bezahlen." />
            </x-ui.section>

            <x-ui.section title="Zugangsdaten" class="border-t border-line py-8">
                <div class="flex flex-col gap-y-5">
                    <x-ui.select label="Modus" name="paypal_mode" required :selected="$organization->paypal_mode"
                        hint="Sandbox: für Tests. Live: für echte Zahlungen.">
                        <option value="sandbox" @selected(old('paypal_mode', $organization->paypal_mode) === 'sandbox')>Sandbox (Testmodus)</option>
                        <option value="live" @selected(old('paypal_mode', $organization->paypal_mode) === 'live')>Live (Produktiv)</option>
                    </x-ui.select>

                    <x-ui.field label="Client ID" name="paypal_client_id"
                        :required="! $organization->paypal_client_id"
                        placeholder="{{ $organization->paypal_client_id ? 'Leer lassen, um beizubehalten' : 'Ihre PayPal Client ID' }}">
                        <x-slot:hint>
                            @if ($organization->paypal_client_id && ! old('paypal_client_id'))
                                Aktuell gespeichert: ••••••••. Lassen Sie das Feld leer, um den gespeicherten Wert zu behalten.
                            @endif
                            Erhältlich im <a href="https://developer.paypal.com/dashboard/" target="_blank" rel="noopener" class="font-medium text-ink underline underline-offset-[3px]">PayPal Developer Dashboard</a>.
                        </x-slot:hint>
                    </x-ui.field>

                    <x-ui.field type="password" label="Client Secret" name="paypal_client_secret"
                        :required="! $organization->paypal_client_secret" autocomplete="off"
                        placeholder="{{ $organization->paypal_client_secret ? 'Leer lassen, um beizubehalten' : 'Ihr PayPal Client Secret' }}">
                        <x-slot:hint>
                            @if ($organization->paypal_client_secret && ! old('paypal_client_secret'))
                                Aktuell gespeichert: ••••••••. Lassen Sie das Feld leer, um den gespeicherten Wert zu behalten.
                            @endif
                            Wird verschlüsselt gespeichert.
                        </x-slot:hint>
                    </x-ui.field>

                    <x-ui.field label="Webhook ID" name="paypal_webhook_id" :value="$organization->paypal_webhook_id"
                        optional placeholder="Ihre PayPal Webhook ID"
                        hint="Erforderlich für den Live-Modus zur Webhook-Verifizierung." />
                </div>
            </x-ui.section>

            <x-ui.section title="So richten Sie PayPal ein" level="h3" class="border-t border-line py-8">
                <ol class="m-0 list-decimal pl-5 text-base text-ink">
                    <li>Erstellen Sie eine App im <a href="https://developer.paypal.com/dashboard/" target="_blank" rel="noopener" class="font-medium underline underline-offset-[3px]">PayPal Developer Dashboard</a></li>
                    <li>Kopieren Sie Client ID und Secret aus Ihrer App</li>
                    <li>Konfigurieren Sie einen Webhook auf: <code>{{ url('/paypal/webhook') }}</code></li>
                    <li>Abonnieren Sie die Ereignisse: <code>PAYMENT.CAPTURE.COMPLETED</code></li>
                    <li>Kopieren Sie die Webhook-ID und tragen Sie sie oben ein</li>
                </ol>
            </x-ui.section>

            <div class="flex flex-wrap gap-3 border-t border-line pt-8">
                <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
                <x-ui.button variant="secondary" :href="route('organizer.organization.edit')">Zurück</x-ui.button>
            </div>
        </form>
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
