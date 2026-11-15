@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <rect x="3.5" y="5" width="17" height="15.5" rx="2"/>
    <path d="M3.5 10h17M8 3v4M16 3v4M9 15l2 2 4-4"/>
</svg>
