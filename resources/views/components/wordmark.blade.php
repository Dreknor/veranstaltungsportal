{{--
    Wortmarke (A6, Platzhalter bis zum endgültigen Logo): Petrol-Kreis mit Blattgrün-Blatt,
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
    <svg width="{{ $mark }}" height="{{ $mark }}" viewBox="0 0 36 36" aria-hidden="true" class="flex-none">
        <circle cx="18" cy="18" r="18" class="fill-primary" />
        <path d="M11 25c0-8.5 5-14 14-14 0 8.5-5 14-14 14z" class="fill-leaf" />
        <path d="M11 25l8-8" class="stroke-primary" stroke-width="1.5" stroke-linecap="round" fill="none" />
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
