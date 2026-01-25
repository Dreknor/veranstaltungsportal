@props(['badge', 'size' => 'md', 'earned' => true])

@php
$sizeClasses = [
    'sm' => 'w-12 h-12 text-2xl',
    'md' => 'w-20 h-20 text-4xl',
    'lg' => 'w-32 h-32 text-6xl',
    'xl' => 'w-48 h-48 text-8xl',
];

$borderSizes = [
    'sm' => 'border-2',
    'md' => 'border-2',
    'lg' => 'border-4',
    'xl' => 'border-4',
];

$classes = $sizeClasses[$size] ?? $sizeClasses['md'];
$borderClass = $borderSizes[$size] ?? $borderSizes['md'];
@endphp

<div class="relative inline-flex items-center justify-center {{ $classes }} rounded-full"
     style="background-color: {{ $badge->color }}20;">
    @if($badge->image_path)
        <img src="{{ asset($badge->image_path) }}"
             alt="{{ $badge->name }}"
             class="{{ $classes }} rounded-full object-cover {{ $borderClass }} {{ !$earned ? 'grayscale opacity-50' : '' }}"
             style="border-color: {{ $badge->color }}">
    @else
        <i class="{{ $badge->icon }} {{ !$earned ? 'text-gray-400' : '' }}"
           style="{{ $earned ? 'color: ' . $badge->color : '' }}"></i>
    @endif

    @if(!$earned)
        <div class="absolute inset-0 flex items-center justify-center">
            <i class="fas fa-lock text-gray-600 text-sm"></i>
        </div>
    @endif

    {{ $slot }}
</div>

