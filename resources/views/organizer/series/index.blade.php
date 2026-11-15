<x-layouts.app title="Veranstaltungsreihen">
    <x-ui.page-header title="Veranstaltungsreihen"
        subtitle="Veranstaltungen mit mehreren zusammenhängenden Terminen (Kurse, Weiterbildungen, Workshop-Serien).">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('organizer.events.create')">Einzelne Veranstaltung</x-ui.button>
            <x-ui.button :href="route('organizer.series.create')">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Veranstaltungsreihe erstellen
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.tabs label="Veranstaltungen" class="mb-8">
        <x-ui.tab :href="route('organizer.events.index')">Alle Veranstaltungen</x-ui.tab>
        <x-ui.tab :href="route('organizer.series.index')" active>Veranstaltungsreihen</x-ui.tab>
    </x-ui.tabs>

    @if ($series->isEmpty())
        <x-ui.empty title="Keine Veranstaltungsreihen" :action-href="route('organizer.series.create')"
            action-label="Erste Reihe erstellen" action-variant="primary">
            Erstellen Sie Veranstaltungen mit mehreren Terminen wie Kursreihen, Weiterbildungen oder Workshop-Serien.
        </x-ui.empty>
    @else
        <x-ui.row-list>
            @foreach ($series as $s)
                <x-ui.row>
                    <span class="text-small text-muted">{{ $s->category->name }}</span>
                    <a href="{{ route('organizer.series.show', $s) }}" class="text-h3 text-ink hover:underline">{{ $s->title }}</a>
                    @if ($s->description)
                        <p class="m-0 text-small text-muted">{{ \Illuminate\Support\Str::limit($s->description, 150) }}</p>
                    @endif

                    <x-slot:meta>
                        <span class="text-ui font-medium">{{ $s->getRecurrenceDescription() }}</span>
                        <span class="text-small text-muted tabular-nums">
                            {{ $s->events_count }} Termine@if ($s->recurrence_end_date), bis {{ $s->recurrence_end_date->format('d.m.Y') }}@endif
                        </span>
                    </x-slot:meta>

                    <x-slot:aside>
                        <x-ui.status :tone="$s->is_active ? 'success' : 'neutral'">{{ $s->is_active ? 'Aktiv' : 'Inaktiv' }}</x-ui.status>
                    </x-slot:aside>

                    <x-slot:action class="lg:w-auto">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.button size="sm" variant="secondary" :href="route('organizer.series.show', $s)">Details</x-ui.button>
                            <x-ui.button size="sm" variant="secondary" :href="route('organizer.series.edit', $s)">Bearbeiten</x-ui.button>
                            <form method="POST" action="{{ route('organizer.series.destroy', $s) }}"
                                onsubmit="return confirm('Reihe wirklich löschen? Alle zugehörigen Veranstaltungen werden gelöscht!');">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" size="sm" variant="danger">Löschen</x-ui.button>
                            </form>
                        </div>
                    </x-slot:action>
                </x-ui.row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-6">
            {{ $series->links() }}
        </div>
    @endif
</x-layouts.app>
