@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <circle cx="5" cy="12" r="2" fill="currentColor" stroke="none"/>
    <circle cx="12" cy="12" r="2" fill="currentColor" stroke="none"/>
    <circle cx="19" cy="12" r="2" fill="currentColor" stroke="none"/>
</svg>
