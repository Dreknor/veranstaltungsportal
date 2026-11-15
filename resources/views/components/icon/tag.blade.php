@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path
        d="M3.5 12.1V4.5a1 1 0 011-1h7.6a1 1 0 01.7.3l8 8a1 1 0 010 1.4l-7.6 7.6a1 1 0 01-1.4 0l-8-8a1 1 0 01-.3-.7z" />
    <circle cx="8" cy="8" r="1.5" />
</svg>
