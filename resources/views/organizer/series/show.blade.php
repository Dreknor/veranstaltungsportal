<x-layouts.app title="Veranstaltungsreihe Details">
    <x-ui.page-header :title="$series->title" :subtitle="'Veranstaltungsreihe mit ' . $series->total_events . ' Terminen'"
        :back-href="route('organizer.series.index')" back-label="Zurück zur Übersicht" />

    <x-ui.section title="Details der Veranstaltungsreihe" class="mb-12">
        <x-ui.dl>
            <x-ui.dl-item label="Kategorie">{{ $series->category->name }}</x-ui.dl-item>
            <x-ui.dl-item label="Termin-Rhythmus">{{ $series->getRecurrenceDescription() }}</x-ui.dl-item>
            <x-ui.dl-item label="Anzahl Termine" num>{{ $series->total_events }} Termine</x-ui.dl-item>
            @if ($series->recurrence_end_date)
                <x-ui.dl-item label="Letzter Termin bis" num>{{ $series->recurrence_end_date->format('d.m.Y') }}</x-ui.dl-item>
            @endif
            @if ($series->description)
                <x-ui.dl-item label="Beschreibung"><span class="whitespace-pre-line">{{ $series->description }}</span></x-ui.dl-item>
            @endif
        </x-ui.dl>
    </x-ui.section>

    <x-ui.section title="Alle Termine ({{ $series->events->count() }})">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('organizer.events.create')">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Neuen Termin hinzufügen
            </x-ui.button>
        </x-slot:actions>

        @if ($series->events->count() > 0)
            <x-ui.alert tone="info" class="mb-6">
                <strong>Hinweis:</strong> Die Termine wurden als Platzhalter erstellt.
                Bitte passen Sie die Daten, Uhrzeiten und Details für jeden Termin individuell an.
            </x-ui.alert>

            <x-ui.row-list>
                @foreach ($series->events as $event)
                    <x-ui.row>
                        <x-slot:lead><x-ui.date-block :date="$event->start_date" /></x-slot:lead>

                        <span @class(['text-h3 text-ink', 'line-through' => $event->is_cancelled])>
                            Termin {{ $event->series_position }}: {{ $event->start_date->format('d.m.Y') }}
                        </span>
                        @if ($event->is_cancelled && $event->cancellation_reason)
                            <span class="text-small text-danger"><strong>Absagegrund:</strong> {{ Str::limit($event->cancellation_reason, 100) }}</span>
                        @endif

                        <x-slot:meta>
                            <span class="text-ui font-medium tabular-nums">{{ $event->start_date->format('H:i') }}–{{ $event->end_date->format('H:i') }} Uhr</span>
                            @if ($event->bookings->count() > 0)
                                <span class="text-small text-muted tabular-nums">{{ $event->bookings->count() }} Buchungen</span>
                            @endif
                        </x-slot:meta>

                        <x-slot:aside>
                            @if ($event->is_cancelled)
                                <x-ui.status tone="danger">Abgesagt</x-ui.status>
                            @elseif ($event->is_published)
                                <x-ui.status tone="success">Veröffentlicht</x-ui.status>
                            @else
                                <x-ui.status tone="neutral">Entwurf</x-ui.status>
                            @endif
                        </x-slot:aside>

                        <x-slot:action class="lg:w-auto">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.button size="sm" variant="secondary" :href="route('organizer.events.edit', $event)">Bearbeiten</x-ui.button>
                                <x-ui.button size="sm" variant="secondary" :href="route('events.show', $event->slug)" target="_blank">Ansehen</x-ui.button>
                            </div>
                        </x-slot:action>
                    </x-ui.row>
                @endforeach
            </x-ui.row-list>
        @else
            <x-ui.empty>Noch keine Termine vorhanden.</x-ui.empty>
        @endif
    </x-ui.section>
</x-layouts.app>
