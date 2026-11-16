{{--
    Seitenkopf: optionaler Zurück-Link, H1 (title), optionale Unterzeile, rechts höchstens
    ein Primär- und ein Sekundärbutton (Slot actions). Slot meta: zusätzliche Zeile unter dem Titel.
--}}
@props([
    'title' => null, // ersatzweise Slot
    'subtitle' => null,
    'backHref' => null,
    'backLabel' => 'Zurück',
    'size' => 'title', // title (Standard) | display (nur Startseite)
    'actions' => null,
    'meta' => null,
    'level' => 'h1', // h1 (Standard); h2 nur für Vorschauen, z. B. im Styleguide
])

<div {{ $attributes->class('mb-8 flex flex-col') }}>
    @if ($backHref)
        <a href="{{ $backHref }}"
            class="mb-2 inline-flex min-h-11 items-center gap-2 self-start text-ui font-medium text-ink-soft no-underline underline-offset-[3px] hover:underline">
            <x-icon.arrow-left class="h-4 w-4" />
            {{ $backLabel }}
        </a>
    @endif
    <div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-4">
        <div class="flex min-w-0 flex-col gap-2">
            <{{ $level }} @class([
                'm-0 text-ink',
                'text-title' => $size !== 'display',
                'text-display' => $size === 'display',
            ])>{{ $title ?? $slot }}</{{ $level }}>
            @if ($subtitle)
                <p class="m-0 max-w-prose text-base text-muted">{{ $subtitle }}</p>
            @endif
            @if ($meta)
                <div class="text-small text-ink-soft">{{ $meta }}</div>
            @endif
        </div>
        @if ($actions)
            <div class="flex flex-wrap items-center gap-3">{{ $actions }}</div>
        @endif
    </div>
</div>
