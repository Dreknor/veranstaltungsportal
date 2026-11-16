<x-layouts.app :title="'Individuelle Gebühren für ' . $organization->name">
    <x-ui.page-header title="Individuelle Gebühren" :back-href="route('admin.monetization.index')" back-label="Zurück zur Übersicht">
        <x-slot:meta>
            Ausnahme-Einstellungen für: <strong>{{ $organization->name }}</strong>
        </x-slot:meta>
    </x-ui.page-header>

    <div class="max-w-form">
        <x-ui.box tone="zone" padding="sm" class="mb-8">
            <h2 class="m-0 mb-4 text-h3">Globale Plattform-Einstellungen</h2>
            <x-ui.dl layout="stack" class="lg:grid-cols-2!">
                <x-ui.dl-item label="Typ">{{ $globalSettings['fee_type'] === 'percentage' ? 'Prozentual' : 'Festbetrag' }}</x-ui.dl-item>
                @if ($globalSettings['fee_type'] === 'percentage')
                    <x-ui.dl-item label="Gebühr" num>{{ $globalSettings['fee_percentage'] }}%</x-ui.dl-item>
                @else
                    <x-ui.dl-item label="Gebühr" num>{{ number_format($globalSettings['fee_fixed_amount'], 2, ',', '.') }} €</x-ui.dl-item>
                @endif
                <x-ui.dl-item label="Mindestgebühr" num>{{ number_format($globalSettings['minimum_fee'], 2, ',', '.') }} €</x-ui.dl-item>
                <x-ui.dl-item label="Gilt für">Alle Organisationen</x-ui.dl-item>
            </x-ui.dl>
        </x-ui.box>

        <form method="POST" action="{{ route('admin.organizer-fees.update', $organization) }}">
            @csrf
            @method('PUT')

            <x-ui.section title="Individuelle Regelung" class="border-t border-line py-8">
                <div class="flex flex-col gap-y-5">
                    <x-ui.checkbox name="use_custom_fee" id="use_custom_fee" value="1"
                        :checked="(bool) ($customFee['enabled'] ?? false)" label="Individuelle Gebühren aktivieren"
                        hint="Überschreibt die globalen Einstellungen für diese Organisation." />

                    <div id="custom_fee_settings" class="flex flex-col gap-y-5" style="display: none;">
                        <fieldset class="m-0 min-w-0 border-0 p-0">
                            <x-ui.label as="legend">Gebührentyp</x-ui.label>
                            <x-ui.radio name="custom_fee_type" id="custom_type_percentage" value="percentage"
                                :checked="old('custom_fee_type', $customFee['fee_type'] ?? 'percentage') == 'percentage'"
                                :old="false" label="Prozentual" />
                            <x-ui.radio name="custom_fee_type" id="custom_type_fixed" value="fixed"
                                :checked="old('custom_fee_type', $customFee['fee_type'] ?? 'percentage') == 'fixed'"
                                :old="false" label="Festbetrag" />
                        </fieldset>

                        <div id="custom_percentage_field">
                            <x-ui.field label="Plattformgebühr (Prozent)" name="custom_fee_percentage" type="number"
                                step="0.01" min="0" max="100" :value="$customFee['fee_percentage'] ?? 0"
                                input-class="sm:max-w-64" hint="Beispiel: Bei 3 % und einer Buchung über 100 € werden 3 € Gebühr berechnet." />
                        </div>

                        <div id="custom_fixed_field" style="display: none;">
                            <x-ui.field label="Plattformgebühr (Festbetrag in €)" name="custom_fee_fixed_amount" type="number"
                                step="0.01" min="0" :value="$customFee['fee_fixed_amount'] ?? 0" input-class="sm:max-w-64"
                                hint="Fester Betrag pro Buchung." />
                        </div>

                        <x-ui.field label="Mindestgebühr pro Buchung (in €)" name="custom_minimum_fee" type="number"
                            step="0.01" min="0" :value="$customFee['minimum_fee'] ?? $globalSettings['minimum_fee']"
                            input-class="sm:max-w-64"
                            :hint="'Wichtig für kostenlose Tickets. Standard: ' . number_format($globalSettings['minimum_fee'], 2, ',', '.') . ' €'" />

                        <x-ui.textarea label="Notizen" name="custom_fee_notes" optional rows="3"
                            :value="$customFee['notes'] ?? ''" placeholder="Grund für die individuelle Regelung"
                            hint="Interne Notiz, warum dieser Veranstalter spezielle Konditionen erhält." />
                    </div>

                    @if (!empty($customFee['updated_at']))
                        <p class="m-0 text-small text-muted">
                            Letzte Aktualisierung: <span class="tabular-nums">{{ \Carbon\Carbon::parse($customFee['updated_at'])->format('d.m.Y H:i') }} Uhr</span>
                        </p>
                    @endif
                </div>
            </x-ui.section>

            <div class="flex flex-wrap items-center gap-3 border-t border-line pt-8">
                <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
                @if (!empty($customFee))
                    <x-ui.button variant="secondary" type="submit" form="remove-fee-form" class="sm:ml-auto"
                        onclick="return confirm('Individuelle Gebühren wirklich entfernen?')">Individuelle Gebühren entfernen</x-ui.button>
                @endif
            </div>
        </form>

        @if (!empty($customFee))
            <form id="remove-fee-form" method="POST" action="{{ route('admin.organizer-fees.destroy', $organization) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif

        <x-ui.alert tone="warning" title="Wichtiger Hinweis" class="mt-8">
            <ul class="m-0 list-disc pl-5">
                <li>Individuelle Gebühren gelten für alle neuen Buchungen dieser Organisation.</li>
                <li>Bestehende Rechnungen werden nicht rückwirkend angepasst.</li>
                <li>Bei Deaktivierung gelten wieder die globalen Einstellungen.</li>
            </ul>
        </x-ui.alert>
    </div>

    @push('scripts')
        <script>
            const useCustomFeeCheckbox = document.getElementById('use_custom_fee');
            const customFeeSettings = document.getElementById('custom_fee_settings');
            const percentageRadio = document.getElementById('custom_type_percentage');
            const fixedRadio = document.getElementById('custom_type_fixed');
            const percentageField = document.getElementById('custom_percentage_field');
            const fixedField = document.getElementById('custom_fixed_field');

            function toggleCustomSettings() {
                if (useCustomFeeCheckbox.checked) {
                    customFeeSettings.style.display = 'flex';
                    toggleFeeFields();
                } else {
                    customFeeSettings.style.display = 'none';
                }
            }

            function toggleFeeFields() {
                if (percentageRadio.checked) {
                    percentageField.style.display = 'block';
                    fixedField.style.display = 'none';
                } else {
                    percentageField.style.display = 'none';
                    fixedField.style.display = 'block';
                }
            }

            useCustomFeeCheckbox.addEventListener('change', toggleCustomSettings);
            percentageRadio.addEventListener('change', toggleFeeFields);
            fixedRadio.addEventListener('change', toggleFeeFields);

            // Initialize on page load
            toggleCustomSettings();
        </script>
    @endpush
</x-layouts.app>
