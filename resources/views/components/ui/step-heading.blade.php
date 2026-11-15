{{--
    Abschnittsmarke im Formular: nummerierter Kreis (28 px, primary) + Überschrift h2-form.
    Die Nummer steht für Screenreader versteckt im Titel ("1. Tickets wählen").
    Slot aside: Inhalt rechts (z. B. "* Pflichtfeld").
--}}
@props(['number', 'as' => 'h2', 'id' => null, 'aside' => null])

<div {{ $attributes->class('flex flex-wrap items-center justify-between gap-x-6 gap-y-2') }}>
    <div class="flex items-center gap-3">
        <span aria-hidden="true"
            class="inline-flex h-7 w-7 flex-none items-center justify-center rounded-full bg-primary text-small font-semibold text-on-primary tabular-nums">{{ $number }}</span>
        <{{ $as }} @if ($id) id="{{ $id }}" @endif
            class="m-0 text-h2-form text-ink"><span class="sr-only">{{ $number }}. </span>{{ $slot }}
            </{{ $as }}>
    </div>
    @if ($aside)
        <div class="text-small text-muted">{{ $aside }}</div>
    @endif
</div>
