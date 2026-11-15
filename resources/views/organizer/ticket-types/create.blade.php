<x-layouts.app title="Ticket-Typ erstellen">
    <x-ui.page-header
        title="Ticket-Typ erstellen"
        :subtitle="'für „' . $event->title . '“'"
        :back-href="route('organizer.events.ticket-types.index', $event)"
        back-label="Zurück zur Übersicht"
    />

    <form action="{{ route('organizer.events.ticket-types.store', $event) }}" method="POST" class="flex max-w-form flex-col gap-5">
        @csrf
        <p class="m-0 text-small text-muted">* Pflichtfeld</p>

        <x-ui.field label="Name" name="name" required />
        <x-ui.textarea label="Beschreibung" name="description" optional rows="3" />

        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            <x-ui.field label="Preis (€)" name="price" type="number" step="0.01" min="0" value="0.00" required />
            <x-ui.field label="Verfügbare Menge" name="quantity_available" type="number" min="1" optional hint="Leer lassen für unbegrenzt." />
        </div>

        <x-ui.field label="Max. pro Buchung" name="max_per_booking" type="number" min="1" value="10" optional />

        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            <x-ui.field label="Verkaufsstart" name="sale_start" type="datetime-local" optional />
            <x-ui.field label="Verkaufsende" name="sale_end" type="datetime-local" optional />
        </div>

        <x-ui.checkbox name="is_available" value="1" :checked="true" label="Aktiv" />

        <div class="flex flex-wrap justify-end gap-3 border-t border-line pt-6">
            <x-ui.button variant="secondary" :href="route('organizer.events.ticket-types.index', $event)">Abbrechen</x-ui.button>
            <x-ui.button type="submit">Ticket-Typ erstellen</x-ui.button>
        </div>
    </form>
</x-layouts.app>
