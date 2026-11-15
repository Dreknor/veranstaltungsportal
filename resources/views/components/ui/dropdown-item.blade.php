{{--
    Eintrag im Aufklappmenü. Mit href ein Link, sonst ein <button> (type="submit" mit form="…" für
    Formulare außerhalb des Menüs). danger: Löschen/Stornieren in Fehlerfarbe. Icon über Slot icon.
--}}
@props([
    'href' => null,
    'type' => 'button',
    'danger' => false,
    'icon' => null,
])

@php
    $classes = \Illuminate\Support\Arr::toCssClasses([
        'focus-inset flex min-h-11 w-full cursor-pointer items-center gap-3 border-0 bg-transparent px-4 text-left text-ui font-medium no-underline transition-colors duration-150 hover:bg-zone',
        'text-ink' => ! $danger,
        'text-danger' => $danger,
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)<span class="shrink-0 [&>svg]:h-4 [&>svg]:w-4">{{ $icon }}</span>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icon)<span class="shrink-0 [&>svg]:h-4 [&>svg]:w-4">{{ $icon }}</span>@endif
        {{ $slot }}
    </button>
@endif
