<x-layouts.app :title="$badge->name">
    <x-ui.page-header :title="$badge->name" :back-href="route('badges.index')" back-label="Zurück zu Abzeichen" />

    <div class="max-w-prose">
        <!-- Badge Header -->
        <div class="flex flex-col items-start gap-6 sm:flex-row">
            <x-badge-icon :badge="$badge" size="lg" :earned="$hasEarned" class="flex-none" />

            <div class="min-w-0 flex-1">
                <div class="mb-3">
                    @if ($hasEarned)
                        <x-ui.status tone="success">Verdient</x-ui.status>
                    @else
                        <x-ui.status tone="neutral">Gesperrt</x-ui.status>
                    @endif
                </div>

                <p class="m-0 mb-4 text-base text-ink">{{ $badge->description }}</p>

                <p class="m-0 flex flex-wrap gap-x-6 gap-y-1 text-ui font-medium text-ink tabular-nums">
                    <span>{{ ucfirst($badge->type) }}</span>
                    <span>{{ $badge->points }} Punkte</span>
                    <span>{{ $earnedByCount }} {{ $earnedByCount === 1 ? 'Person' : 'Personen' }}</span>
                </p>

                @if ($hasEarned)
                    @php
                        $userBadge = auth()->user()->badges()->where('badge_id', $badge->id)->first();
                        $earnedAt = $userBadge->pivot->earned_at instanceof \Carbon\Carbon
                            ? $userBadge->pivot->earned_at
                            : \Carbon\Carbon::parse($userBadge->pivot->earned_at);
                    @endphp
                    <p class="m-0 mt-4 text-small text-muted tabular-nums">
                        Verdient am {{ $earnedAt->format('d.m.Y') }}
                        ({{ $earnedAt->diffForHumans() }})
                    </p>
                @endif
            </div>
        </div>

        <!-- Requirements / Progress -->
        @if (!$hasEarned && $progress && !empty($progress))
            <x-ui.section title="Anforderungen & Fortschritt" class="mt-12 border-t border-line pt-8">
                <div class="flex flex-col gap-6">
                    @foreach ($progress as $requirement => $data)
                        <div>
                            <div class="mb-2 flex justify-between gap-4 text-small tabular-nums">
                                <span class="font-medium text-ink">
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
                                <span class="text-muted">
                                    {{ $data['current'] }} / {{ $data['required'] }}
                                    <span class="ml-2 font-medium text-ink">{{ $data['percentage'] }}%</span>
                                </span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-line">
                                <div class="h-3 rounded-full bg-primary" style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                            @if ($data['completed'])
                                <div class="mt-1 text-small text-success">Erfüllt!</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-ui.section>
        @elseif(!$hasEarned && $badge->requirements)
            <x-ui.section title="Anforderungen" class="mt-12 border-t border-line pt-8">
                <ul class="m-0 list-disc space-y-2 pl-5 text-base text-ink">
                    @foreach ($badge->requirements as $requirement => $value)
                        <li>
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
                        </li>
                    @endforeach
                </ul>
            </x-ui.section>
        @endif

        <!-- Action Buttons -->
        <div class="mt-12 flex flex-wrap items-center justify-between gap-4 border-t border-line pt-8">
            <x-ui.button variant="secondary" :href="route('badges.index')">
                <x-slot:icon><x-icon.arrow-left /></x-slot:icon>
                Zurück zur Übersicht
            </x-ui.button>

            @if (!$hasEarned)
                <x-ui.button :href="route('events.index')">
                    <x-slot:icon><x-icon.calendar /></x-slot:icon>
                    Veranstaltungen entdecken
                </x-ui.button>
            @endif
        </div>
    </div>
</x-layouts.app>
