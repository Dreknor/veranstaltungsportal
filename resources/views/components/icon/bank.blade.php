@props(['class' => 'w-5 h-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M3.5 9L12 4l8.5 5"/>
    <path d="M4.5 9h15M6 9v8.5M10 9v8.5M14 9v8.5M18 9v8.5M3.5 20.5h17"/>
</svg>
