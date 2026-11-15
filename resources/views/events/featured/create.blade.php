<x-layouts.app>
    <x-ui.page-header title="Veranstaltung hervorheben"
        subtitle="Wählen Sie die Dauer, für die Ihre Veranstaltung hervorgehoben werden soll."
        :back-href="route('organizer.events.edit', $event)" back-label="Zur Veranstaltung" />

    @if ($activeFee)
        <x-ui.alert tone="success" class="mb-8 max-w-form" title="Aktiv">
            Ihre Veranstaltung ist aktuell bis {{ $activeFee->featured_end_date->format('d.m.Y') }} hervorgehoben.
        </x-ui.alert>
    @endif

    <form method="POST" action="{{ route('organizer.featured-events.store', $event) }}" class="flex max-w-form flex-col gap-y-8">
        @csrf
        <p class="m-0 text-small text-muted">* Pflichtfeld</p>

        <fieldset class="m-0 min-w-0 border-0 p-0">
            <x-ui.label as="legend" required class="mb-2">Dauer</x-ui.label>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-3">
                <x-ui.choice name="duration_type" value="daily" title="1 Tag" checked required>
                    <x-slot:aside>{{ \App\Support\Ui::money($pricing['daily']['rate']) }}</x-slot:aside>
                </x-ui.choice>
                <x-ui.choice name="duration_type" value="weekly" title="7 Tage" required>
                    <x-slot:aside>{{ \App\Support\Ui::money($pricing['weekly']['rate']) }}</x-slot:aside>
                </x-ui.choice>
                <x-ui.choice name="duration_type" value="monthly" title="30 Tage" required>
                    <x-slot:aside>{{ \App\Support\Ui::money($pricing['monthly']['rate']) }}</x-slot:aside>
                </x-ui.choice>
                <x-ui.choice name="duration_type" value="custom" title="Individuell" description="Anzahl unten angeben" required />
            </div>
            <x-ui.field-error name="duration_type" />
        </fieldset>

        <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            <x-ui.field label="Startdatum" name="start_date" type="date" :value="now()->format('Y-m-d')" required />
            <x-ui.field label="Eigene Tage" name="custom_days" type="number" optional min="1"
                max="{{ config('monetization.featured_event_max_duration_days') }}"
                hint="Nur bei der Dauer „Individuell“." />
        </div>

        <div>
            <x-ui.button type="submit">Antrag erstellen</x-ui.button>
        </div>
    </form>
</x-layouts.app>
