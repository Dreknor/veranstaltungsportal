{{--
    Gruppe in der Seitenleiste: Label in caption/muted, darunter die Einträge (<x-ui.sidebar-item>
    in <li>, z. B. über <x-layouts.sidebar-link>). Ohne label nur die Liste.
--}}
@props(['label' => null])

<div {{ $attributes->class('flex flex-col') }}>
    @if ($label)
        <p class="m-0 px-3 pt-6 pb-2 text-caption text-muted">{{ $label }}</p>
    @endif
    <ul class="m-0 flex list-none flex-col gap-0.5 p-0">
        {{ $slot }}
    </ul>
</div>
