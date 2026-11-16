{{--
    Eintrag der Seitenleiste im angemeldeten Bereich (B4): 44 px hoch, Text ui, Icon 20 px.
    Aktiv: Fläche zone, Gewicht 600, aria-current="page" – kein farbiger Balken.

    icon: Name eines Icons unter components/icon/ ("calendar") oder ein alter Font-Awesome-Name
          (z. B. "fas-calendar-days"), der über App\Support\Ui::icon() abgebildet wird.
          Unbekannte Namen fallen auf das Listen-Icon zurück.
    count: Zahl rechts (z. B. ungelesene Benachrichtigungen); count-label ergänzt sie für Screenreader.
    href: Link; ohne href ein <button> (z. B. für Aufklapp-Einträge).
--}}
@props([
    'href' => null,
    'active' => false,
    'icon' => null,
    'count' => null,
    'countLabel' => 'neu',
])

@php
    // Eigene Icon-Namen oder alte Font-Awesome-Namen → Linien-Icon (Zuordnung in App\Support\Ui::icon)
    $iconComponent = $icon ? \App\Support\Ui::icon($icon, 'list') : null;

    $classes = \Illuminate\Support\Arr::toCssClasses([
        'flex min-h-11 w-full items-center gap-3 rounded-control px-3 py-2 text-left text-ui text-ink no-underline transition-colors duration-150',
        'bg-zone font-semibold' => $active,
        'font-medium hover:bg-zone-hover' => !$active,
    ]);
@endphp

@if ($href !== null)
    <a href="{{ $href }}" @if ($active) aria-current="page" @endif
        {{ $attributes->class($classes) }}>
        @if ($iconComponent)
            <x-dynamic-component :component="$iconComponent" aria-hidden="true" @class([
                'h-5 w-5 flex-none',
                'text-ink' => $active,
                'text-muted' => !$active,
            ]) />
        @endif
        <span class="min-w-0 flex-1">{{ $slot }}</span>
        @if ($count)
            <span class="flex-none text-small font-medium text-ink-soft tabular-nums"><span
                    class="sr-only">(</span>{{ $count }}<span class="sr-only"> {{ $countLabel }})</span></span>
        @endif
    </a>
@else
    <button type="button" {{ $attributes->class(['cursor-pointer border-0', 'bg-transparent' => !$active, $classes]) }}>
        @if ($iconComponent)
            <x-dynamic-component :component="$iconComponent" aria-hidden="true" @class([
                'h-5 w-5 flex-none',
                'text-ink' => $active,
                'text-muted' => !$active,
            ]) />
        @endif
        <span class="min-w-0 flex-1">{{ $slot }}</span>
        @if ($count)
            <span class="flex-none text-small font-medium text-ink-soft tabular-nums"><span
                    class="sr-only">(</span>{{ $count }}<span class="sr-only"> {{ $countLabel }})</span></span>
        @endif
    </button>
@endif
