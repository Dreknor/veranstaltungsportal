{{--
    Mehrzeiliges Feld für Markdown (z. B. Veranstaltungsbeschreibung): Label, Werkzeugleiste
    (Fett, Kursiv, Überschrift, Aufzählung, Nummerierung, Link, Vorschau), <x-ui.textarea>, Hinweis, Fehler.
    Die Vorschau rendert der Server mit App\Support\Ui::markdown() – genauso wie die spätere Anzeige.
    Tastenkürzel im Feld: Strg/Cmd+B, Strg/Cmd+I, Strg/Cmd+K. Props wie <x-ui.textarea>; class landet am
    äußeren <div>, alle übrigen Attribute am <textarea>. Braucht Alpine (resources/js/markdown-editor.js).
--}}
@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'rows' => 8,
    'required' => false,
    'optional' => false,
    'hint' => 'Formatieren Sie mit der Werkzeugleiste oder mit Markdown, z. B. **fett** oder „- “ am Zeilenanfang für eine Aufzählung.',
    'error' => null,
    'old' => true,
    'bag' => 'default',
    'previewUrl' => null,
])

@php
    $id ??= \App\Support\Ui::id($name) ?? 'feld-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
    $previewUrl ??= route('markdown.preview');
    $tools = [
        ['bold', 'Fett', 'Fett (Strg+B)'],
        ['italic', 'Kursiv', 'Kursiv (Strg+I)'],
        ['heading', 'Überschrift', 'Überschrift'],
        ['bullets', 'Aufzählung', 'Aufzählung'],
        ['numbers', 'Nummerierung', 'Nummerierte Liste'],
        ['link', 'Link', 'Link einfügen (Strg+K)'],
    ];
@endphp

<div x-data="markdownEditor({ previewUrl: @js($previewUrl) })" {{ $attributes->only('class') }}>
    @if ($label)
        <x-ui.label :for="$id" :required="$required" :optional="$optional">{{ $label }}</x-ui.label>
    @endif

    <div role="toolbar" aria-label="Formatierung{{ $label ? ': ' . $label : '' }}" aria-controls="{{ $id }}"
        class="mb-2 flex flex-wrap gap-2"
        x-on:keydown.right.prevent="moveFocus($event, 1)" x-on:keydown.left.prevent="moveFocus($event, -1)">
        @foreach ($tools as [$action, $text, $title])
            <x-ui.button variant="secondary" size="sm" x-on:click="{{ $action }}()" title="{{ $title }}">{{ $text }}</x-ui.button>
        @endforeach
        <x-ui.button variant="secondary" size="sm" x-on:click="togglePreview()" aria-pressed="false"
            x-bind:aria-pressed="preview.toString()" aria-controls="{{ $id }}-preview"
            class="aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold sm:ml-auto">
            <x-slot:icon>
                <x-icon.eye />
            </x-slot:icon>
            Vorschau
        </x-ui.button>
    </div>

    <x-ui.textarea :name="$name" :id="$id" :value="$value" :rows="$rows" :required="$required" :hint="$hint"
        :error="$error" :old="$old" :bag="$bag" x-ref="input" x-on:keydown="shortcut($event)" x-on:input="changed()"
        {{ $attributes->except('class') }} />

    <section id="{{ $id }}-preview" x-show="preview" x-cloak aria-label="Vorschau"
        class="mt-3 rounded-box border border-line p-4">
        <p class="m-0 mb-3 text-small text-muted" aria-live="polite">
            <span x-show="loading">Vorschau wird erstellt …</span>
            <span x-show="!loading && failed">Die Vorschau konnte nicht geladen werden.</span>
            <span x-show="!loading && !failed">So erscheint der Text auf der Seite:</span>
        </p>
        <div class="richtext" x-html="html"></div>
        <p x-show="!loading && !failed && html === ''" class="m-0 text-small text-muted">Noch kein Text eingegeben.</p>
    </section>
</div>
