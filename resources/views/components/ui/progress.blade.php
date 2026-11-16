{{--
    Fortschrittsbalken: Balken primary auf Fläche line, Radius rund. Als <span class="block">, damit er
    auch in Zeilen- und Inline-Kontexten (Kennzahl, Tabellenzelle) gültig ist.

    Barrierefrei als role="progressbar" mit aria-valuenow/-min/-max. Den Namen gibt label (aria-label)
    oder ein durchgereichtes aria-labelledby. decorative: nur Schmuck, wenn die Zahl daneben als Text
    steht (Balkendiagramme in Tabellen) – dann aria-hidden und keine Rolle.
--}}
@props([
    'value' => 0,
    'max' => 100,
    'label' => null, // Name für Screenreader (aria-label)
    'valueText' => null, // optional aria-valuetext, z. B. "12 von 40 eingecheckt"
    'size' => 'md', // md (8 px) | lg (12 px)
    'decorative' => false,
])

@php
    $max = (float) $max > 0 ? (float) $max : 100.0;
    $current = max(0.0, min((float) $value, $max));
    $percent = round($current / $max * 100, 1);
    $format = fn(float $n): string => rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
@endphp

<span
    @if ($decorative) aria-hidden="true"
    @else
        role="progressbar" aria-valuemin="0" aria-valuemax="{{ $format($max) }}" aria-valuenow="{{ $format($current) }}"
        @if ($label) aria-label="{{ $label }}" @endif
        @if ($valueText) aria-valuetext="{{ $valueText }}" @endif @endif
    {{ $attributes->class([
        'block w-full overflow-hidden rounded-full bg-line',
        'h-2' => $size !== 'lg',
        'h-3' => $size === 'lg',
    ]) }}>
    <span class="block h-full rounded-full bg-primary" style="width: {{ $percent }}%"></span>
</span>
