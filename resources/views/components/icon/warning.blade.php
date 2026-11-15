@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M12 3.75L2.75 19.75h18.5L12 3.75z"/>
    <path d="M12 10v4.5M12 17v.01"/>
</svg>
