{{--
    Dialog "Veranstaltung hervorheben" (Featured-Buchung) im Veranstalter-Formular.
    Auslöser unverändert: window-Event "featured-modal-open" (organizer/events/create, edit).
    Bestätigung: window-Event "featured-booking-confirm" mit { durationType, customDays, startDate }.
--}}
@props(['event'])

@php
    $rates = [
        'daily' => (float) config('monetization.featured_event_rates.daily', 5.0),
        'weekly' => (float) config('monetization.featured_event_rates.weekly', 25.0),
        'monthly' => (float) config('monetization.featured_event_rates.monthly', 80.0),
    ];
    $money = fn($amount) => \App\Support\Ui::money($amount);
@endphp

<div x-data="{
    durationType: 'weekly',
    customDays: 7,
    startDate: '{{ now()->format('Y-m-d') }}',
    rates: @js($rates),
    net() { return this.durationType === 'custom' ? this.customDays * this.rates.daily : this.rates[this.durationType]; },
    euro(amount) { return (Number(amount) || 0).toFixed(2).replace('.', ',') + ' €'; },
    durationLabel() {
        if (this.durationType === 'custom') return this.customDays + (Number(this.customDays) === 1 ? ' Tag' : ' Tage');
        return { daily: '1 Tag', weekly: '7 Tage', monthly: '30 Tage' } [this.durationType];
    },
}" x-on:featured-modal-open.window="$dispatch('open-modal', 'featured-booking')"
    class="contents">
    <x-ui.modal name="featured-booking" title="Veranstaltung hervorheben">
        <div class="flex flex-col gap-6">
            <fieldset class="m-0 min-w-0 border-0 p-0">
                <x-ui.label as="legend" class="mb-2">Zeitraum</x-ui.label>
                <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-3">
                    <x-ui.choice name="duration_type" value="daily" title="1 Tag" :old="false" :description="$money($rates['daily'])"
                        x-model="durationType" />
                    <x-ui.choice name="duration_type" value="weekly" title="7 Tage" :old="false" checked
                        :description="$money($rates['weekly']) . ' · ' . $money($rates['weekly'] / 7) . ' pro Tag'" x-model="durationType" />
                    <x-ui.choice name="duration_type" value="monthly" title="30 Tage" :old="false"
                        :description="$money($rates['monthly']) . ' · ' . $money($rates['monthly'] / 30) . ' pro Tag'" x-model="durationType" />
                    <x-ui.choice name="duration_type" value="custom" title="Eigene Dauer" :old="false"
                        :description="$money($rates['daily']) . ' pro Tag'" x-model="durationType" />
                </div>
            </fieldset>

            <div x-show="durationType === 'custom'" x-cloak class="flex flex-wrap items-end gap-x-4 gap-y-2">
                <x-ui.field label="Anzahl Tage" id="featured_custom_days_input" type="number" x-model="customDays"
                    min="1" max="{{ config('monetization.featured_event_max_duration_days', 90) }}"
                    class="w-40" />
                <span class="pb-3 text-ui tabular-nums text-ink-soft">= <span
                        x-text="euro(customDays * rates.daily)"></span>
                    netto</span>
            </div>

            <x-ui.field label="Beginn des Zeitraums" id="featured_start_date_input" type="date" x-model="startDate"
                min="{{ now()->format('Y-m-d') }}" class="max-w-xs" />

            <div class="flex flex-col gap-2 rounded-box border border-line bg-zone p-4">
                <x-ui.summary-line label="Dauer"><span x-text="durationLabel()"></span></x-ui.summary-line>
                <x-ui.summary-line label="Kosten (netto)"><span x-text="euro(net())"></span></x-ui.summary-line>
                <x-ui.summary-line label="MwSt. (19 %)" muted><span
                        x-text="euro(net() * 0.19)"></span></x-ui.summary-line>
                <x-ui.summary-total label="Gesamt (brutto)"><span
                        x-text="euro(net() * 1.19)"></span></x-ui.summary-total>
            </div>

            <p class="m-0 text-small text-muted">
                Die Zahlung erfolgt nach Bestätigung. Sie werden zur Zahlungsseite weitergeleitet.
            </p>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" x-on:click="close()">Abbrechen</x-ui.button>
            <x-ui.button
                x-on:click="$dispatch('featured-booking-confirm', { durationType, customDays, startDate }); close();">
                Hervorhebung buchen
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
