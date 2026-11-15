{{--
    Leerer Zustand: ein Satz (Slot), optional Titel, genau eine Aktion (action-href + action-label
    oder Slot action). bordered=false ohne Rahmen, z. B. innerhalb einer Tabelle.
--}}
@props([
    'title' => null,
    'actionHref' => null,
    'actionLabel' => null,
    'actionVariant' => 'secondary',
    'bordered' => true,
    'action' => null,
])

<div
    {{ $attributes->class([
        'flex flex-col items-start gap-3 px-6 py-8 text-ink',
        'rounded-box border border-line bg-surface' => $bordered,
    ]) }}>
    @if ($title)
        <p class="m-0 text-h3">{{ $title }}</p>
    @endif
    <p class="m-0 max-w-prose text-base text-muted">{{ $slot }}</p>
    @if ($action)
        {{ $action }}
    @elseif ($actionHref)
        <x-ui.button :variant="$actionVariant" :href="$actionHref">{{ $actionLabel }}</x-ui.button>
    @endif
</div>
