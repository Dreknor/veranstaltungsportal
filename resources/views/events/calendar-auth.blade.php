<x-layouts.app title="Veranstaltungskalender">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Veranstaltungskalender</h1>
                    <p class="text-gray-600 mt-2">{{ \Carbon\Carbon::create($year, $month)->locale('de')->isoFormat('MMMM YYYY') }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('events.calendar', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year]) }}"
                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        ← Vorheriger Monat
                    </a>
                    <a href="{{ route('events.calendar', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year]) }}"
                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Nächster Monat →
                    </a>
                    <a href="{{ route('events.index') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Zur Listenansicht
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="grid grid-cols-7 gap-4 mb-4">
                    @foreach(['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'] as $day)
                        <div class="text-center font-semibold text-gray-700">{{ $day }}</div>
                    @endforeach
                </div>

                @php
                    $date = \Carbon\Carbon::create($year, $month, 1);
                    $daysInMonth = $date->daysInMonth;
                    $firstDayOfWeek = $date->dayOfWeekIso;

                    // Gruppiere Events nach Tag - mehrtägige Events erscheinen an jedem Tag
                    $eventsGroupedByDay = collect();
                    foreach($events as $event) {
                        $startDate = $event->start_date->copy()->startOfDay();
                        $endDate = $event->end_date->copy()->startOfDay();

                        // Durchlaufe jeden Tag des Events
                        $currentEventDate = $startDate->copy();
                        while($currentEventDate <= $endDate) {
                            $dateString = $currentEventDate->format('Y-m-d');
                            if (!$eventsGroupedByDay->has($dateString)) {
                                $eventsGroupedByDay->put($dateString, collect());
                            }
                            $eventsGroupedByDay->get($dateString)->push($event);
                            $currentEventDate->addDay();
                        }
                    }
                @endphp

                <div class="grid grid-cols-7 gap-4">
                    {{-- Leerfelder für den Start --}}
                    @for($i = 1; $i < $firstDayOfWeek; $i++)
                        <div class="h-24 bg-gray-50 rounded border border-gray-200"></div>
                    @endfor

                    {{-- Tage --}}
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $currentDate = \Carbon\Carbon::create($year, $month, $day);
                            $dateString = $currentDate->format('Y-m-d');
                            $dayEvents = $eventsGroupedByDay->get($dateString, collect());
                        @endphp

                        <div class="h-24 border border-gray-200 rounded hover:border-blue-500 transition p-2 {{ $currentDate->isToday() ? 'bg-blue-50 border-blue-300' : 'bg-white' }}">
                            <div class="font-semibold text-sm mb-1 {{ $currentDate->isToday() ? 'text-blue-600' : 'text-gray-700' }}">
                                {{ $day }}
                            </div>
                            <div class="space-y-1 overflow-y-auto" style="max-height: 60px;">
                                @foreach($dayEvents->take(2) as $event)
                                    <a href="{{ route('events.show', $event->slug) }}"
                                       class="block text-xs px-1 py-0.5 rounded truncate hover:opacity-80"
                                       style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                        {{ $event->title }}
                                    </a>
                                @endforeach
                                @if($dayEvents->count() > 2)
                                    <div class="text-xs text-gray-500 px-1">+{{ $dayEvents->count() - 2 }} weitere</div>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Legende --}}
            <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Kategorien</h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @foreach($events->pluck('category')->unique('id') as $category)
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: {{ $category->color }}"></div>
                            <span class="text-sm text-gray-700">{{ $category->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>

