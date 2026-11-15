{{--
    Kennzahl als schlichter Kasten: Label (small, muted), Wert (24 px, 600), optional Unterzeile.
    Mehrere Kennzahlen in ein Raster: <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">.
    href macht den ganzen Kasten zum Link.
--}}
@props([
    'label',
    'value' => null, // ersatzweise Slot
    'sub' => null,
    'href' => null,
])

@php $tag = $href ? 'a' : 'div'; @endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->class([
    'flex flex-col gap-1 rounded-box border border-line bg-surface p-5 text-ink no-underline',
    'transition-colors duration-150 hover:border-line-strong hover:bg-zone-hover' => $href,
]) }}>
    <span class="text-small text-muted">{{ $label }}</span>
    <span class="text-h2 tabular-nums">{{ $value ?? $slot }}</span>
    @if ($sub)
        <span class="text-small text-ink-soft">{{ $sub }}</span>
    @endif
</{{ $tag }}>
