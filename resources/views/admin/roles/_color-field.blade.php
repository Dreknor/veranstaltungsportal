{{-- Rollenfarbe: nur Töne aus der Palette (config/permission-labels.php). --}}
@php
    $selected = old('color', \App\Support\AccessLabels::toneKey($current ?? null));
@endphp
<fieldset class="m-0 min-w-0 border-0 p-0">
    <x-ui.label as="legend">Farbe der Rolle</x-ui.label>
    <div class="flex flex-wrap gap-x-6">
        @foreach (\App\Support\AccessLabels::toneOptions() as $key => $label)
            <x-ui.radio name="color" :value="$key" :checked="$selected === $key" :old="false"
                :id="'role-color-' . ($key)">
                <x-ui.status :tone="\App\Support\AccessLabels::roleTone($key)">{{ $label }}</x-ui.status>
            </x-ui.radio>
        @endforeach
    </div>
    <x-ui.field-error name="color" />
</fieldset>
