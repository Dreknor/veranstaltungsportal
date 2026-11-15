{{--
    Kasten: Kontur line, Radius 12. Nur mit Grund (zusammengehörige Liste, Zusammenfassung, Hinweis,
    Aufrufzeile). Kästen nicht ineinander schachteln.
--}}
@props([
    'as' => 'div',
    'tone' => 'white', // white | zone
    'padding' => 'md', // none | sm (16) | md (24) | lg (24–48, wächst mit der Breite)
])

<{{ $as }}
    {{ $attributes->class([
        'rounded-box border border-line text-ink',
        'bg-surface' => $tone !== 'zone',
        'bg-zone' => $tone === 'zone',
        'p-4' => $padding === 'sm',
        'p-6' => $padding === 'md',
        'p-[clamp(24px,4vw,48px)]' => $padding === 'lg',
    ]) }}>
    {{ $slot }}
    </{{ $as }}>
