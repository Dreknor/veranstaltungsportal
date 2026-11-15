{{--
    Format einer Veranstaltung als schlichter Text ("Präsenz", "Online", "Hybrid", "Extern") –
    keine farbige Pille. size steuert nur die Schriftgröße.
--}}
@props(['event', 'size' => 'sm'])

@php
    $sizeClass = match ($size) {
        'xs' => 'text-caption',
        'md' => 'text-ui',
        'lg' => 'text-base',
        default => 'text-small',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center font-medium text-ink-soft', $sizeClass]) }}>
    {{ \App\Support\Ui::format($event) }}
</span>
