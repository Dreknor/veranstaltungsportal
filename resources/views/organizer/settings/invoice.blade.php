<x-layouts.app title="Rechnungsnummern">
    <x-ui.page-header title="Rechnungsdaten verwalten"
        subtitle="Verwalten Sie Ihre Bankdaten, Firmendaten und Rechnungseinstellungen." />

    <x-ui.tabs label="Bereiche der Rechnungsdaten" class="mb-8">
        <x-ui.tab :href="route('organizer.bank-account.index')">Kontoverbindung</x-ui.tab>
        <x-ui.tab :href="route('organizer.bank-account.billing-data')">Rechnungsdaten</x-ui.tab>
        <x-ui.tab :href="route('organizer.settings.invoice.index')" active>Rechnungsnummern</x-ui.tab>
    </x-ui.tabs>

    <div class="max-w-form">
        {{-- Rechnungsmodus --}}
        <x-ui.section title="Rechnungsmodus"
            description="Wählen Sie, ob Rechnungen automatisch über die Plattform oder extern in Ihrer eigenen Buchhaltungssoftware erstellt werden."
            class="pb-8">
            <form method="POST" action="{{ route('organizer.settings.invoice-mode.update') }}">
                @csrf
                @method('PUT')

                <div class="flex flex-col gap-3" x-data="{ mode: '{{ $organization->invoice_mode ?? 'automatic' }}' }">
                    <x-ui.choice name="invoice_mode" value="automatic" x-model="mode"
                        title="Automatische Rechnungsstellung"
                        description="Rechnungen werden automatisch als ZUGFeRD-PDF generiert und per E-Mail an die Teilnehmer versendet. PayPal-Zahlung ist möglich." />
                    <x-ui.choice name="invoice_mode" value="external" x-model="mode" title="Externe Rechnungsstellung"
                        description="Sie erstellen Rechnungen in Ihrer eigenen Buchhaltungssoftware. Die Plattform generiert keine Rechnungen und bietet kein PayPal an. Rechnungsdaten können als Excel exportiert werden." />

                    {{-- Warnhinweis --}}
                    <x-ui.alert tone="warning" x-show="mode !== '{{ $organization->invoice_mode ?? 'automatic' }}'"
                        x-cloak>
                        <span>
                            <strong>Achtung:</strong> Beim Wechsel des Rechnungsmodus werden bestehende Buchungen nicht
                            verändert. Der neue Modus gilt nur für zukünftige Buchungen.
                            <span x-show="mode === 'external'">Bei Wechsel auf „Extern" wird PayPal automatisch für neue
                                Buchungen deaktiviert.</span>
                        </span>
                    </x-ui.alert>

                    <div class="mt-3">
                        <x-ui.button type="submit">Rechnungsmodus speichern</x-ui.button>
                    </div>
                </div>
            </form>
        </x-ui.section>

        @if ($organization->hasAutomaticInvoicing())
            <x-ui.section title="Rechnungsnummern-Format"
                description="Konfigurieren Sie das Format und die Nummerierung für Rechnungen an Ihre Teilnehmer"
                class="border-t border-line py-8">
                <form action="{{ route('organizer.settings.invoice.update') }}" method="POST"
                    class="flex flex-col gap-y-5">
                    @csrf
                    @method('PUT')

                    <p class="m-0 text-small text-muted">* Pflichtfeld</p>

                    <!-- Placeholders Info -->
                    <x-ui.box tone="zone" padding="sm">
                        <h3 class="m-0 mb-3 text-h3">Verfügbare Platzhalter</h3>
                        <dl class="m-0 grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-3 text-small">
                            @foreach ($placeholders as $placeholder => $description)
                                <div class="flex flex-col gap-0.5">
                                    <dt><code
                                            class="font-sans text-small font-medium text-ink">{{ $placeholder }}</code>
                                    </dt>
                                    <dd class="m-0 text-muted">{{ $description }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </x-ui.box>

                    <!-- Invoice Format -->
                    <div>
                        <x-ui.label for="invoice_number_format_booking" required>Format für
                            Rechnungsnummern</x-ui.label>
                        <x-ui.input name="invoice_number_format_booking" id="invoice_number_format_booking"
                            :value="$settings['invoice_number_format_booking']" placeholder="RE-{YEAR}-{NUMBER}" required
                            aria-describedby="invoice_number_format_booking-hint" />
                        <x-ui.hint id="invoice_number_format_booking-hint">
                            Beispiel: RE-{YEAR}-{NUMBER} wird zu <span id="preview-booking"
                                class="font-semibold text-ink">...</span>
                        </x-ui.hint>
                        <x-ui.field-error name="invoice_number_format_booking" />
                    </div>

                    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.field label="Aktueller Zählerstand" name="invoice_number_counter_booking" type="number"
                            :value="$settings['invoice_number_counter_booking']" min="1" required
                            hint="Die nächste Rechnungsnummer wird ab diesem Wert vergeben." />
                        <x-ui.field label="Nullen-Auffüllung für {NUMBER}" name="invoice_number_padding" type="number"
                            :value="$settings['invoice_number_padding']" min="1" max="10" required
                            hint="Anzahl der Stellen: 5 ergibt 00001, 00002, …" />
                    </div>

                    <div>
                        <input type="hidden" name="invoice_reset_yearly" value="0">
                        <x-ui.checkbox name="invoice_reset_yearly" id="invoice_reset_yearly" value="1"
                            :checked="(bool) $settings['invoice_reset_yearly']" label="Zähler jährlich zurücksetzen"
                            hint="Wenn aktiviert, wird der Zähler am 1. Januar automatisch auf 1 zurückgesetzt." />
                    </div>

                    <!-- Warning Box -->
                    <x-ui.alert tone="warning" title="Wichtiger Hinweis">
                        Diese Einstellungen gelten nur für Ihre Rechnungen. Ändern Sie die Zählerstände nur, wenn Sie
                        genau wissen, was Sie tun, da dies zu doppelten Rechnungsnummern führen kann.
                    </x-ui.alert>

                    <!-- Submit Button -->
                    <div class="flex flex-wrap gap-3">
                        <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
                        <x-ui.button variant="secondary" :href="route('organizer.settings.invoice.sample')">
                            <x-slot:icon><x-icon.download /></x-slot:icon>
                            Beispielrechnung herunterladen
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.section>
        @else
            <x-ui.alert tone="info">
                Die Rechnungsnummern-Einstellungen sind bei externer Rechnungsstellung nicht verfügbar, da keine
                Rechnungsnummern generiert werden.
            </x-ui.alert>
        @endif
    </div>

    @if ($organization->hasAutomaticInvoicing())
        @push('scripts')
            <script>
                function updatePreview() {
                    const formatInput = document.getElementById('invoice_number_format_booking');
                    const paddingInput = document.getElementById('invoice_number_padding');
                    const previewSpan = document.getElementById('preview-booking');

                    const format = formatInput.value;
                    const padding = parseInt(paddingInput.value) || 5;

                    fetch('{{ route('organizer.settings.invoice.preview') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                format,
                                padding
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                previewSpan.textContent = data.preview;
                                previewSpan.className = 'font-semibold text-success';
                            } else {
                                previewSpan.textContent = 'Ungültig!';
                                previewSpan.className = 'font-semibold text-danger';
                            }
                        })
                        .catch(() => {
                            previewSpan.textContent = 'Fehler';
                            previewSpan.className = 'font-semibold text-danger';
                        });
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const formatInput = document.getElementById('invoice_number_format_booking');
                    const paddingInput = document.getElementById('invoice_number_padding');

                    if (formatInput && paddingInput) {
                        formatInput.addEventListener('input', updatePreview);
                        paddingInput.addEventListener('input', updatePreview);
                        updatePreview();
                    }
                });
            </script>
        @endpush
    @endif
</x-layouts.app>
