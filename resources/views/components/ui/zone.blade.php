{{--
    Vollbreite ruhige Fläche (zone) mit Linie unten – Einstieg einer Seite, Fuß.
    Der Inhalt wird auf die Seitenbreite (1200 px) mit Seitenrand gesetzt; contained=false lässt ihn frei.
--}}
@props([
    'as' => 'section',
    'contained' => true,
    'border' => 'bottom', // bottom | top | both | none
])

<{{ $as }}
    {{ $attributes->class([
        'bg-zone',
        'border-b border-line' => in_array($border, ['bottom', 'both'], true),
        'border-t border-line' => in_array($border, ['top', 'both'], true),
    ]) }}>
    @if ($contained)
        <div class="mx-auto max-w-page px-page">{{ $slot }}</div>
    @else
        {{ $slot }}
    @endif
    </{{ $as }}>
