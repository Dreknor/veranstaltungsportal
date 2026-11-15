{{--
    Beschriftung/Wert-Paare für Detailseiten. Einträge: <x-ui.dl-item label="…">Wert</x-ui.dl-item>.
    layout="rows": ab 768 px Label links (200 px) und Wert rechts, Zeilen durch Linien getrennt.
    layout="stack": Label über Wert, im Raster mit bis zu drei Spalten (kompakte Übersichten).
--}}
@props([
    'layout' => 'rows', // rows | stack
])

<dl {{ $attributes->class([
    'm-0 text-ink',
    'divide-y divide-line border-y border-line' => $layout === 'rows',
    'grid gap-x-8 gap-y-5 sm:grid-cols-2 lg:grid-cols-3' => $layout === 'stack',
]) }} data-layout="{{ $layout }}">
    {{ $slot }}
</dl>
