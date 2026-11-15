{{-- Posten mit rechtsbündigem Betrag ("2 × Teilnahme … 90,00 €"). Betrag über amount oder den Slot. --}}
@props([
    'label' => null,
    'amount' => null,
    'muted' => false, // z. B. Rabatt, Gebühren-Hinweis
])

<div {{ $attributes->class(['flex justify-between gap-4 text-ui tabular-nums', 'text-muted' => $muted]) }}>
    <span class="min-w-0">{{ $label }}</span>
    <span class="whitespace-nowrap">{{ $amount ?? $slot }}</span>
</div>
