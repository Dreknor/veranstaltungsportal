{{-- Symbol des Abzeichens: Auswahl aus den eigenen Symbolen des Portals (resources/views/components/icon). --}}
@php
    $selected = old('icon', \App\Support\AdminLabels::badgeIconKey($current ?? null));
    $options = \App\Support\AdminLabels::badgeIcons($current ?? null);
@endphp
<fieldset class="m-0 min-w-0 border-0 p-0">
    <x-ui.label as="legend" required>Symbol</x-ui.label>
    <x-ui.hint class="mb-1">Ohne eigenes Bild zeigt das Abzeichen dieses Symbol.</x-ui.hint>
    <div class="grid grid-cols-2 gap-x-4 sm:grid-cols-3">
        @foreach ($options as $key => $label)
            <x-ui.radio name="icon" :value="$key" :checked="$selected === $key" :old="false"
                id="badge-icon-{{ $key }}">
                <span class="inline-flex items-center gap-2">
                    <x-dynamic-component :component="'icon.' . $key" class="h-5 w-5 flex-none" />
                    {{ $label }}
                </span>
            </x-ui.radio>
        @endforeach
    </div>
    <x-ui.field-error name="icon" />
</fieldset>
