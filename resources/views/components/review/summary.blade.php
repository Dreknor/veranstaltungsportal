{{--
    Ausführliche Bewertungsübersicht: Durchschnitt groß, Sterne, Anzahl und Verteilung 5 → 1 Sterne.
    Unter ReviewSummary::MIN_COUNT Bewertungen nur die Anzahl, kein Durchschnitt und keine Verteilung.
--}}
@props([
    'summary', // App\Support\ReviewSummary
    'note' => null, // Zusatz unter der Anzahl, z. B. „aus den letzten 24 Monaten“
])

<div {{ $attributes->class('flex flex-wrap items-start gap-x-12 gap-y-6') }}>
    @if ($summary->isMeaningful())
        <div class="flex items-center gap-4">
            <span
                class="text-h2 text-ink tabular-nums">{{ $summary->formattedAverage() }}</span>
            <div class="flex flex-col gap-1">
                <x-ui.stars :value="$summary->average" />
                <span class="text-small text-muted">
                    <span class="sr-only">Durchschnitt {{ $summary->formattedAverage() }} von 5 aus</span>
                    <span class="tabular-nums">{{ $summary->countLabel() }}</span>
                    @if ($note)
                        {{ $note }}
                    @endif
                </span>
            </div>
        </div>

        <dl
            class="m-0 grid min-w-0 flex-[1_1_240px] grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-x-3 gap-y-1.5 sm:max-w-sm">
            @for ($stars = 5; $stars >= 1; $stars--)
                <dt class="text-small text-muted tabular-nums">{{ $stars }}
                    {{ $stars === 1 ? 'Stern' : 'Sterne' }}</dt>
                <dd class="m-0">
                    <x-ui.progress :value="$summary->share($stars)" decorative />
                </dd>
                <dd class="m-0 text-right text-small text-ink-soft tabular-nums">{{ $summary->distribution[$stars] }}
                </dd>
            @endfor
        </dl>
    @else
        <p class="m-0 max-w-prose text-base text-muted">
            @if ($summary->hasReviews())
                {{ $summary->countLabel() }} – ein Durchschnitt wird ab {{ \App\Support\ReviewSummary::MIN_COUNT }}
                Bewertungen angezeigt.
            @else
                Noch keine Bewertungen vorhanden.
            @endif
        </p>
    @endif
</div>
