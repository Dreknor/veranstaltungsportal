{{--
    Generische Listenzeile im Raster der Veranstaltungszeile.
    Slots: lead (80 px, z. B. Datumsblock), Standard-Slot (Hauptspalte), meta, aside (128 px, rechtsbündig),
    action (184 px). Jeder Slot ist optional; fehlende Spalten geben ihren Platz frei.
    Unter lg: lead + Hauptspalte, darunter meta links / aside rechts, darunter action in voller Breite.
    Breite einer Spalte ändern: Klasse am Slot, z. B. <x-slot:action class="lg:w-auto">.
--}}
@props([
    'as' => 'li',
    'hover' => true,
    'lead' => null,
    'meta' => null,
    'aside' => null,
    'action' => null,
])

@php
    $has = fn ($part) => $part instanceof \Illuminate\View\ComponentSlot ? $part->isNotEmpty() : filled($part);
    $slotClass = fn ($part) => $part instanceof \Illuminate\View\ComponentSlot ? $part->attributes->get('class') : null;
    $hasLead = $has($lead);
    $hasMeta = $has($meta);
    $hasAside = $has($aside);
    $hasAction = $has($action);
@endphp

<{{ $as }} {{ $attributes->class([
    'grid grid-cols-[80px_minmax(0,1fr)_auto] items-center gap-4 p-4 text-ink transition-colors duration-150',
    'lg:flex lg:gap-6 lg:px-6 lg:py-5',
    'hover:bg-zone-hover' => $hover,
]) }}>
    @if ($hasLead)
        <div @class(['lg:flex-none', $slotClass($lead)])>{{ $lead }}</div>
    @endif

    <div @class(['flex min-w-0 flex-col gap-1 lg:flex-[1.7_1_0%]', 'col-span-2' => $hasLead, 'col-span-3' => ! $hasLead])>
        {{ $slot }}
    </div>

    @if ($hasMeta)
        <div @class(['flex min-w-0 flex-col gap-0.5 lg:flex-[1_1_0%]', 'col-span-2' => $hasAside, 'col-span-3' => ! $hasAside, $slotClass($meta)])>
            {{ $meta }}
        </div>
    @endif

    @if ($hasAside)
        <div @class(['flex flex-col items-end gap-0.5 text-right tabular-nums lg:w-32 lg:flex-none', 'col-span-3' => ! $hasMeta, $slotClass($aside)])>
            {{ $aside }}
        </div>
    @endif

    @if ($hasAction)
        <div @class(['col-span-3 flex flex-col gap-2 lg:w-[184px] lg:flex-none', $slotClass($action)])>
            {{ $action }}
        </div>
    @endif
</{{ $as }}>
