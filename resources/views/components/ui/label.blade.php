{{-- Feldbeschriftung. as="legend" für <fieldset>, as="span" für Gruppen ohne eigenes Feld. --}}
@props([
    'for' => null,
    'required' => false,
    'optional' => false,
    'as' => 'label',
])

<{{ $as }} @if ($as === 'label' && $for) for="{{ $for }}" @endif
    {{ $attributes->class('mb-1.5 block p-0 text-small font-medium text-ink') }}>
    {{ $slot }}
    @if ($required)
        <span aria-hidden="true">*</span>
    @endif
    @if ($optional)
        <span class="font-normal text-muted">(optional)</span>
    @endif
    </{{ $as }}>
