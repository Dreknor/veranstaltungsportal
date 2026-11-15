@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M14 5h5v5M19 5l-8.5 8.5M17 13.5V18a1.5 1.5 0 01-1.5 1.5H6A1.5 1.5 0 014.5 18V8.5A1.5 1.5 0 016 7h4.5"/>
</svg>
