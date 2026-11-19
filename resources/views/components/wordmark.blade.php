{{--
    Wortmarke (A6): Zeichen "Keimling" im ESDi-Stil (Navy-Kreis, zwei Blätter über einer Schale),
    daneben der Name aus config('app.name') und darunter "Hauptfach Mensch".
    Für helle Flächen (weiß, zone). size="md" im Kopf (36 px / 17 px), size="sm" im Fuß (32 px / 16 px).
    Verlinkt auf "/"; href=false gibt sie ohne Link aus.
--}}
@props([
    'size' => 'md', // md | sm
    'href' => '/',
])

@php
    $name = config('app.name', 'Bildungsportal');
    $tag = $href === false ? 'span' : 'a';
    $mark = $size === 'sm' ? 32 : 36;
@endphp

<{{ $tag }}
    @if ($tag === 'a') href="{{ url($href) }}" aria-label="{{ $name }} Hauptfach Mensch – Startseite" @endif
    {{ $attributes->class([
        'inline-flex items-center gap-3 text-ink no-underline hover:no-underline',
        'min-h-14' => $size !== 'sm',
        'min-h-11' => $size === 'sm',
    ]) }}>
    {{-- Zeichen "Keimling" im ESDi-Stil; Farben sind Logofarben (A6), keine UI-Tokens. --}}
    <svg width="{{ $mark }}" height="{{ $mark }}" viewBox="0 0 360 360" aria-hidden="true" class="flex-none">
        <circle cx="180" cy="180" r="180" fill="#22224A" />
        <path d="M75 75A105 105 0 0 1 180 180Z" fill="#95E01F" />
        <path d="M180 180A105 105 0 0 1 75 75Z" fill="#3BAE00" />
        <path d="M285 75A105 105 0 0 0 180 180Z" fill="#6FD600" />
        <path d="M180 180A105 105 0 0 0 285 75Z" fill="#007A1C" />
        <path d="M180 180H75A105 105 0 0 0 180 285Z" fill="#004D24" />
        <path d="M180 180V285A105 105 0 0 0 285 180Z" fill="#006400" />
    </svg>
    <span class="flex flex-col leading-[1.15]">
        <span @class([
            'font-semibold tracking-[-0.01em]',
            'text-[17px]' => $size !== 'sm',
            'text-base leading-[1.15]' => $size === 'sm',
        ])>{{ $name }}</span>
        <span class="text-caption leading-[1.15] text-muted">Hauptfach Mensch</span>
    </span>
    </{{ $tag }}>
