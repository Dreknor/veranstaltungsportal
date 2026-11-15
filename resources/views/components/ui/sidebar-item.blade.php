{{--
    Eintrag der Seitenleiste im angemeldeten Bereich (B4): 44 px hoch, Text ui, Icon 20 px.
    Aktiv: Fläche zone, Gewicht 600, aria-current="page" – kein farbiger Balken.

    icon: Name eines Icons unter components/icon/ ("calendar") oder ein alter Font-Awesome-Name
          ("fas-calendar-days", "fa-solid fa-ticket"), der über die Zuordnung unten abgebildet wird.
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
    // Font-Awesome-Namen (Seitenleiste, Konfiguration, Datenbank) → eigene Linien-Icons
    $faMap = [
        'house' => 'home',
        'home' => 'home',
        'gauge' => 'chart',
        'gauge-high' => 'chart',
        'chart-line' => 'chart',
        'chart-bar' => 'chart',
        'chart-pie' => 'chart',
        'calendar' => 'calendar',
        'calendar-days' => 'calendar',
        'calendar-alt' => 'calendar',
        'calendar-check' => 'calendar-check',
        'ticket' => 'ticket',
        'ticket-alt' => 'ticket',
        'heart' => 'heart',
        'medal' => 'medal',
        'award' => 'medal',
        'trophy' => 'medal',
        'certificate' => 'sparkles',
        'star' => 'star',
        'receipt' => 'receipt',
        'file-invoice' => 'document',
        'file-invoice-dollar' => 'document',
        'file-contract' => 'scale',
        'file-lines' => 'document',
        'file-alt' => 'document',
        'file-export' => 'download',
        'cloud-arrow-down' => 'download',
        'download' => 'download',
        'building' => 'building-office',
        'building-columns' => 'bank',
        'university' => 'bank',
        'paypal' => 'credit-card',
        'credit-card' => 'credit-card',
        'coins' => 'currency',
        'euro-sign' => 'currency',
        'users' => 'users',
        'users-gear' => 'users',
        'user-group' => 'user-group',
        'user' => 'user',
        'shield-halved' => 'shield-check',
        'shield-alt' => 'shield-check',
        'tags' => 'tag',
        'tag' => 'tag',
        'clipboard-list' => 'list',
        'list' => 'list',
        'envelope' => 'mail',
        'bell' => 'bell',
        'circle-question' => 'help',
        'question-circle' => 'help',
        'gear' => 'settings',
        'cog' => 'settings',
    ];

    $iconComponent = null;
    if ($icon) {
        // "fas-calendar-days", "fab-paypal", "fa-solid fa-ticket", "fas fa-ticket" → "calendar-days" usw.
        $key = preg_replace('/^(fa[srbld]?|fa-(solid|regular|brands))[-\s]+/', '', trim((string) $icon));
        $key = preg_replace('/^fa-/', '', $key);
        $name = $faMap[$key] ?? $key;
        $iconComponent = view()->exists('components.icon.' . $name) ? 'icon.' . $name : 'icon.list';
    }

    $classes = \Illuminate\Support\Arr::toCssClasses([
        'flex min-h-11 w-full items-center gap-3 rounded-control px-3 py-2 text-left text-ui text-ink no-underline transition-colors duration-150',
        // ältere Icons mit Strich 2 auf die Linienstärke 1,6 bringen
        '[&_svg_*]:[stroke-width:1.6]',
        'bg-zone font-semibold' => $active,
        'font-medium hover:bg-zone-hover' => !$active,
    ]);
@endphp

@if ($href !== null)
    <a href="{{ $href }}" @if ($active) aria-current="page" @endif {{ $attributes->class($classes) }}>
        @if ($iconComponent)
            <x-dynamic-component :component="$iconComponent" aria-hidden="true" @class(['h-5 w-5 flex-none', 'text-ink' => $active, 'text-muted' => !$active]) />
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
            <x-dynamic-component :component="$iconComponent" aria-hidden="true" @class(['h-5 w-5 flex-none', 'text-ink' => $active, 'text-muted' => !$active]) />
        @endif
        <span class="min-w-0 flex-1">{{ $slot }}</span>
        @if ($count)
            <span class="flex-none text-small font-medium text-ink-soft tabular-nums"><span
                    class="sr-only">(</span>{{ $count }}<span class="sr-only"> {{ $countLabel }})</span></span>
        @endif
    </button>
@endif
