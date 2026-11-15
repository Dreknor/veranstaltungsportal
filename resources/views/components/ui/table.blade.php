{{--
    Tabelle (Backend). Container mit Kontur line, Radius 12, waagrecht scrollbar.
    Im Slot steht normales Tabellen-Markup (<thead>, <tbody>, <tr>, <th>, <td>) OHNE eigene Klassen –
    die Gestaltung kommt aus der Klasse "ui-table" (app.css). Hilfsklassen an Zellen:
      num     – Zahl/Betrag/Datum: rechtsbündig, tabellarische Ziffern
      actions – Aktionen: rechtsbündig, kein Umbruch (Textlinks oder <x-ui.button size="sm">)
    label: Name der Tabelle für Screenreader (macht den Scrollbereich per Tastatur erreichbar).
--}}
@props([
    'label' => null,
    'caption' => null, // sichtbare Beschriftung über der Tabelle (small, muted)
    'dense' => false, // 44 px statt 56 px Zeilenhöhe (lange Listen)
    'tableClass' => '',
])

<div
    @if ($label) role="region" aria-label="{{ $label }}" tabindex="0" @endif
    {{ $attributes->class('w-full overflow-x-auto rounded-box border border-line bg-surface') }}
>
    <table @class(['ui-table', 'ui-table--dense' => $dense, $tableClass])>
        @if ($caption)
            <caption>{{ $caption }}</caption>
        @endif
        {{ $slot }}
    </table>
</div>
