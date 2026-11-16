{{--
    Formularfeld: Label, Eingabe, Hinweis, Fehler.
    class landet am umschließenden <div> (Rasterplatz, Breite); alle übrigen Attribute
    (x-model, autocomplete, placeholder, :disabled …) am <input>. Klassen fürs <input>: input-class.
    Mit Slot-Inhalt ersetzt dieser das <input> (eigenes Bedienelement mit gleichem Rahmen).
--}}
@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'optional' => false,
    'hint' => null, // Text; HTML über <x-slot:hint>
    'error' => null, // null = automatisch aus $errors
    'size' => 'md',
    'old' => true,
    'bag' => 'default',
    'inputClass' => '',
])

@php
    $id ??= \App\Support\Ui::id($name) ?? 'feld-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
    $error ??= \App\Support\Ui::error($errors ?? null, $name, $bag);
    // Hinweis als Prop (Text) oder als Slot <x-slot:hint> (HTML, z. B. mit Link)
    $hasHint = $hint instanceof \Illuminate\View\ComponentSlot ? $hint->hasActualContent() : filled($hint);
    $describedBy = trim(($hasHint ? $id . '-hint ' : '') . ($error ? $id . '-error' : ''));
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <x-ui.label :for="$id" :required="$required" :optional="$optional">{{ $label }}</x-ui.label>
    @endif

    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        <x-ui.input :type="$type" :name="$name" :id="$id" :value="$value" :size="$size"
            :old="$old" :invalid="(bool) $error" :required="$required" :aria-describedby="$describedBy ?: null" :class="$inputClass"
            {{ $attributes->except('class') }} />
    @endif

    @if ($hasHint)
        <x-ui.hint :id="$id . '-hint'">{{ $hint }}</x-ui.hint>
    @endif
    <x-ui.field-error :message="$error" :id="$id . '-error'" />
</div>
