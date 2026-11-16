@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <rect x="3.5" y="3.5" width="6" height="6" rx="1" />
    <rect x="14.5" y="3.5" width="6" height="6" rx="1" />
    <rect x="3.5" y="14.5" width="6" height="6" rx="1" />
    <path d="M6.5 6.5h.01M17.5 6.5h.01M6.5 17.5h.01" />
    <path d="M14.5 14.5h2.5v2.5M20.5 14.5v.01M14.5 20.5h2.5M20.5 17.5v3h-1" />
</svg>
