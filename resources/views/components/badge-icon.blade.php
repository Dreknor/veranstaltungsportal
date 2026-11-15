{{-- Abzeichen-Symbol. Die Farbe des Abzeichens ist Inhalt und bleibt (nur innerhalb des Abzeichens, A2). --}}
@props(['badge', 'size' => 'md', 'earned' => true])

@php
    $sizeClasses = [
        'sm' => 'h-12 w-12',
        'md' => 'h-20 w-20',
        'lg' => 'h-32 w-32',
        'xl' => 'h-48 w-48',
    ];
    $glyphSizes = [
        'sm' => 'h-6 w-6',
        'md' => 'h-10 w-10',
        'lg' => 'h-16 w-16',
        'xl' => 'h-24 w-24',
    ];
    $borderSizes = [
        'sm' => 'border-2',
        'md' => 'border-2',
        'lg' => 'border-4',
        'xl' => 'border-4',
    ];

    $classes = $sizeClasses[$size] ?? $sizeClasses['md'];
    $glyph = $glyphSizes[$size] ?? $glyphSizes['md'];
    $borderClass = $borderSizes[$size] ?? $borderSizes['md'];
@endphp

<div class="relative inline-flex items-center justify-center {{ $classes }} rounded-full {{ $earned ? '' : 'bg-zone' }}"
    @if ($earned) style="background-color: {{ $badge->color }}20;" @endif>
    @if ($badge->image_path)
        <img src="{{ asset($badge->image_path) }}" alt="{{ $badge->name }}"
            class="{{ $classes }} rounded-full object-cover {{ $borderClass }} {{ !$earned ? 'grayscale opacity-50' : '' }}"
            style="border-color: {{ $badge->color }}">
    @else
        <x-icon.medal class="{{ $glyph }} {{ !$earned ? 'text-disabled' : '' }}"
            :style="$earned ? 'color: ' . $badge->color : null" />
    @endif

    @if (!$earned)
        <div class="absolute inset-0 flex items-center justify-center">
            <x-icon.lock class="h-5 w-5 text-muted" />
        </div>
    @endif

    {{ $slot }}
</div>
