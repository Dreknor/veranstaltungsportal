{{--
    Zelle der Suchzeile mit kleinem Label über dem Wert. Mit :options ein Auswahlfeld,
    sonst ein eigenes Bedienelement im Slot (Klasse "ui-search-control" setzen).
    wide: 200 px statt 176 px (z. B. "Zeitraum").
--}}
@props([
    'label',
    'name' => null,
    'id' => null,
    'options' => null, // ['wert' => 'Text', …]
    'selected' => null, // Standard: request($name)
    'wide' => false,
])

@php
    $id ??= \App\Support\Ui::id($name) ?? 'cell-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
    $selected ??= $name ? request()->input(\App\Support\Ui::key($name)) : null;
@endphp

<div {{ $attributes->only('class')->class(['ui-search-cell', 'ui-search-cell--md' => $wide]) }}>
    <label for="{{ $id }}" class="ui-search-label">{{ $label }}</label>
    @if ($options !== null)
        <select id="{{ $id }}" @if ($name) name="{{ $name }}" @endif
            {{ $attributes->except('class')->class('ui-search-control') }}>
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) $selected === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
        <x-icon.chevron-down class="pointer-events-none absolute top-8 right-4 h-4 w-4 text-ink" />
    @else
        {{ $slot }}
    @endif
</div>
