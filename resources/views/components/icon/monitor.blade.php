@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <rect x="3" y="4.5" width="18" height="12" rx="2"/>
    <path d="M8.5 20h7M12 16.5V20"/>
</svg>
