<x-layouts.app title="Rechnungsnummern für Plattformgebühren">
    <x-ui.page-header title="Rechnungsnummern für Plattformgebühren"
        subtitle="Konfigurieren Sie das Format und die Nummerierung für Rechnungen, die von der Plattform an Veranstalter gestellt werden." />

    @include('admin.monetization._tabs', ['active' => 'numbers'])

    <div class="max-w-form">
        <x-ui.alert tone="info" class="mb-8">
            <strong>Hinweis:</strong> Rechnungen an Teilnehmer werden vom jeweiligen Veranstalter konfiguriert.
        </x-ui.alert>

        <form action="{{ route('admin.settings.invoice.update') }}" method="POST">
            @csrf
            @method('PUT')

            <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

            <x-ui.box tone="zone" padding="sm" class="mb-8">
                <h2 class="m-0 mb-3 text-h3">Verfügbare Platzhalter</h2>
                <dl class="m-0 grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-3 text-small">
                    @foreach ($placeholders as $placeholder => $description)
                        <div class="flex flex-col gap-0.5">
                            <dt><code class="text-small font-medium text-ink">{{ $placeholder }}</code></dt>
                            <dd class="m-0 text-muted">{{ $description }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-ui.box>

            <x-ui.section title="Plattformgebühren-Rechnungen"
                description="Diese Rechnungen werden von der Plattform an Veranstalter gestellt, wenn eine Veranstaltung abgeschlossen ist."
                class="border-t border-line py-8">
                <div class="flex flex-col gap-y-5">
                    <div>
                        <x-ui.label for="invoice_number_format_platform_fee" required>Format für
                            Rechnungsnummern</x-ui.label>
                        <x-ui.input name="invoice_number_format_platform_fee" id="invoice_number_format_platform_fee"
                            :value="$settings['invoice_number_format_platform_fee']" required aria-describedby="invoice_number_format_platform_fee-hint" />
                        <x-ui.hint id="invoice_number_format_platform_fee-hint">
                            Beispiel: PF-{YEAR}-{NUMBER} wird zu <span id="preview-platform"
                                class="font-semibold text-ink">...</span>
                        </x-ui.hint>
                        <x-ui.field-error name="invoice_number_format_platform_fee" />
                    </div>

                    <x-ui.field label="Aktueller Zählerstand" name="invoice_number_counter_platform_fee" type="number"
                        :value="$settings['invoice_number_counter_platform_fee']" min="1" required
                        hint="Die nächste Rechnungsnummer wird ab diesem Wert vergeben." />
                </div>
            </x-ui.section>

            <x-ui.section title="Allgemeine Einstellungen" class="border-t border-line py-8">
                <div class="flex flex-col gap-y-5">
                    <x-ui.field label="Nullen-Auffüllung für {NUMBER}" name="invoice_number_padding" type="number"
                        :value="$settings['invoice_number_padding']" min="1" max="10" required
                        hint="Anzahl der Stellen: 5 ergibt 00001, 00002, …" />

                    <div>
                        <input type="hidden" name="invoice_reset_yearly" value="0">
                        <x-ui.checkbox name="invoice_reset_yearly" id="invoice_reset_yearly" value="1"
                            :checked="(bool) $settings['invoice_reset_yearly']" label="Zähler jährlich zurücksetzen"
                            hint="Wenn aktiviert, wird der Zähler am 1. Januar automatisch auf 1 zurückgesetzt." />
                    </div>
                </div>
            </x-ui.section>

            <div class="border-t border-line pt-8">
                <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Live preview of invoice number format
            function updatePreview() {
                const formatInput = document.getElementById('invoice_number_format_platform_fee');
                const paddingInput = document.getElementById('invoice_number_padding');
                const previewSpan = document.getElementById('preview-platform');

                const format = formatInput.value;
                const padding = parseInt(paddingInput.value) || 5;

                fetch('{{ route('admin.settings.invoice.preview') }}', {
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

            // Update preview on input change
            document.addEventListener('DOMContentLoaded', function() {
                updatePreview();

                document.getElementById('invoice_number_format_platform_fee').addEventListener('input', updatePreview);
                document.getElementById('invoice_number_padding').addEventListener('input', updatePreview);
            });
        </script>
    @endpush
</x-layouts.app>
