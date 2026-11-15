<x-layouts.app title="Rabattcode erstellen">
    <x-ui.page-header
        title="Rabattcode erstellen"
        :subtitle="$event->title"
        :back-href="route('organizer.events.discount-codes.index', $event)"
        back-label="Zurück zur Übersicht"
    />

    <form action="{{ route('organizer.events.discount-codes.store', $event) }}" method="POST" class="flex max-w-form flex-col gap-5">
        @csrf
        <p class="m-0 text-small text-muted">* Pflichtfeld</p>

        <div>
            <x-ui.label for="code" required>Code</x-ui.label>
            <div class="flex flex-wrap gap-2">
                <x-ui.input name="code" id="code" required class="min-w-0 flex-[1_1_180px] uppercase" aria-describedby="code-hint code-error" />
                <x-ui.button variant="secondary" size="lg" onclick="generateCode()">Generieren</x-ui.button>
            </div>
            <x-ui.hint id="code-hint">Der Code wird automatisch in Großbuchstaben umgewandelt.</x-ui.hint>
            <x-ui.field-error name="code" />
        </div>

        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            <x-ui.select label="Rabatt-Typ" name="type" required onchange="updateValueLabel()">
                <option value="percentage" @selected(old('type', 'percentage') == 'percentage')>Prozent (%)</option>
                <option value="fixed" @selected(old('type') == 'fixed')>Festbetrag (€)</option>
            </x-ui.select>
            <div>
                <x-ui.label for="value" required><span id="value-label">Wert (%)</span></x-ui.label>
                <x-ui.input type="number" name="value" id="value" step="0.01" min="0" required aria-describedby="value-error" />
                <x-ui.field-error name="value" />
            </div>
        </div>

        <x-ui.field label="Maximale Verwendungen" name="max_uses" type="number" min="1" optional hint="Leer lassen für unbegrenzt." />

        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            <x-ui.field label="Gültig ab" name="valid_from" type="datetime-local" optional />
            <x-ui.field label="Gültig bis" name="valid_until" type="datetime-local" optional />
        </div>

        <x-ui.checkbox name="is_active" value="1" :checked="true" label="Aktiv" />

        <div class="flex flex-wrap justify-end gap-3 border-t border-line pt-6">
            <x-ui.button variant="secondary" :href="route('organizer.events.discount-codes.index', $event)">Abbrechen</x-ui.button>
            <x-ui.button type="submit">Rabattcode erstellen</x-ui.button>
        </div>
    </form>

    @push('scripts')
    <script>
        function updateValueLabel() {
            const type = document.getElementById('type').value;
            const label = document.getElementById('value-label');
            label.textContent = type === 'percentage' ? 'Wert (%)' : 'Wert (€)';
        }

        async function generateCode() {
            try {
                const response = await fetch('{{ route('organizer.events.discount-codes.generate', $event) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                document.getElementById('code').value = data.code;
            } catch (error) {
                console.error('Error generating code:', error);
            }
        }
    </script>
    @endpush
</x-layouts.app>
