{{-- Listencontainer: Kontur line, Radius 12, Zeilen durch 1 px line getrennt. Kinder: <x-ui.row> / <x-ui.event-row>. --}}
@props(['as' => 'ul'])

<{{ $as }} {{ $attributes->class('m-0 list-none divide-y divide-line overflow-hidden rounded-box border border-line bg-surface p-0') }}>
    {{ $slot }}
</{{ $as }}>
