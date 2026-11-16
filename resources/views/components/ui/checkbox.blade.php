{{--
    Checkbox mit klickbarem Label (Zeile mindestens 44 px). Beschriftung über label oder den Slot
    (Slot erlaubt Links). class landet am äußeren <div>, alle übrigen Attribute (data-*, x-model,
    @change, form …) am <input>. Klassen fürs <input> selbst: input-class.
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => '1',
    'checked' => false,
    'label' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'old' => true,
    'bag' => 'default',
    'size' => 'ui', // ui (15 px) | small (14 px, z. B. Zustimmung in der Zusammenfassung)
    'inputClass' => '', // zusätzliche Klassen am <input> (z. B. JS-Selektor)
])

@php
    $id ??= \App\Support\Ui::optionId($name, $value, str_ends_with((string) $name, '[]'));
    $error ??= \App\Support\Ui::error($errors ?? null, $name, $bag);
    $isChecked = $old ? \App\Support\Ui::checked($name, $value, (bool) $checked) : (bool) $checked;
    $describedBy = trim(($hint ? $id . '-hint ' : '') . ($error ? $id . '-error' : ''));
@endphp

<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" @class([
        'flex min-h-11 cursor-pointer items-start gap-3 py-[11px] text-ink has-disabled:cursor-not-allowed has-disabled:text-muted',
        'text-ui' => $size !== 'small',
        'text-small leading-[1.45]' => $size === 'small',
    ])>
        <input type="checkbox" @if ($name) name="{{ $name }}" @endif
            id="{{ $id }}" value="{{ $value }}" @checked($isChecked) @required($required)
            @if ($error) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->except('class')->class(['ui-checkbox', $inputClass]) }}>
        <span class="min-w-0">
            {{ $label ?? $slot }}
            @if ($required)
                <span aria-hidden="true">*</span>
            @endif
            @if ($hint)
                <span id="{{ $id }}-hint" class="mt-0.5 block text-small text-muted">{{ $hint }}</span>
            @endif
        </span>
    </label>
    <x-ui.field-error :message="$error" :id="$id . '-error'" />
</div>
