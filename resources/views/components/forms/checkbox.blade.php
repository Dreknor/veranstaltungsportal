{{-- Alter Baustein: dünne Hülle um <x-ui.checkbox>; das versteckte Feld sendet "0", wenn nichts gewählt ist. --}}
@props(['label', 'name', 'value' => null])

<input type="hidden" name="{{ $name }}" value="0">
<x-ui.checkbox :name="$name" :value="$value ?? '1'" :label="$label" {{ $attributes }} />
