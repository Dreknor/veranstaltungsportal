{{--
    Reiterleiste. Einträge: <x-ui.tab>. Linie line unten; aktiver Reiter Gewicht 600 mit 2-px-Linie ink.
    Bei Platzmangel scrollt die Leiste waagrecht.

    variant="links" (Standard): Reiter als Links zu anderen Seiten (<nav>, aria-current="page").
    variant="toggle": Umschalter innerhalb einer Seite (<div role="group">); die Reiter sind Buttons
                      mit aria-pressed, der Zustand kommt aus Alpine (:aria-pressed) oder bestehendem Skript.
--}}
@props([
    'label' => 'Bereiche',
    'variant' => 'links', // links | toggle
])

@php $tag = $variant === 'toggle' ? 'div' : 'nav'; @endphp

<{{ $tag }} @if ($variant === 'toggle') role="group" @endif aria-label="{{ $label }}"
    {{ $attributes->class('relative overflow-x-auto border-b border-line') }}>
    <ul class="m-0 flex list-none gap-1 p-0">
        {{ $slot }}
    </ul>
    </{{ $tag }}>
