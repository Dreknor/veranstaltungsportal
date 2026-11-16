{{--
    Auswahlkarte mit Radio (Zahlungsmethode, Ticketart mit Einzelauswahl). type="checkbox" für Mehrfachwahl.
    class landet an der Karte (<label>), alle übrigen Attribute am <input>.
    Slot: zusätzliche Zeilen unter der Beschreibung. Slot aside: Inhalt rechts (z. B. Preis).
    compact: ohne Mindesthöhe, Inhalt senkrecht mittig (einzeilige Optionen). input-class: Klassen am <input>.
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'type' => 'radio',
    'checked' => false,
    'title' => null,
    'description' => null,
    'required' => false,
    'old' => true,
    'aside' => null,
    'compact' => false, // einzeilige Karte ohne Mindesthöhe 88 px, Inhalt mittig (z. B. Empfängerliste)
    'inputClass' => '',
])

@php
    $id ??= \App\Support\Ui::optionId($name, $value);
    $isChecked = $old ? \App\Support\Ui::checked($name, $value, (bool) $checked) : (bool) $checked;
@endphp

<label for="{{ $id }}" {{ $attributes->only('class')->class(['ui-choice', 'ui-choice--compact' => $compact]) }}>
    <input type="{{ $type }}" @if ($name) name="{{ $name }}" @endif
        id="{{ $id }}" value="{{ $value }}" @checked($isChecked) @required($required)
        {{ $attributes->except('class')->class([$type === 'checkbox' ? 'ui-checkbox' : 'ui-radio', 'mt-0.5' => !$compact, $inputClass]) }}>
    <span class="flex min-w-0 flex-1 flex-col gap-0.5">
        @if ($title)
            <span class="text-base font-semibold">{{ $title }}</span>
        @endif
        @if ($description)
            <span class="text-small text-ink-soft">{{ $description }}</span>
        @endif
        {{ $slot }}
    </span>
    @if ($aside)
        <span class="shrink-0 text-right text-base font-semibold tabular-nums">{{ $aside }}</span>
    @endif
</label>
