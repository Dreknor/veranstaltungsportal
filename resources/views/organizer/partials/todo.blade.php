{{-- "Heute zu tun" – offene Aufgaben und anstehende Veranstaltungen --}}
@php
    $tones = [
        'amber' => 'bg-amber-50 border-amber-200 text-amber-900 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-100',
        'yellow' => 'bg-yellow-50 border-yellow-200 text-yellow-900 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-100',
        'blue' => 'bg-blue-50 border-blue-200 text-blue-900 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-100',
        'purple' => 'bg-purple-50 border-purple-200 text-purple-900 dark:bg-purple-900/20 dark:border-purple-800 dark:text-purple-100',
    ];
@endphp
<section class="mb-8 grid grid-cols-1 lg:grid-cols-2 gap-4" aria-labelledby="todo-heading">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
        <h2 id="todo-heading" class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">Zu tun</h2>
        @if(empty($todo['tasks']))
            <p class="text-sm text-gray-600 dark:text-gray-400">✓ Alles erledigt – keine offenen Freigaben, Zahlungen oder Rechnungen.</p>
        @else
            <ul class="space-y-2">
                @foreach($todo['tasks'] as $task)
                    <li>
                        <a href="{{ $task['url'] }}" class="flex items-center justify-between gap-3 rounded-lg border px-4 py-3 hover:shadow-sm transition {{ $tones[$task['tone']] }}">
                            <span class="text-sm font-medium"><span class="text-lg font-bold mr-1">{{ $task['count'] }}</span> {{ $task['label'] }}</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
        @if($todo['waiting'] > 0)
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">{{ $todo['waiting'] }} Platz/Plätze auf Wartelisten angefragt – bei Stornierungen rücken Wartende automatisch nach.</p>
        @endif
        @foreach($todo['almostFull'] as $event)
            <p class="text-sm text-amber-800 dark:text-amber-300 mt-2">
                ⚠ „{{ $event->title }}“ ist fast ausgebucht ({{ $event->max_attendees - $event->availableTickets() }}/{{ $event->max_attendees }}).
                <a href="{{ route('organizer.events.edit', $event) }}" class="underline">Kapazität prüfen</a>
            </p>
        @endforeach
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">Die nächsten 7 Tage</h2>
        @forelse($todo['soon'] as $row)
            @php $event = $row['event']; @endphp
            <div class="py-3 border-b last:border-0 border-gray-100 dark:border-gray-700">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $event->title }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $event->start_date->isToday() ? 'Heute' : ($event->start_date->isTomorrow() ? 'Morgen' : $event->start_date->translatedFormat('D, d.m.')) }},
                            {{ $event->start_date->format('H:i') }} Uhr ·
                            {{ $row['booked'] }}{{ $row['capacity'] ? '/' . $row['capacity'] : '' }} Plätze bestätigt
                            @if($row['open']) · <span class="text-amber-700 dark:text-amber-400">{{ $row['open'] }} offen</span> @endif
                        </p>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        @unless($event->isOnline())
                            <a href="{{ route('organizer.check-in.index', $event) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-600 text-white hover:bg-green-700">Check-in</a>
                        @endunless
                        <a href="{{ route('organizer.bookings.index', ['event_id' => $event->id]) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300">Buchungen</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-600 dark:text-gray-400">Keine Veranstaltungen in den nächsten 7 Tagen.</p>
        @endforelse
    </div>
</section>
