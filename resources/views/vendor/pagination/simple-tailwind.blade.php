{{-- Einfache Seitennavigation (ohne Gesamtzahl): Sekundärbuttons "Zurück" und "Weiter". --}}
@php
    $edge = 'inline-flex h-11 items-center justify-center gap-2 rounded-control border px-4 text-ui font-medium whitespace-nowrap no-underline transition-colors duration-150';
    $edgeOn = $edge.' border-line-strong bg-surface text-ink hover:border-ink hover:bg-zone';
    $edgeOff = $edge.' cursor-not-allowed border-line bg-zone text-disabled';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Seitennavigation" class="flex items-center justify-between gap-4">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" class="{{ $edgeOff }}">
                <x-icon.arrow-left class="h-4 w-4" />
                Zurück
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $edgeOn }}">
                <x-icon.arrow-left class="h-4 w-4" />
                Zurück
            </a>
        @endif

        <p class="m-0 text-small text-muted tabular-nums">Seite {{ $paginator->currentPage() }}</p>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $edgeOn }}">
                Weiter
                <x-icon.arrow-right class="h-4 w-4" />
            </a>
        @else
            <span aria-disabled="true" class="{{ $edgeOff }}">
                Weiter
                <x-icon.arrow-right class="h-4 w-4" />
            </span>
        @endif
    </nav>
@endif
