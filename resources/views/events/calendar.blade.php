<x-layouts.public title="Veranstaltungskalender">
    <div class="mx-auto max-w-page px-page pt-8 pb-16">
        <x-ui.page-header title="Veranstaltungskalender">
            <x-slot:actions>
                <x-ui.button variant="secondary" :href="route('events.index')">
                    <x-slot:icon><x-icon.list /></x-slot:icon>
                    Zur Listenansicht
                </x-ui.button>
            </x-slot:actions>
        </x-ui.page-header>

        @php
            $date = \Carbon\Carbon::create($year, $month, 1);
            $daysInMonth = $date->daysInMonth;
            $firstDayOfWeek = $date->dayOfWeekIso;

            // Gruppiere Events nach Tag - mehrtägige Events erscheinen an jedem Tag
            $eventsGroupedByDay = collect();
            foreach ($events as $event) {
                $startDate = $event->start_date->copy()->startOfDay();
                $endDate = $event->end_date->copy()->startOfDay();

                // Durchlaufe jeden Tag des Events
                $currentEventDate = $startDate->copy();
                while ($currentEventDate <= $endDate) {
                    $dateString = $currentEventDate->format('Y-m-d');
                    if (!$eventsGroupedByDay->has($dateString)) {
                        $eventsGroupedByDay->put($dateString, collect());
                    }
                    $eventsGroupedByDay->get($dateString)->push($event);
                    $currentEventDate->addDay();
                }
            }
        @endphp

        @php
            $monthLabel = \Carbon\Carbon::create($year, $month)->locale('de')->isoFormat('MMMM YYYY');
            // nur Tage dieses Monats (mehrtägige Termine können in den Folgemonat reichen)
            $monthPrefix = sprintf('%04d-%02d-', $year, $month);
            $daysWithEvents = $eventsGroupedByDay
                ->keys()
                ->filter(fn($key) => str_starts_with($key, $monthPrefix))
                ->sort()
                ->values();
        @endphp

        <section aria-labelledby="kalender-monat">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-x-6 gap-y-3">
                <h2 id="kalender-monat" class="m-0 text-h2 text-ink">{{ $monthLabel }}</h2>
                <nav aria-label="Monat wechseln" class="flex flex-wrap gap-2">
                    <x-ui.button variant="secondary" :href="route('events.calendar', [
                        'month' => $month == 1 ? 12 : $month - 1,
                        'year' => $month == 1 ? $year - 1 : $year,
                    ])">
                        <x-slot:icon><x-icon.arrow-left /></x-slot:icon>
                        Vorheriger Monat
                    </x-ui.button>
                    <x-ui.button variant="secondary" :href="route('events.calendar', [
                        'month' => $month == 12 ? 1 : $month + 1,
                        'year' => $month == 12 ? $year + 1 : $year,
                    ])">
                        Nächster Monat
                        <x-icon.arrow-right class="h-4 w-4 shrink-0" />
                    </x-ui.button>
                </nav>
            </div>

            {{-- Monatsraster ab 768 px --}}
            <div class="hidden overflow-hidden rounded-box border border-line md:block">
                <div class="grid grid-cols-7 border-b border-line bg-zone" aria-hidden="true">
                    @foreach (['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'] as $day)
                        <div class="px-3 py-2 text-small font-medium text-muted">{{ $day }}</div>
                    @endforeach
                </div>

                <ol class="m-0 grid list-none grid-cols-7 p-0">
                    {{-- Leerfelder für den Start --}}
                    @for ($i = 1; $i < $firstDayOfWeek; $i++)
                        <li class="min-h-28 border-r border-b border-line bg-zone" aria-hidden="true"></li>
                    @endfor

                    {{-- Tage --}}
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $currentDate = \Carbon\Carbon::create($year, $month, $day);
                            $dateString = $currentDate->format('Y-m-d');
                            $dayEvents = $eventsGroupedByDay->get($dateString, collect());
                        @endphp

                        <li @class([
                            'flex min-h-28 min-w-0 flex-col gap-1 border-b border-line p-2',
                            'border-r' => $currentDate->dayOfWeekIso !== 7,
                        ])>
                            <time datetime="{{ $dateString }}"
                                @if ($currentDate->isToday()) aria-current="date" @endif
                                @class([
                                    'inline-flex h-7 w-7 items-center justify-center rounded-full text-small font-semibold tabular-nums',
                                    'bg-primary text-on-primary' => $currentDate->isToday(),
                                    'text-ink' => !$currentDate->isToday(),
                                ])>
                                <span
                                    class="sr-only">{{ \App\Support\Ui::date(\Illuminate\Support\Carbon::instance($currentDate)) }}
                                    @if ($currentDate->isToday())
                                        , heute
                                    @endif
                                </span>
                                <span aria-hidden="true">{{ $day }}</span>
                            </time>
                            @foreach ($dayEvents->take(2) as $event)
                                <a href="{{ route('events.show', $event->slug) }}"
                                    class="block truncate text-caption text-ink no-underline underline-offset-[3px] hover:underline"
                                    title="{{ $event->title }}">
                                    @if ($event->start_date->isSameDay($currentDate))
                                        <span
                                            class="text-muted tabular-nums">{{ $event->start_date->format('H:i') }}</span>
                                    @endif
                                    {{ $event->title }}
                                </a>
                            @endforeach
                            @if ($dayEvents->count() > 2)
                                <span class="text-caption text-muted">+{{ $dayEvents->count() - 2 }} weitere</span>
                            @endif
                        </li>
                    @endfor

                    {{-- Leerfelder bis zum Wochenende --}}
                    @for ($i = ($firstDayOfWeek - 1 + $daysInMonth) % 7; $i > 0 && $i < 7; $i++)
                        <li @class([
                            'min-h-28 border-b border-line bg-zone',
                            'border-r' => $i < 6,
                        ]) aria-hidden="true"></li>
                    @endfor
                </ol>
            </div>

            {{-- Unter 768 px: Tage mit Terminen als Liste --}}
            <div class="md:hidden">
                @if ($daysWithEvents->isEmpty())
                    <x-ui.empty>In diesem Monat sind keine Veranstaltungen geplant.</x-ui.empty>
                @else
                    <ol class="m-0 list-none border-b border-line p-0">
                        @foreach ($daysWithEvents as $dateString)
                            @php $currentDate = \Carbon\Carbon::parse($dateString); @endphp
                            <li class="flex flex-col gap-1 border-t border-line py-3">
                                <h3 class="m-0 text-small font-semibold text-ink tabular-nums">
                                    {{ \App\Support\Ui::date(\Illuminate\Support\Carbon::instance($currentDate)) }}
                                    @if ($currentDate->isToday())
                                        <span class="font-normal text-muted">· heute</span>
                                    @endif
                                </h3>
                                @foreach ($eventsGroupedByDay->get($dateString) as $event)
                                    <a href="{{ route('events.show', $event->slug) }}"
                                        class="flex min-h-11 items-center gap-3 text-base text-ink no-underline underline-offset-[3px] hover:underline">
                                        <span class="w-12 flex-none text-small text-muted tabular-nums">
                                            @if ($event->start_date->isSameDay($currentDate))
                                                {{ $event->start_date->format('H:i') }}
                                            @endif
                                        </span>
                                        <span class="min-w-0">{{ $event->title }}</span>
                                    </a>
                                @endforeach
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </section>

    </div>
</x-layouts.public>
