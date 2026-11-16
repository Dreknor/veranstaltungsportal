@use('App\Support\Ui')

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
    <div class="mb-12 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-ui.stat label="Abzeichen" :value="$stats['earned_badges'] . ' von ' . $stats['total_badges']" />
        <x-ui.stat label="Fortschritt" :value="Ui::percent($stats['completion_percentage'])" />
        <x-ui.stat label="Punkte" :value="Ui::number($stats['total_points'])" />
        <x-ui.stat label="Zuletzt" :value="$stats['recent_badges']->isNotEmpty() ? $stats['recent_badges']->first()->name : 'Noch keine'"
            class="[&>span:nth-child(2)]:truncate [&>span:nth-child(2)]:text-h3" />
    </div>

    <!-- Earned Badges -->
    @if ($earnedBadges->isNotEmpty())
        <x-ui.section :title="'Verdiente Abzeichen (' . $earnedBadges->count() . ')'" class="mb-12">
            <x-ui.row-list>
                @foreach ($earnedBadges as $badge)
                    @php
                        $earnedAt =
                            $badge->pivot->earned_at instanceof \Carbon\Carbon
                                ? $badge->pivot->earned_at
                                : \Carbon\Carbon::parse($badge->pivot->earned_at);
                        $highlighted = (bool) $badge->pivot->is_highlighted;
                    @endphp
                    <x-ui.row>
                        <x-slot:lead>
                            <x-badge-icon :badge="$badge" size="sm" />
                        </x-slot:lead>
                        <span class="text-small text-muted">{{ Ui::badgeType($badge->type) }}</span>
                        <a href="{{ route('badges.show', $badge) }}"
                            class="text-h3 text-ink no-underline hover:underline">{{ $badge->name }}</a>
                        @if ($badge->description)
                            <span class="line-clamp-2 text-small text-ink-soft">{{ $badge->description }}</span>
                        @endif
                        <x-slot:meta>
                            <span class="text-ui font-medium tabular-nums">{{ Ui::number($badge->points) }}
                                Punkte</span>
                            <span class="text-small text-muted tabular-nums">Verdient am
                                {{ $earnedAt->format('d.m.Y') }}</span>
                        </x-slot:meta>
                        <x-slot:action>
                            <x-ui.button variant="secondary" full onclick="toggleHighlight({{ $badge->id }})"
                                title="Als Favorit markieren" aria-pressed="{{ $highlighted ? 'true' : 'false' }}">
                                <x-slot:icon>
                                    @if ($highlighted)
                                        <x-icon.star-filled />
                                    @else
                                        <x-icon.star />
                                    @endif
                                </x-slot:icon>
                                Favorit
                            </x-ui.button>
                        </x-slot:action>
                    </x-ui.row>
                @endforeach
            </x-ui.row-list>
        </x-ui.section>
    @endif

    <!-- Badges by Type -->
    <div class="mb-12 grid gap-4 sm:grid-cols-3">
        <x-ui.stat :label="Ui::badgeType('attendance')" :value="$stats['badges_by_type']['attendance']" sub="verdiente Abzeichen" />
        <x-ui.stat :label="Ui::badgeType('achievement')" :value="$stats['badges_by_type']['achievement']" sub="verdiente Abzeichen" />
        <x-ui.stat :label="Ui::badgeType('special')" :value="$stats['badges_by_type']['special']" sub="verdiente Abzeichen" />
    </div>

    <!-- Unearned Badges -->
    @if ($unearnedBadges->isNotEmpty())
        <x-ui.section :title="'Noch zu verdienen (' . $unearnedBadges->count() . ')'">
            <x-ui.row-list>
                @foreach ($unearnedBadges as $badge)
                    @php
                        // Angezeigt wird die Anforderung, die am weitesten vom Ziel entfernt ist.
                        $requirements = collect($badgeProgress[$badge->id] ?? []);
                        $openKey = $requirements->sortBy('percentage')->keys()->first();
                        $open = $openKey !== null ? $requirements[$openKey] : null;
                        $allMet = $requirements->isNotEmpty() && $requirements->every(fn($r) => $r['completed']);
                    @endphp
                    <x-ui.row>
                        <x-slot:lead>
                            <x-badge-icon :badge="$badge" size="sm" :earned="false" />
                        </x-slot:lead>
                        <span class="text-small text-muted">{{ Ui::badgeType($badge->type) }}</span>
                        <a href="{{ route('badges.show', $badge) }}"
                            class="text-h3 text-ink no-underline hover:underline">{{ $badge->name }}</a>
                        @if ($badge->description)
                            <span class="line-clamp-2 text-small text-ink-soft">{{ $badge->description }}</span>
                        @endif
                        <x-slot:meta>
                            <span class="text-ui font-medium tabular-nums">{{ Ui::number($badge->points) }}
                                Punkte</span>
                            @if ($open && !$allMet)
                                <span class="text-small text-muted tabular-nums">
                                    {{ Ui::badgeRequirement($openKey) }}:
                                    {{ Ui::number(min($open['current'], $open['required'])) }} von
                                    {{ Ui::number($open['required']) }}
                                </span>
                            @endif
                        </x-slot:meta>
                        <x-slot:aside>
                            @if ($allMet)
                                <x-ui.status tone="success">Anforderungen erfüllt</x-ui.status>
                            @elseif ($open)
                                <span class="text-small text-ink-soft">{{ Ui::percent($open['percentage']) }}</span>
                                <x-ui.progress :value="$open['percentage']" decorative class="w-28 lg:w-full" />
                            @endif
                        </x-slot:aside>
                    </x-ui.row>
                @endforeach
            </x-ui.row-list>
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
