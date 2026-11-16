{{--
    Radio mit klickbarem Label (Zeile mindestens 44 px). Mehrere Radios gleichen Namens gehören in ein
    <fieldset> mit <x-ui.label as="legend">. class landet am äußeren <div>, alles Übrige (data-*, x-model …) am <input>;
    Klassen fürs <input> selbst: input-class.
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'checked' => false,
    'label' => null,
    'hint' => null,
    'required' => false,
    'old' => true,
    'inputClass' => '', // zusätzliche Klassen am <input> (z. B. JS-Selektor)
])

@php
    $id ??= \App\Support\Ui::optionId($name, $value);
    $isChecked = $old ? \App\Support\Ui::checked($name, $value, (bool) $checked) : (bool) $checked;
@endphp

<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}"
        class="flex min-h-11 cursor-pointer items-start gap-3 py-[11px] text-ui text-ink has-disabled:cursor-not-allowed has-disabled:text-muted">
        <input type="radio" @if ($name) name="{{ $name }}" @endif id="{{ $id }}"
            value="{{ $value }}" @checked($isChecked) @required($required)
            @if ($hint) aria-describedby="{{ $id }}-hint" @endif
            {{ $attributes->except('class')->class(['ui-radio', $inputClass]) }}>
        <span class="min-w-0">
            {{ $label ?? $slot }}
            @if ($hint)
                <span id="{{ $id }}-hint" class="mt-0.5 block text-small text-muted">{{ $hint }}</span>
            @endif
        </span>
    </label>
</div>
