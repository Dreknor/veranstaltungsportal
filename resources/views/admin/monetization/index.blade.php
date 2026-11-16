<x-layouts.app title="Monetarisierungs-Einstellungen">
    <x-ui.page-header title="Monetarisierungs-Einstellungen"
        subtitle="Verwalten Sie Plattformgebühren und Rechnungseinstellungen." />

    @include('admin.monetization._tabs', ['active' => 'fees'])

    <form method="POST" action="{{ route('admin.monetization.update') }}" class="max-w-form">
        @csrf
        @method('PUT')

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <x-ui.section title="Plattformgebühr" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <fieldset class="m-0 min-w-0 border-0 p-0">
                    <x-ui.label as="legend">Gebührentyp</x-ui.label>
                    <x-ui.radio name="platform_fee_type" id="type_percentage" value="percentage"
                        label="Prozentual (empfohlen)" :checked="old('platform_fee_type', $settings['platform_fee_type']) == 'percentage'" :old="false" />
                    <x-ui.radio name="platform_fee_type" id="type_fixed" value="fixed" label="Festbetrag"
                        :checked="old('platform_fee_type', $settings['platform_fee_type']) == 'fixed'" :old="false" />
                </fieldset>

                <div id="percentage_field">
                    <x-ui.field label="Plattformgebühr (Prozent)" name="platform_fee_percentage" type="number" required
                        step="0.01" min="0" max="100" :old="false" :value="old('platform_fee_percentage', $settings['platform_fee_percentage'])"
                        hint="Beispiel: Bei 5 % und einer Buchung über 100 € werden 5 € Gebühr berechnet." />
                </div>

                <div id="fixed_field" style="display: none;">
                    <x-ui.field label="Plattformgebühr (Festbetrag in €)" name="platform_fee_fixed_amount"
                        type="number" step="0.01" min="0" :old="false" :value="old('platform_fee_fixed_amount', $settings['platform_fee_fixed_amount'])"
                        hint="Fester Betrag pro Buchung, unabhängig vom Ticketpreis." />
                </div>

                <x-ui.field label="Mindestgebühr pro Buchung (in €)" name="platform_fee_minimum" type="number" required
                    step="0.01" min="0" :old="false" :value="old('platform_fee_minimum', $settings['platform_fee_minimum'])"
                    hint="Wichtig für kostenfreie Tickets. Es gilt die höhere von berechneter Gebühr und Mindestgebühr." />
            </div>
        </x-ui.section>

        <x-ui.section title="Rechnungseinstellungen" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.checkbox name="auto_invoice" id="auto_invoice" value="1"
                    label="Automatische Rechnungserstellung"
                    hint="Rechnungen werden automatisch nach Veranstaltungsende erstellt." :checked="old('auto_invoice', $settings['auto_invoice'])"
                    :old="false" />

                <x-ui.field label="Buchhaltungs-E-Mail (CC)" name="invoice_cc_email" type="email" optional
                    :old="false" :value="old('invoice_cc_email', $settings['invoice_cc_email'])" placeholder="buchhaltung@ihre-plattform.de"
                    hint="Diese Adresse erhält eine Kopie aller erzeugten Rechnungen." />

                <x-ui.field label="Zahlungsfrist (Tage)" name="payment_deadline_days" type="number" required
                    min="1" max="90" :old="false" :value="old('payment_deadline_days', $settings['payment_deadline_days'])"
                    hint="Anzahl der Tage, die Veranstalter Zeit haben, die Plattformgebühr zu zahlen." />
            </div>
        </x-ui.section>

        <x-ui.section title="Hervorhebung von Veranstaltungen" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.checkbox name="featured_event_enabled" id="featured_event_enabled" value="1"
                    label="Hervorhebung aktivieren"
                    hint="Veranstalter können Veranstaltungen gegen Gebühr hervorheben lassen." :checked="old('featured_event_enabled', $settings['featured_event_enabled'])"
                    :old="false" />

                <div id="featured_settings" class="flex flex-col gap-y-5">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <x-ui.stat label="Aktive Hervorhebungen" :value="$featuredStats['active_featured_events']" />
                        <x-ui.stat label="Gesamtumsatz" :value="\App\Support\Ui::money($featuredStats['total_revenue'])" />
                        <x-ui.stat label="Umsatz diesen Monat" :value="\App\Support\Ui::money($featuredStats['this_month_revenue'])" />
                    </div>

                    <div class="grid grid-cols-[repeat(auto-fit,minmax(168px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.field label="Tagespreis (€)" name="featured_event_daily_rate" type="number" required
                            step="0.01" min="0" :old="false" :value="old('featured_event_daily_rate', $settings['featured_event_daily_rate'])"
                            hint="Gebühr für 1 Tag." />
                        <x-ui.field label="Wochenpreis (€)" name="featured_event_weekly_rate" type="number" required
                            step="0.01" min="0" :old="false" :value="old('featured_event_weekly_rate', $settings['featured_event_weekly_rate'])"
                            hint="Gebühr für 7 Tage." />
                        <x-ui.field label="Monatspreis (€)" name="featured_event_monthly_rate" type="number" required
                            step="0.01" min="0" :old="false" :value="old('featured_event_monthly_rate', $settings['featured_event_monthly_rate'])"
                            hint="Gebühr für 30 Tage." />
                    </div>

                    <x-ui.field label="Maximale Dauer (Tage)" name="featured_event_max_duration_days" type="number"
                        required min="1" max="365" :old="false" :value="old(
                            'featured_event_max_duration_days',
                            $settings['featured_event_max_duration_days'],
                        )"
                        hint="Maximale Anzahl Tage, die eine Veranstaltung hervorgehoben sein kann." />

                    <x-ui.checkbox name="featured_event_auto_disable_on_expiry"
                        id="featured_event_auto_disable_on_expiry" value="1" label="Automatisch deaktivieren"
                        hint="Die Hervorhebung wird nach Ablauf automatisch entfernt." :checked="old(
                            'featured_event_auto_disable_on_expiry',
                            $settings['featured_event_auto_disable_on_expiry'],
                        )"
                        :old="false" />

                    @if ($featuredStats['pending_payments'] > 0 || $featuredStats['total_featured_fees'] > 0)
                        <x-ui.alert tone="info" title="Status der Hervorhebungen">
                            <ul class="m-0 list-disc pl-5">
                                @if ($featuredStats['pending_payments'] > 0)
                                    <li>{{ $featuredStats['pending_payments'] }}
                                        {{ $featuredStats['pending_payments'] === 1 ? 'Zahlung' : 'Zahlungen' }}
                                        ausstehend</li>
                                @endif
                                <li>{{ $featuredStats['total_featured_fees'] }} bezahlte Zeiträume insgesamt</li>
                                <li>Nächste automatische Prüfung: täglich um 00:00 Uhr</li>
                            </ul>
                        </x-ui.alert>
                    @endif
                </div>
            </div>
        </x-ui.section>

        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
        </div>
    </form>

    <x-ui.alert tone="info" title="Wichtige Hinweise" class="mt-8 max-w-form">
        <ul class="m-0 list-disc pl-5">
            <li>Änderungen an Plattformgebühren gelten für alle neuen Buchungen.</li>
            <li>Gebühren für Hervorhebungen gelten für neue Anträge.</li>
            <li>Bestehende Rechnungen bleiben unverändert.</li>
            <li>Die Einstellungen werden in der .env-Datei gespeichert.</li>
            <li>Hervorhebungen werden automatisch deaktiviert, wenn diese Option aktiv ist.</li>
        </ul>
    </x-ui.alert>

    <!-- Organizers with Custom Fees -->
    <x-ui.section class="mt-12" :title="'Organisationen mit individuellen Gebühren (' . $organizersWithCustomFees->count() . ')'"
        description="Diese Organisationen haben Sonderkonditionen, die von den globalen Einstellungen abweichen.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.organizations.index')">Alle Organisationen</x-ui.button>
        </x-slot:actions>

        <x-ui.table label="Organisationen mit individuellen Gebühren">
            <thead>
                <tr>
                    <th scope="col">Organisation</th>
                    <th scope="col" class="max-sm:hidden">Gebührentyp</th>
                    <th scope="col" class="num">Gebühr</th>
                    <th scope="col" class="max-sm:hidden">Notizen</th>
                    <th scope="col" class="max-sm:hidden">Letzte Änderung</th>
                    <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($organizersWithCustomFees as $organization)
                    @php
                        $customFee = $organization->custom_platform_fee;
                        $feeType = $customFee['fee_type'] ?? 'percentage';
                        $feeValue =
                            $feeType === 'percentage'
                                ? \App\Support\Ui::percent($customFee['fee_percentage'] ?? 0, 2)
                                : number_format($customFee['fee_fixed_amount'] ?? 0, 2, ',', '.') . ' €';
                    @endphp
                    <tr>
                        <th scope="row">
                            {{ $organization->name }}
                            <span class="block text-small font-normal text-muted">{{ $organization->email }}</span>
                        </th>
                        <td class="max-sm:hidden">{{ $feeType === 'percentage' ? 'Prozentual' : 'Festbetrag' }}</td>
                        <td class="num">
                            {{ $feeValue }}
                            @if ($feeType === 'percentage')
                                <span class="block text-small font-normal text-muted">statt
                                    {{ \App\Support\Ui::percent($settings['platform_fee_percentage'], 2) }}</span>
                            @endif
                        </td>
                        <td class="max-w-xs truncate max-sm:hidden" title="{{ $customFee['notes'] ?? '' }}">
                            {{ $customFee['notes'] ?? '–' }}</td>
                        <td class="whitespace-nowrap tabular-nums max-sm:hidden">
                            @if (isset($customFee['updated_at']))
                                {{ \Carbon\Carbon::parse($customFee['updated_at'])->format('d.m.Y H:i') }}
                            @else
                                –
                            @endif
                        </td>
                        <td class="actions">
                            <x-ui.button size="sm" variant="secondary"
                                :href="route('admin.organizer-fees.edit', $organization)">Bearbeiten</x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="6" class="p-0">
                            <x-ui.empty :bordered="false" title="Keine individuellen Gebühren" :action-href="route('admin.organizations.index')"
                                action-label="Zur Organisationenverwaltung">
                                Derzeit hat keine Organisation eine individuelle Gebührenregelung. Sie können sie in der
                                Organisationenverwaltung festlegen.
                            </x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>

    @push('scripts')
        <script>
            const percentageRadio = document.getElementById('type_percentage');
            const fixedRadio = document.getElementById('type_fixed');
            const percentageField = document.getElementById('percentage_field');
            const fixedField = document.getElementById('fixed_field');

            function toggleFeeFields() {
                if (percentageRadio.checked) {
                    percentageField.style.display = 'block';
                    fixedField.style.display = 'none';
                } else {
                    percentageField.style.display = 'none';
                    fixedField.style.display = 'block';
                }
            }

            percentageRadio.addEventListener('change', toggleFeeFields);
            fixedRadio.addEventListener('change', toggleFeeFields);

            toggleFeeFields();

            // Featured Events Toggle
            const featuredEnabledCheckbox = document.getElementById('featured_event_enabled');
            const featuredSettingsDiv = document.getElementById('featured_settings');

            function toggleFeaturedSettings() {
                if (featuredEnabledCheckbox.checked) {
                    featuredSettingsDiv.style.display = 'flex';
                } else {
                    featuredSettingsDiv.style.display = 'none';
                }
            }

            if (featuredEnabledCheckbox) {
                featuredEnabledCheckbox.addEventListener('change', toggleFeaturedSettings);
                toggleFeaturedSettings();
            }

            // Scroll to Featured Events section
            function scrollToFeatured(event) {
                event.preventDefault();
                // Scroll to the featured events checkbox area
                const featuredCheckbox = document.getElementById('featured_event_enabled');
                if (featuredCheckbox) {
                    featuredCheckbox.closest('.border-t').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        </script>
    @endpush
</x-layouts.app>
