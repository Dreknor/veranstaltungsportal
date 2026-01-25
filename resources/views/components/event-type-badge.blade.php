@props(['event', 'size' => 'sm'])

@php
    $sizeClasses = [
        'xs' => 'px-2 py-0.5 text-xs',
        'sm' => 'px-2.5 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-1.5 text-base',
    ];

    $badgeClass = $sizeClasses[$size] ?? $sizeClasses['sm'];

    $typeConfig = [
        'online' => [
            'label' => 'Online',
            'icon' => 'globe',
            'bgClass' => 'bg-blue-100 dark:bg-blue-900',
            'textClass' => 'text-blue-800 dark:text-blue-200',
            'borderClass' => 'border-blue-300 dark:border-blue-700',
        ],
        'physical' => [
            'label' => 'Präsenz',
            'icon' => 'location',
            'bgClass' => 'bg-green-100 dark:bg-green-900',
            'textClass' => 'text-green-800 dark:text-green-200',
            'borderClass' => 'border-green-300 dark:border-green-700',
        ],
        'hybrid' => [
            'label' => 'Hybrid',
            'icon' => 'mix',
            'bgClass' => 'bg-purple-100 dark:bg-purple-900',
            'textClass' => 'text-purple-800 dark:text-purple-200',
            'borderClass' => 'border-purple-300 dark:border-purple-700',
        ],
    ];

    $config = $typeConfig[$event->event_type] ?? $typeConfig['physical'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full font-medium border {$badgeClass} {$config['bgClass']} {$config['textClass']} {$config['borderClass']}"]) }}>
    @if($config['icon'] === 'globe')
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
        </svg>
    @elseif($config['icon'] === 'location')
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
    @elseif($config['icon'] === 'mix')
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
        </svg>
    @endif
    {{ $config['label'] }}
</span>
