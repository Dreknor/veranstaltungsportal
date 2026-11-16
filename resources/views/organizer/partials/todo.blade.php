{{-- "Heute zu tun" – offene Aufgaben und anstehende Veranstaltungen --}}
<div class="mb-12 grid grid-cols-1 gap-x-12 gap-y-12 lg:grid-cols-2">
    <x-ui.section title="Zu tun" id="todo-heading">
        @if (empty($todo['tasks']))
            <p class="m-0 flex items-center gap-2 border-t border-line pt-4 text-base text-ink-soft">
                <x-icon.check-circle class="h-5 w-5 flex-none" />
                Alles erledigt – keine offenen Freigaben, Zahlungen oder Rechnungen.
            </p>
        @else
            <ul class="m-0 list-none p-0">
                @foreach ($todo['tasks'] as $task)
                    <li class="border-t border-line last:border-b">
                        <a href="{{ $task['url'] }}"
                            class="flex min-h-15 items-center justify-between gap-4 py-3 text-ui font-medium text-ink no-underline underline-offset-[3px] hover:underline">
                            <span>
                                <span class="mr-1 text-h3 tabular-nums">{{ $task['count'] }}</span>
                                {{ $task['label'] }}
                            </span>
                            <x-icon.arrow-right class="h-4 w-4 shrink-0" />
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($todo['waiting'] > 0)
            <p class="mt-4 mb-0 text-small text-muted">
                {{ trans_choice('{1} :count Platz|[0,*] :count Plätze', $todo['waiting']) }} auf Wartelisten angefragt –
                bei Stornierungen rücken Wartende
                automatisch nach.
            </p>
        @endif

        @if ($todo['almostFull']->isNotEmpty())
            <div class="mt-4 flex flex-col gap-3">
                @foreach ($todo['almostFull'] as $event)
                    <x-ui.alert tone="warning">
                        „{{ $event->title }}“ ist fast ausgebucht
                        (<span
                            class="tabular-nums">{{ $event->max_attendees - $event->availableTickets() }}/{{ $event->max_attendees }}</span>)
                        .
                        <a href="{{ route('organizer.events.edit', $event) }}">Kapazität prüfen</a>
                    </x-ui.alert>
                @endforeach
            </div>
        @endif
    </x-ui.section>

    <x-ui.section title="Die nächsten 7 Tage" id="todo-soon-heading">
        @if ($todo['soon']->isEmpty())
            <p class="m-0 border-t border-line pt-4 text-base text-muted">Keine Veranstaltungen in den nächsten 7 Tagen.
            </p>
        @else
            <ul class="m-0 list-none p-0">
                @foreach ($todo['soon'] as $row)
                    @php $event = $row['event']; @endphp
                    <li
                        class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3 border-t border-line py-3 last:border-b">
                        <div class="flex min-w-0 flex-[1_1_240px] flex-col gap-0.5">
                            <a href="{{ route('organizer.events.edit', $event) }}"
                                class="text-base font-semibold text-ink no-underline underline-offset-[3px] hover:underline">{{ $event->title }}</a>
                            <span class="text-small text-muted tabular-nums">
                                {{ $event->start_date->isToday() ? 'Heute' : ($event->start_date->isTomorrow() ? 'Morgen' : $event->start_date->translatedFormat('D, d.m.')) }},
                                {{ $event->start_date->format('H:i') }} Uhr ·
                                {{ $row['booked'] }}{{ $row['capacity'] ? '/' . $row['capacity'] : '' }}
                                {{ $row['booked'] === 1 ? 'Platz' : 'Plätze' }} bestätigt
                                @if ($row['open'])
                                    · <span class="font-medium text-warning">{{ $row['open'] }} offen</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex shrink-0 gap-2">
                            @unless ($event->isOnline())
                                <x-ui.button variant="secondary" :href="route('organizer.check-in.index', $event)">Check-in</x-ui.button>
                            @endunless
                            <x-ui.button variant="secondary" :href="route('organizer.bookings.index', ['event_id' => $event->id])">Buchungen</x-ui.button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-ui.section>
</div>
