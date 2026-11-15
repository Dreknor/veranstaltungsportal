@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <rect x="5" y="10.5" width="14" height="10" rx="2"/>
    <path d="M8 10.5V8a4 4 0 018 0v2.5"/>
</svg>
