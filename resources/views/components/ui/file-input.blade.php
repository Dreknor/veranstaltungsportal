{{--
    Dateiauswahl mit deutscher Beschriftung statt des Browser-Standards („Choose File / No file chosen").
    Das echte <input type="file"> bleibt bedienbar (Tab, Leertaste), ist aber visuell versteckt; sichtbar
    ist ein Sekundärbutton („Datei auswählen") und daneben der gewählte Dateiname. Ein Label setzt der
    Aufrufer selbst (<x-ui.label for="…">). class landet am äußeren Element, alle übrigen Attribute
    (accept, required, aria-describedby, onchange …) am <input>.
--}}
@props([
    'name' => null,
    'id' => null,
    'button' => 'Datei auswählen',
    'empty' => 'Keine Datei ausgewählt',
    'invalid' => null, // null = automatisch aus $errors
    'bag' => 'default',
])

@php
    $id ??= \App\Support\Ui::id($name);
    $invalid ??= \App\Support\Ui::error($errors ?? null, $name, $bag) !== null;
@endphp

<div x-data="{ files: '' }"
    {{ $attributes->only('class')->class('flex min-w-0 flex-wrap items-center gap-x-3 gap-y-2') }}>
    <input type="file" @if ($name) name="{{ $name }}" @endif id="{{ $id }}"
        @if ($invalid) aria-invalid="true" @endif
        x-on:change="files = Array.from($event.target.files).map(f => f.name).join(', ')"
        {{ $attributes->except('class')->class('peer sr-only') }}>
    <label for="{{ $id }}"
        class="inline-flex min-h-11 cursor-pointer items-center gap-2 rounded-control border border-line-strong bg-surface px-4 text-ui font-medium text-ink transition-colors duration-150 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-ink peer-disabled:cursor-not-allowed peer-disabled:border-line peer-disabled:bg-zone peer-disabled:text-disabled peer-aria-invalid:border-danger-field hover:border-ink hover:bg-zone">
        <x-icon.upload class="h-4 w-4 flex-none" />
        {{ $button }}
    </label>
    <span class="min-w-0 text-small break-all text-muted" aria-live="polite"
        x-text="files || @js($empty)">{{ $empty }}</span>
</div>
