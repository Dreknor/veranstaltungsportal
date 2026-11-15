{{-- Hinweis unter einem Feld. --}}
@props(['id' => null])

<p @if ($id) id="{{ $id }}" @endif
    {{ $attributes->class('mt-1.5 text-small text-muted') }}>{{ $slot }}</p>
