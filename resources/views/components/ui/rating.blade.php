{{--
    Kompakte Bewertungszeile: „(5 Sterne) 4,6 von 5 · 38 Bewertungen“.
    Unter ReviewSummary::MIN_COUNT Bewertungen erscheint kein Durchschnitt, nur
    „Noch zu wenige Bewertungen“ (oder gar nichts, wenn hideIfFew gesetzt ist).
    Mit href wird die Anzahl zum Link (z. B. auf das Veranstalter-Profil).
--}}
@props([
    'summary', // App\Support\ReviewSummary
    'href' => null,
    'size' => 'ui', // ui (15 px) | small (14 px, z. B. in Listenzeilen)
    'hideIfFew' => false,
    'prefix' => null, // z. B. „Veranstalter:“ vor der Zeile
])

@php
    $text = $size === 'small' ? 'text-small' : 'text-ui';
@endphp

@if ($summary->isMeaningful())
    <p {{ $attributes->class(['m-0 flex flex-wrap items-center gap-x-2 gap-y-1 text-ink-soft', $text]) }}>
        @if ($prefix)
            <span class="text-muted">{{ $prefix }}</span>
        @endif
        <x-ui.stars :value="$summary->average" size="sm" decorative />
        <span class="font-medium text-ink tabular-nums">{{ $summary->formattedAverage() }} von 5</span>
        <span aria-hidden="true" class="text-muted">·</span>
        @if ($href)
            <a href="{{ $href }}"
                class="text-ink-soft tabular-nums underline underline-offset-[3px] hover:text-ink">{{ $summary->countLabel() }}</a>
        @else
            <span class="tabular-nums">{{ $summary->countLabel() }}</span>
        @endif
    </p>
@elseif (!$hideIfFew)
    <p {{ $attributes->class(['m-0 text-muted', $text]) }}>
        @if ($prefix)
            {{ $prefix }}
        @endif
        @if ($summary->hasReviews())
            Noch zu wenige Bewertungen für einen Durchschnitt ({{ $summary->countLabel() }}).
        @else
            Noch keine Bewertungen.
        @endif
    </p>
@endif
