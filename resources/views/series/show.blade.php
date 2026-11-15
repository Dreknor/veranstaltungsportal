@php
    use App\Support\Ui;

    $layout = auth()->check() ? 'layouts.app' : 'layouts.public';
    $events = $series->events->sortBy('series_position');
    $first = $series->events->first();
    $city = $series->template_data['venue_city'] ?? null;
@endphp
<x-dynamic-component :component="$layout" title="{{ $series->title }}">
    <div @class([
        'mx-auto max-w-page px-page pt-8 pb-[clamp(56px,7vw,96px)]' => !auth()->check(),
    ])>
        <x-ui.page-header :title="$series->title">
            <x-slot:meta>
                <span class="text-muted">Veranstaltungsreihe{{ $series->category ? ' · ' . $series->category->name : '' }}</span>
            </x-slot:meta>
            <x-slot:actions>
                <x-ui.button :href="route('series.book', $series)">Reihe buchen</x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="flex max-w-prose flex-col gap-6">
            @if ($series->description)
                <p class="m-0 text-base">{{ $series->description }}</p>
            @endif

            <p class="m-0 flex flex-wrap items-center gap-x-6 gap-y-2 text-ui font-medium tabular-nums">
                <span class="inline-flex items-center gap-2">
                    <x-icon.calendar class="h-4 w-4 text-muted" />
                    {{ $series->events->count() }} Termine
                </span>
                @if ($first)
                    <span class="inline-flex items-center gap-2">
                        <x-icon.clock class="h-4 w-4 text-muted" />
                        {{ Ui::time($first->start_date, $first->end_date) }}
                    </span>
                    @if ($city)
                        <span class="inline-flex items-center gap-2">
                            <x-icon.location class="h-4 w-4 text-muted" />
                            {{ $city }}
                        </span>
                    @endif
                @endif
            </p>

            <x-ui.alert tone="info" title="Wichtig">
                <p>
                    Dies ist eine Veranstaltungsreihe mit mehreren zusammenhängenden Terminen.
                    Die Buchung umfasst alle {{ $series->events->count() }} Termine.
                    Einzelne Termine können nicht separat gebucht werden.
                </p>
            </x-ui.alert>
        </div>

        <x-ui.section title="Alle Termine dieser Veranstaltungsreihe" class="mt-12">
            <x-ui.row-list>
                @foreach ($events as $event)
                    <x-ui.row :hover="false">
                        <x-slot:lead>
                            <x-ui.date-block :date="$event->start_date" />
                        </x-slot:lead>
                        <span class="text-small text-muted">Termin {{ $event->series_position }}</span>
                        <span @class(['text-h3', 'line-through' => $event->is_cancelled])>{{ Ui::date($event->start_date) }}</span>
                        <x-slot:meta>
                            <span class="text-ui font-medium tabular-nums">{{ Ui::time($event->start_date, $event->end_date) }}</span>
                            <span class="text-small text-muted tabular-nums">{{ $event->start_date->diffInMinutes($event->end_date) }} Minuten</span>
                        </x-slot:meta>
                        @if ($event->is_cancelled)
                            <x-slot:aside>
                                <x-ui.status tone="danger">Abgesagt</x-ui.status>
                                @if ($event->cancellation_reason)
                                    <span class="text-small text-muted">Grund: {{ Str::limit($event->cancellation_reason, 80) }}</span>
                                @endif
                            </x-slot:aside>
                        @endif
                    </x-ui.row>
                @endforeach
            </x-ui.row-list>
        </x-ui.section>

        @if (isset($series->template_data['venue_name']))
            <x-ui.section title="Veranstaltungsort" class="mt-12">
                <address class="flex items-start gap-3 text-base not-italic">
                    <x-icon.location class="mt-1 h-5 w-5 flex-none text-muted" />
                    <span>
                        <span class="block font-semibold">{{ $series->template_data['venue_name'] }}</span>
                        <span class="block text-ink-soft">
                            {{ $series->template_data['venue_address'] }}<br>
                            {{ $series->template_data['venue_postal_code'] }} {{ $series->template_data['venue_city'] }}<br>
                            {{ $series->template_data['venue_country'] }}
                        </span>
                    </span>
                </address>
            </x-ui.section>
        @endif

        <x-ui.box tone="zone" class="mt-12 flex flex-wrap items-center justify-between gap-x-6 gap-y-4">
            <div class="flex flex-col gap-1">
                <span class="text-small text-muted">Gesamte Veranstaltungsreihe buchen</span>
                <span class="text-h3 tabular-nums">{{ $series->events->count() }} Termine</span>
            </div>
            <x-ui.button :href="route('series.book', $series)">Reihe buchen</x-ui.button>
        </x-ui.box>
    </div>
</x-dynamic-component>
