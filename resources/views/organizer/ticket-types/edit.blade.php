<x-layouts.app title="Ticketart bearbeiten">
    <x-ui.page-header title="Ticketart bearbeiten" :subtitle="$ticketType->name" :back-href="route('organizer.events.ticket-types.index', $event)"
        back-label="Zurück zur Übersicht" />

    @if ($ticketType->hasActiveSales())
        <x-ui.alert tone="warning" title="Aktive Buchungen vorhanden" class="mb-8 max-w-form">
            Für diese Ticketart gibt es noch aktive (nicht-stornierte) Buchungen.
            Der <strong class="font-semibold">Preis kann nicht geändert</strong> werden, solange aktive Buchungen
            bestehen.
            Alle anderen Felder (Name, Beschreibung, Verfügbarkeit etc.) können weiterhin bearbeitet werden.
        </x-ui.alert>
    @endif

    <form action="{{ route('organizer.events.ticket-types.update', [$event, $ticketType]) }}" method="POST"
        class="flex max-w-form flex-col gap-5">
        @csrf
        @method('PUT')
        <p class="m-0 text-small text-muted">* Pflichtfeld</p>

        <x-ui.field label="Name" name="name" :value="$ticketType->name" required />
        <x-ui.textarea label="Beschreibung" name="description" :value="$ticketType->description" optional rows="3" />

        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            <x-ui.field label="Preis (€)" name="price" type="number" step="0.01" min="0" :value="$ticketType->price"
                required :readonly="$ticketType->hasActiveSales()" :input-class="$ticketType->hasActiveSales() ? 'cursor-not-allowed text-muted' : ''" :hint="$ticketType->hasActiveSales()
                    ? 'Preisänderung nicht möglich – aktive Buchungen vorhanden.'
                    : null" />
            <x-ui.field label="Verfügbare Menge" name="quantity_available" type="number" min="1"
                :value="$ticketType->quantity_available" optional hint="Leer lassen für unbegrenzt." />
        </div>

        <x-ui.field label="Max. pro Buchung" name="max_per_booking" type="number" min="1" :value="$ticketType->max_per_booking"
            optional />

        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            <x-ui.field label="Verkaufsstart" name="sale_start" type="datetime-local" :value="$ticketType->sale_start ? $ticketType->sale_start->format('Y-m-d\TH:i') : ''" optional />
            <x-ui.field label="Verkaufsende" name="sale_end" type="datetime-local" :value="$ticketType->sale_end ? $ticketType->sale_end->format('Y-m-d\TH:i') : ''" optional />
        </div>

        <x-ui.checkbox name="is_available" value="1" :checked="(bool) $ticketType->is_available" label="Aktiv" />

        <div class="flex flex-wrap gap-3 border-t border-line pt-6">
            <x-ui.button type="submit">Änderungen speichern</x-ui.button>
            <x-ui.button variant="secondary" :href="route('organizer.events.ticket-types.index', $event)">Abbrechen</x-ui.button>
        </div>
    </form>
</x-layouts.app>
