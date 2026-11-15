{{-- Gesamtzeile der Zusammenfassung: Linie ink darüber, Betrag 24 px / 600. --}}
@props([
    'label' => 'Gesamt',
    'amount' => null,
])

<div {{ $attributes->class('flex items-baseline justify-between gap-4 border-t border-ink pt-4 tabular-nums') }}>
    <span class="text-base font-semibold">{{ $label }}</span>
    <span class="text-h2 leading-[1.2] tracking-[-0.02em] whitespace-nowrap">{{ $amount ?? $slot }}</span>
</div>
