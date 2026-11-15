{{-- Alter Baustein: dünne Hülle um <x-ui.button>. type: primary | danger; buttonType: submit | button. --}}
@props([
    'type' => 'primary',
    'buttonType' => 'submit',
    'tag' => 'button',
])

<x-ui.button :variant="$type" :type="$buttonType" {{ $attributes }}>{{ $slot }}</x-ui.button>
