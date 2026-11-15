{{--
    Zusammenfassungskasten (Buchung, Bestellung): Fläche zone, Kontur line, Radius 12, Polster 24,
    Abstand der Teile 20 px; ab 1024 px mitlaufend (sticky, 24 px von oben).
    Inhalt der Reihe nach: <x-ui.summary-lines> mit <x-ui.summary-line>, <x-ui.summary-total>,
    Zustimmung (<x-ui.checkbox>), Primärbutton (full), kurzer Hinweis.
    Slot event: Block mit Veranstaltung (Titel, Datum, Ort) direkt unter der Überschrift.
--}}
@props([
    'title' => 'Zusammenfassung',
    'id' => 'summary-title',
    'sticky' => true,
    'as' => 'aside',
    'event' => null,
])

<{{ $as }} aria-labelledby="{{ $id }}" {{ $attributes->class([
    'flex min-w-0 flex-col gap-5 rounded-box border border-line bg-zone p-6 text-ink',
    'lg:sticky lg:top-6' => $sticky,
]) }}>
    <h2 id="{{ $id }}" class="m-0 text-h3">{{ $title }}</h2>
    @if ($event)
        <div class="flex flex-col gap-1 text-small text-ink-soft tabular-nums [&>:first-child]:text-base [&>:first-child]:leading-[1.35] [&>:first-child]:font-semibold [&>:first-child]:text-ink">
            {{ $event }}
        </div>
    @endif
    {{ $slot }}
</{{ $as }}>
