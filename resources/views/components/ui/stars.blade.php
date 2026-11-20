{{--
    Sternreihe 1–5 (gefüllt ink, leer line-strong). Für Screenreader eine Grafik mit „4,5 von 5 Sternen“.
    decorative: steht die Zahl daneben schon als Text, wird die Reihe ausgeblendet.
--}}
@props([
    'value' => 0, // 0–5, auch Kommazahl (gerundet auf halbe Sterne nach unten, ganze Sterne gefüllt)
    'size' => 'md', // sm (16 px) | md (20 px)
    'decorative' => false,
])

@php
    $value = max(0.0, min(5.0, (float) $value));
    $filled = (int) floor($value + 0.25); // 4,8 → 5 Sterne, 4,6 → 4
    $icon = $size === 'sm' ? 'h-4 w-4' : 'h-5 w-5';
    $label = number_format($value, $value == floor($value) ? 0 : 1, ',', '.') . ' von 5 Sternen';
@endphp

<span @if ($decorative) aria-hidden="true" @else role="img" aria-label="{{ $label }}" @endif
    {{ $attributes->class('inline-flex flex-none items-center gap-0.5 text-ink') }}>
    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= $filled)
            <x-icon.star-filled :class="$icon" />
        @else
            <x-icon.star :class="$icon . ' text-line-strong'" />
        @endif
    @endfor
</span>
