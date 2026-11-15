{{-- Alter Baustein: dünne Hülle um <x-ui.field>. class landet am äußeren Element, alles Übrige am <input>. --}}
@props([
    'label' => null,
    'name',
    'type' => 'text',
    'placeholder' => '',
    'error' => false,
    'class' => '',
    'labelClass' => '',
])

<x-ui.field :label="$label" :name="$name" :type="$type" :placeholder="$placeholder" :class="$class" {{ $attributes }} />
