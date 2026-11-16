{{--
    Personenbild: Foto (rund, Kontur line) oder Initialen im zone-Kreis.

    user:     App\Models\User – liefert Foto (nur wenn ein eigenes hochgeladen ist), Initialen und Namen.
              Ohne eigenes Foto erscheinen die Initialen (keine Gravatar-Anfrage an Dritte).
    src:      Foto-URL (überschreibt user); name/initials für den Initialen-Kreis ohne Foto.
    size:     sm (40 px, Tabellen) | md (56 px, Listenzeilen) | lg (96 px, Profilkopf)
    label:    Name für Screenreader. Ohne label ist das Bild Schmuck (alt="" bzw. aria-hidden),
              weil der Name daneben als Text steht – der Normalfall.
--}}
@props([
    'user' => null,
    'src' => null,
    'name' => null,
    'initials' => null,
    'size' => 'md',
    'label' => null,
])

@php
    if ($user) {
        $src ??= $user->profile_photo ? $user->profilePhotoUrl() : null;
        $name ??= $user->fullName();
        $initials ??= $user->initials();
    }

    if ($initials === null && $name) {
        $initials = \Illuminate\Support\Str::of($name)
            ->squish()
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
            ->implode('');
    }

    $sizeClasses = match ($size) {
        'sm' => 'h-10 w-10 text-small font-semibold',
        'lg' => 'h-24 w-24 text-h2',
        default => 'h-14 w-14 text-h3',
    };
    $base = 'flex-none rounded-full border border-line';
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $label ?? '' }}" loading="lazy"
        {{ $attributes->class([$base, $sizeClasses, 'bg-zone object-cover']) }}>
@else
    <span
        @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif
        {{ $attributes->class([$base, $sizeClasses, 'inline-flex items-center justify-center bg-zone text-ink-soft select-none']) }}>{{ $initials }}</span>
@endif
