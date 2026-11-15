@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M4 7h16v4a2 2 0 000 4v4H4v-4a2 2 0 000-4V7z"/>
    <path d="M14 7v12" stroke-dasharray="2 3"/>
</svg>
