<x-layouts.app title="Rabattcode bearbeiten: {{ $discountCode->code }}">
    <x-ui.page-header title="Rabattcode bearbeiten" :subtitle="$discountCode->code" :back-href="route('organizer.events.discount-codes.index', $event)"
        back-label="Zurück zur Übersicht" />

    <form action="{{ route('organizer.events.discount-codes.update', [$event, $discountCode]) }}" method="POST"
        class="flex max-w-form flex-col gap-5">
        @csrf
        @method('PUT')
        <p class="m-0 text-small text-muted">* Pflichtfeld</p>

        <x-ui.field label="Code" name="code" :value="$discountCode->code" required input-class="uppercase"
            hint="Der Code wird automatisch in Großbuchstaben umgewandelt." />

        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            <x-ui.select label="Rabatt-Typ" name="type" required onchange="updateValueLabel()">
                <option value="percentage" @selected(old('type', $discountCode->type) == 'percentage')>Prozent (%)</option>
                <option value="fixed" @selected(old('type', $discountCode->type) == 'fixed')>Festbetrag (€)</option>
            </x-ui.select>
            <div>
                <x-ui.label for="value" required><span id="value-label">Wert
                        ({{ $discountCode->type == 'percentage' ? '%' : '€' }})</span></x-ui.label>
                <x-ui.input type="number" name="value" id="value" step="0.01" min="0" :value="$discountCode->value"
                    required aria-describedby="value-error" />
                <x-ui.field-error name="value" />
            </div>
        </div>

        <x-ui.field label="Maximale Verwendungen" name="usage_limit" type="number" min="1" :value="$discountCode->usage_limit"
            optional hint="Leer lassen für unbegrenzt (aktuell verwendet: {{ $discountCode->usage_count }})." />

        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            <x-ui.field label="Gültig ab" name="valid_from" type="datetime-local" :value="$discountCode->valid_from ? $discountCode->valid_from->format('Y-m-d\TH:i') : ''" optional />
            <x-ui.field label="Gültig bis" name="valid_until" type="datetime-local" :value="$discountCode->valid_until ? $discountCode->valid_until->format('Y-m-d\TH:i') : ''" optional />
        </div>

        <x-ui.checkbox name="is_active" value="1" :checked="(bool) $discountCode->is_active" label="Aktiv" />

        <div class="flex flex-wrap gap-3 border-t border-line pt-6">
            <x-ui.button type="submit">Änderungen speichern</x-ui.button>
            <x-ui.button variant="secondary" :href="route('organizer.events.discount-codes.index', $event)">Abbrechen</x-ui.button>
        </div>
    </form>

    @push('scripts')
        <script>
            function updateValueLabel() {
                const type = document.getElementById('type').value;
                const label = document.getElementById('value-label');
                label.textContent = type === 'percentage' ? 'Wert (%)' : 'Wert (€)';
            }
        </script>
    @endpush
</x-layouts.app>
