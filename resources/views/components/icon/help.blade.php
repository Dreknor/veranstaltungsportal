@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <circle cx="12" cy="12" r="9" />
    <path d="M9.5 9.5a2.5 2.5 0 114.2 1.8c-.8.7-1.7 1.1-1.7 2.2v.3" />
    <circle cx="12" cy="17" r="1" fill="currentColor" stroke="none" />
</svg>
