<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Eventreihen</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Veranstaltungen mit mehreren zusammenhängenden Terminen (Kurse, Weiterbildungen, Workshop-Serien)</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('organizer.series.index') }}"
                   class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 flex items-center">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Eventreihen
                </a>
                <a href="{{ route('organizer.series.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex items-center">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Eventreihe erstellen
                </a>
                <a href="{{ route('organizer.events.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Einzelnes Event
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('organizer.events.index') }}"
                   class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                    Alle Events
                </a>
                <a href="{{ route('organizer.series.index') }}"
                   class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                    Eventreihen
                </a>
            </nav>
        </div>
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            @endif

            @if($series->isEmpty())
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Keine Veranstaltungsreihen</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Erstellen Sie Veranstaltungen mit mehreren Terminen wie Kursreihen, Weiterbildungen oder Workshop-Serien.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('organizer.series.create') }}"
                               class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                                Erste Reihe erstellen
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($series as $s)
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $s->title }}
                                            </h3>
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $s->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $s->is_active ? 'Aktiv' : 'Inaktiv' }}
                                            </span>
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                                {{ $s->category->name }}
                                            </span>
                                        </div>

                                        @if($s->description)
                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                {{ \Illuminate\Support\Str::limit($s->description, 150) }}
                                            </p>
                                        @endif

                                        <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $s->getRecurrenceDescription() }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $s->events_count }} Termine</span>
                                            </div>
                                            @if($s->recurrence_end_date)
                                                <div class="flex items-center gap-1">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>Bis {{ $s->recurrence_end_date->format('d.m.Y') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <a href="{{ route('organizer.series.show', $s) }}"
                                           class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                                            Details
                                        </a>
                                        <a href="{{ route('organizer.series.edit', $s) }}"
                                           class="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                                            Bearbeiten
                                        </a>
                                        <form method="POST" action="{{ route('organizer.series.destroy', $s) }}"
                                              onsubmit="return confirm('Reihe wirklich löschen? Alle zugehörigen Events werden gelöscht!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                                Löschen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $series->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

