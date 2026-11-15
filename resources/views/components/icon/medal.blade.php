@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M8 3.5l2.5 6M16 3.5l-2.5 6"/>
    <circle cx="12" cy="15" r="5.5"/>
    <path d="M12 12.5l.8 1.6 1.7.2-1.25 1.2.3 1.7L12 16.4l-1.55.8.3-1.7-1.25-1.2 1.7-.2z"/>
</svg>
