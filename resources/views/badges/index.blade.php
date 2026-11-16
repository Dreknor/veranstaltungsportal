<x-layouts.app title="Meine Abzeichen">
    <x-ui.page-header :title="__('Meine Abzeichen')">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('badges.leaderboard')">
                <x-slot:icon><x-icon.medal /></x-slot:icon>
                Bestenliste
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Statistics -->
    <div class="mb-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Abzeichen" :value="$stats['earned_badges'] . ' / ' . $stats['total_badges']" />
        <x-ui.stat label="Fortschritt" :value="$stats['completion_percentage'] . '%'" />
        <x-ui.stat label="Punkte" :value="$stats['total_points']" />
        <x-ui.stat label="Zuletzt"
            :value="$stats['recent_badges']->isNotEmpty() ? $stats['recent_badges']->first()->name : 'Noch keine'"
            class="[&>span:nth-child(2)]:truncate [&>span:nth-child(2)]:text-h3" />
    </div>

    <!-- Earned Badges -->
    @if ($earnedBadges->isNotEmpty())
        <x-ui.section :title="'Verdiente Abzeichen (' . $earnedBadges->count() . ')'" class="mb-12">
            <ul class="m-0 grid list-none grid-cols-[repeat(auto-fill,minmax(248px,1fr))] gap-4 p-0">
                @foreach ($earnedBadges as $badge)
                    <li class="relative flex flex-col items-center rounded-box border border-line p-4 text-center transition-colors duration-150 hover:bg-zone-hover"
                        onclick="window.location.href='{{ route('badges.show', $badge) }}'">
                        <!-- Highlight Toggle -->
                        <button type="button" onclick="event.stopPropagation(); toggleHighlight({{ $badge->id }})"
                            class="absolute right-1 top-1 inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-control text-ink"
                            title="{{ $badge->pivot->is_highlighted ? 'Von Favoriten entfernen' : 'Als Favorit markieren' }}"
                            aria-label="{{ $badge->pivot->is_highlighted ? 'Von Favoriten entfernen' : 'Als Favorit markieren' }}"
                            aria-pressed="{{ $badge->pivot->is_highlighted ? 'true' : 'false' }}">
                            @if ($badge->pivot->is_highlighted)
                                <x-icon.star-filled class="h-5 w-5" />
                            @else
                                <x-icon.star class="h-5 w-5" />
                            @endif
                        </button>

                        <x-badge-icon :badge="$badge" />
                        <h3 class="m-0 mb-1 mt-3 text-base font-semibold text-ink">
                            <a href="{{ route('badges.show', $badge) }}" class="text-ink no-underline hover:underline"
                                onclick="event.stopPropagation()">{{ $badge->name }}</a>
                        </h3>
                        <p class="m-0 mb-2 line-clamp-2 text-small text-muted">{{ $badge->description }}</p>
                        <p class="m-0 flex flex-wrap items-center justify-center gap-x-3 text-small text-ink-soft tabular-nums">
                            <span>{{ $badge->points }} Punkte</span>
                            <span>
                                @if ($badge->pivot->earned_at instanceof \Carbon\Carbon)
                                    {{ $badge->pivot->earned_at->diffForHumans() }}
                                @else
                                    {{ \Carbon\Carbon::parse($badge->pivot->earned_at)->diffForHumans() }}
                                @endif
                            </span>
                        </p>
                    </li>
                @endforeach
            </ul>
        </x-ui.section>
    @endif

    <!-- Badges by Type -->
    <div class="mb-12 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Teilnahme" :value="$stats['badges_by_type']['attendance']" />
        <x-ui.stat label="Leistung" :value="$stats['badges_by_type']['achievement']" />
        <x-ui.stat label="Besonders" :value="$stats['badges_by_type']['special']" />
    </div>

    <!-- Unearned Badges -->
    @if ($unearnedBadges->isNotEmpty())
        <x-ui.section :title="'Noch zu verdienen (' . $unearnedBadges->count() . ')'">
            <ul class="m-0 grid list-none grid-cols-[repeat(auto-fill,minmax(248px,1fr))] gap-4 p-0">
                @foreach ($unearnedBadges as $badge)
                    <li class="flex flex-col items-center rounded-box border border-line p-4 text-center transition-colors duration-150 hover:bg-zone-hover"
                        onclick="window.location.href='{{ route('badges.show', $badge) }}'">
                        <x-badge-icon :badge="$badge" :earned="false" />
                        <h3 class="m-0 mb-1 mt-3 text-base font-semibold text-ink">
                            <a href="{{ route('badges.show', $badge) }}" class="text-ink no-underline hover:underline"
                                onclick="event.stopPropagation()">{{ $badge->name }}</a>
                        </h3>
                        <p class="m-0 mb-2 line-clamp-2 text-small text-muted">{{ $badge->description }}</p>
                        <p class="m-0 text-small text-ink-soft tabular-nums">{{ $badge->points }} Punkte</p>

                        <!-- Progress Bar -->
                        @if (isset($badgeProgress[$badge->id]) && !empty($badgeProgress[$badge->id]))
                            @php
                                $firstProgress = array_values($badgeProgress[$badge->id])[0];
                            @endphp
                            <div class="mt-3 w-full">
                                <div class="mb-1 flex justify-between text-small text-muted tabular-nums">
                                    <span>Fortschritt</span>
                                    <span>{{ $firstProgress['percentage'] }}%</span>
                                </div>
                                <x-ui.progress :value="$firstProgress['percentage']" decorative />
                                <div class="mt-1 text-small text-muted tabular-nums">
                                    {{ $firstProgress['current'] }} / {{ $firstProgress['required'] }}
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </x-ui.section>
    @endif

    @push('scripts')
        <script>
            function toggleHighlight(badgeId) {
                fetch(`/badges/${badgeId}/toggle-highlight`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        </script>
    @endpush
</x-layouts.app>
