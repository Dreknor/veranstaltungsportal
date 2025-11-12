<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('badges.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i> Zurück zu Abzeichen
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight inline-block">
                    {{ $badge->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <!-- Badge Header -->
                    <div class="flex flex-col md:flex-row items-center md:items-start mb-8">
                        <div class="w-32 h-32 rounded-full flex items-center justify-center mb-4 md:mb-0 md:mr-8 {{ $hasEarned ? '' : 'bg-gray-100' }}"
                             style="{{ $hasEarned ? 'background-color: ' . $badge->color . '20;' : '' }}">
                            <i class="fas fa-medal text-6xl {{ $hasEarned ? '' : 'text-gray-400' }}"
                               style="{{ $hasEarned ? 'color: ' . $badge->color . ';' : '' }}"></i>
                            @if(!$hasEarned)
                                <div class="absolute">
                                    <i class="fas fa-lock text-gray-600 text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 text-center md:text-left">
                            <div class="flex items-center justify-center md:justify-start mb-2">
                                <h3 class="text-3xl font-bold text-gray-900">{{ $badge->name }}</h3>
                                @if($hasEarned)
                                    <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i> Verdient
                                    </span>
                                @else
                                    <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-lock mr-1"></i> Gesperrt
                                    </span>
                                @endif
                            </div>

                            <p class="text-gray-600 mb-4">{{ $badge->description }}</p>

                            <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                                <div class="flex items-center text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                          style="background-color: {{ $badge->color }}20; color: {{ $badge->color }};">
                                        <i class="fas fa-tag mr-2"></i> {{ ucfirst($badge->type) }}
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star mr-2"></i> {{ $badge->points }} Punkte
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-users mr-2"></i> {{ $earnedByCount }} {{ $earnedByCount === 1 ? 'Person' : 'Personen' }}
                                    </span>
                                </div>
                            </div>

                            @if($hasEarned)
                                @php
                                    $userBadge = auth()->user()->badges()->where('badge_id', $badge->id)->first();
                                    $earnedAt = $userBadge->pivot->earned_at instanceof \Carbon\Carbon
                                        ? $userBadge->pivot->earned_at
                                        : \Carbon\Carbon::parse($userBadge->pivot->earned_at);
                                @endphp
                                <div class="mt-4 text-sm text-gray-600">
                                    <i class="far fa-clock mr-1"></i>
                                    Verdient am {{ $earnedAt->format('d.m.Y') }}
                                    ({{ $earnedAt->diffForHumans() }})
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Requirements / Progress -->
                    @if(!$hasEarned && $progress && !empty($progress))
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-tasks mr-2"></i>Anforderungen & Fortschritt
                            </h4>
                            <div class="space-y-4">
                                @foreach($progress as $requirement => $data)
                                    <div>
                                        <div class="flex justify-between text-sm mb-2">
                                            <span class="font-medium text-gray-700">
                                                @switch($requirement)
                                                    @case('bookings_count')
                                                        Anzahl Buchungen
                                                        @break
                                                    @case('events_attended')
                                                        Besuchte Veranstaltungen
                                                        @break
                                                    @case('events_organized')
                                                        Organisierte Veranstaltungen
                                                        @break
                                                    @case('reviews_written')
                                                        Geschriebene Bewertungen
                                                        @break
                                                    @case('total_hours_attended')
                                                        Fortbildungsstunden
                                                        @break
                                                    @case('categories_explored')
                                                        Verschiedene Kategorien
                                                        @break
                                                    @case('early_bird_bookings')
                                                        Frühbuchungen
                                                        @break
                                                    @default
                                                        {{ $requirement }}
                                                @endswitch
                                            </span>
                                            <span class="text-gray-600">
                                                {{ $data['current'] }} / {{ $data['required'] }}
                                                <span class="ml-2 text-blue-600 font-medium">{{ $data['percentage'] }}%</span>
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div class="h-3 rounded-full transition-all {{ $data['completed'] ? 'bg-green-500' : 'bg-blue-600' }}"
                                                 style="width: {{ $data['percentage'] }}%"></div>
                                        </div>
                                        @if($data['completed'])
                                            <div class="text-sm text-green-600 mt-1">
                                                <i class="fas fa-check-circle"></i> Erfüllt!
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif(!$hasEarned && $badge->requirements)
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-tasks mr-2"></i>Anforderungen
                            </h4>
                            <ul class="space-y-2">
                                @foreach($badge->requirements as $requirement => $value)
                                    <li class="flex items-start">
                                        <i class="fas fa-circle text-blue-500 text-xs mt-1 mr-3"></i>
                                        <span class="text-gray-700">
                                            @switch($requirement)
                                                @case('bookings_count')
                                                    {{ $value }} {{ $value === 1 ? 'Buchung' : 'Buchungen' }} tätigen
                                                    @break
                                                @case('events_attended')
                                                    {{ $value }} {{ $value === 1 ? 'Veranstaltung' : 'Veranstaltungen' }} besuchen
                                                    @break
                                                @case('events_organized')
                                                    {{ $value }} {{ $value === 1 ? 'Veranstaltung' : 'Veranstaltungen' }} organisieren
                                                    @break
                                                @case('reviews_written')
                                                    {{ $value }} {{ $value === 1 ? 'Bewertung' : 'Bewertungen' }} schreiben
                                                    @break
                                                @case('total_hours_attended')
                                                    {{ $value }} {{ $value === 1 ? 'Stunde' : 'Stunden' }} Fortbildung absolvieren
                                                    @break
                                                @case('categories_explored')
                                                    Veranstaltungen in {{ $value }} verschiedenen Kategorien besuchen
                                                    @break
                                                @case('early_bird_bookings')
                                                    {{ $value }} Frühbuchungen (mindestens 7 Tage im Voraus)
                                                    @break
                                                @default
                                                    {{ $requirement }}: {{ $value }}
                                            @endswitch
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('badges.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                <i class="fas fa-arrow-left mr-2"></i> Zurück zur Übersicht
                            </a>

                            @if(!$hasEarned)
                                <a href="{{ route('events.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    <i class="fas fa-calendar mr-2"></i> Veranstaltungen entdecken
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

