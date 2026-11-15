{{--
    Reiter als Links (Seitenwechsel, kein JavaScript). Einträge: <x-ui.tab>.
    Linie line unten; aktiver Reiter Gewicht 600 mit 2-px-Linie ink und aria-current="page".
    Bei Platzmangel scrollt die Leiste waagrecht.
--}}
@props([
    'label' => 'Bereiche',
])

<nav aria-label="{{ $label }}" {{ $attributes->class('relative overflow-x-auto border-b border-line') }}>
    <ul class="m-0 flex list-none gap-1 p-0">
        {{ $slot }}
    </ul>
</nav>
