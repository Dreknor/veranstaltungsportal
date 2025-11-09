<x-layouts.app title="Veranstaltungsreihe Details">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('organizer.series.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Zurück zur Übersicht
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">{{ $series->title }}</h1>
                <p class="text-gray-600 mt-2">Veranstaltungsreihe mit {{ $series->total_events }} Terminen</p>
            </div>

            <div class="space-y-6">
                <!-- Series Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Details der Veranstaltungsreihe</h3>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kategorie</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $series->category->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Termin-Rhythmus</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $series->getRecurrenceDescription() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Anzahl Termine</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $series->total_events }} Termine</dd>
                        </div>
                        @if($series->recurrence_end_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Letzter Termin bis</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $series->recurrence_end_date->format('d.m.Y') }}</dd>
                        </div>
                        @endif
                    </dl>

                    @if($series->description)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Beschreibung</dt>
                            <dd class="text-sm text-gray-900 whitespace-pre-line">{{ $series->description }}</dd>
                        </div>
                    @endif
                </div>

                <!-- Events List -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Alle Termine ({{ $series->events->count() }})</h3>
                        <a href="{{ route('organizer.events.create') }}"
                           class="text-sm text-green-600 hover:text-green-800 font-medium inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Neuen Termin hinzufügen
                        </a>
                    </div>

                    @if($series->events->count() > 0)
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong>Hinweis:</strong> Die Termine wurden als Platzhalter erstellt.
                                        Bitte passen Sie die Daten, Uhrzeiten und Details für jeden Termin individuell an.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                    @foreach($series->events as $event)
                        <div class="flex items-center justify-between p-4 border rounded-lg {{ $event->is_cancelled ? 'border-red-300 bg-red-50' : 'border-gray-200 hover:bg-gray-50' }} transition">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 {{ $event->is_cancelled ? 'bg-red-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center">
                                        @if($event->is_cancelled)
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium {{ $event->is_cancelled ? 'text-red-900 line-through' : 'text-gray-900' }}">
                                        Termin {{ $event->series_position }}: {{ $event->start_date->format('d.m.Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        <span class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }} Uhr
                                        </span>
                                        @if($event->bookings->count() > 0)
                                            <span class="ml-4 inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                {{ $event->bookings->count() }} Buchungen
                                            </span>
                                        @endif
                                    </div>
                                    @if($event->is_cancelled && $event->cancellation_reason)
                                        <div class="text-xs text-red-600 mt-2">
                                            <strong>Absagegrund:</strong> {{ Str::limit($event->cancellation_reason, 100) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($event->is_cancelled)
                                        <span class="inline-flex items-center rounded-full bg-red-500 px-3 py-1 text-xs font-medium text-white">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Abgesagt
                                        </span>
                                    @elseif($event->is_published)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                            ✓ Veröffentlicht
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800">
                                            Entwurf
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('organizer.events.edit', $event) }}"
                                   class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    Bearbeiten
                                </a>
                                <a href="{{ route('events.show', $event->slug) }}"
                                   target="_blank"
                                   class="text-sm text-gray-600 hover:text-gray-800">
                                    Ansehen →
                                </a>
                            </div>
                        </div>
                    @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Noch keine Termine vorhanden.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

