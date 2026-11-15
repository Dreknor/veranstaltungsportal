{{--
    Abschnitt mit Überschrift (h2, 24 px) und optionalem Link rechts ("Alle Veranstaltungen →").
    Slot actions statt Link für Buttons. level="h3" für Unterabschnitte.
--}}
@props([
    'title',
    'id' => null,
    'linkHref' => null,
    'linkLabel' => null,
    'level' => 'h2', // h2 | h3
    'description' => null,
    'actions' => null,
])

@php
    $id ??= 'abschnitt-'.\Illuminate\Support\Str::slug($title);
@endphp

<section aria-labelledby="{{ $id }}" {{ $attributes }}>
    <div class="mb-6 flex flex-wrap items-baseline justify-between gap-x-6 gap-y-2">
        <div class="flex min-w-0 flex-col gap-1">
            <{{ $level }} id="{{ $id }}" @class(['m-0 text-ink', 'text-h2' => $level === 'h2', 'text-h3' => $level !== 'h2'])>{{ $title }}</{{ $level }}>
            @if ($description)
                <p class="m-0 text-small text-muted">{{ $description }}</p>
            @endif
        </div>
        @if ($actions)
            <div class="flex flex-wrap items-center gap-3">{{ $actions }}</div>
        @elseif ($linkHref)
            <x-ui.button variant="link" :href="$linkHref" arrow>{{ $linkLabel }}</x-ui.button>
        @endif
    </div>
    {{ $slot }}
</section>
