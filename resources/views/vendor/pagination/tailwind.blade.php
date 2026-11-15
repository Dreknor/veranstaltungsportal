{{--
    Seitennavigation (B3): Sekundärbuttons "Zurück" und "Weiter", dazwischen Seitenzahlen als
    44-px-Flächen; aktive Seite Fläche primary, Schrift weiß. Unter 768 px nur Zurück/Weiter und
    "Seite x von y". Darüber: "21–40 von 133 Einträgen".
--}}
@php
    $edge =
        'inline-flex h-11 items-center justify-center gap-2 rounded-control border px-4 text-ui font-medium whitespace-nowrap no-underline transition-colors duration-150';
    $edgeOn = $edge . ' border-line-strong bg-surface text-ink hover:border-ink hover:bg-zone';
    $edgeOff = $edge . ' cursor-not-allowed border-line bg-zone text-disabled';
    $page =
        'inline-flex h-11 min-w-11 items-center justify-center rounded-control px-2 text-ui font-medium tabular-nums no-underline transition-colors duration-150';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Seitennavigation" class="flex flex-col gap-3">
        <div class="flex items-center justify-between gap-4">
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

            <p class="m-0 text-small text-muted tabular-nums md:hidden">
                Seite {{ $paginator->currentPage() }} von {{ $paginator->lastPage() }}
            </p>

            <ul class="m-0 hidden list-none items-center gap-1 p-0 md:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li aria-hidden="true" class="{{ $page }} text-muted">…</li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $number => $url)
                            <li>
                                @if ($number == $paginator->currentPage())
                                    <span aria-current="page" class="{{ $page }} bg-primary text-on-primary">
                                        <span class="sr-only">Seite</span> {{ $number }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="{{ $page }} text-ink hover:bg-zone">
                                        <span class="sr-only">Seite</span> {{ $number }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach
            </ul>

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
        </div>

        @if ($paginator->firstItem())
            <p class="m-0 hidden text-small text-muted tabular-nums md:block">
                {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} von {{ $paginator->total() }} Einträgen
            </p>
        @endif
    </nav>
@endif
