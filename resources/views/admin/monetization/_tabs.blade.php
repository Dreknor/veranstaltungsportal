{{-- Reiter der Monetarisierung (gleich auf allen Seiten). $active: fees | billing | numbers | featured --}}
@php
    $pendingFeatured = \App\Models\FeaturedEventFee::where('payment_status', 'pending')->count();
    $showFeatured =
        $active === 'featured' ||
        config('monetization.featured_event_enabled', true) ||
        \App\Models\FeaturedEventFee::where('payment_status', 'paid')->exists();
@endphp
<x-ui.tabs label="Bereiche der Monetarisierung" class="mb-8" wrap>
    <x-ui.tab :href="route('admin.monetization.index')" :active="$active === 'fees'">Gebühren-Einstellungen</x-ui.tab>
    <x-ui.tab :href="route('admin.monetization.billing-data')" :active="$active === 'billing'">Plattform-Rechnungsdaten</x-ui.tab>
    <x-ui.tab :href="route('admin.settings.invoice.index')" :active="$active === 'numbers'">Rechnungsnummern</x-ui.tab>
    @if ($showFeatured)
        <x-ui.tab :href="route('admin.monetization.featured-events')" :active="$active === 'featured'" :count="$pendingFeatured > 0 ? $pendingFeatured : null">Übersicht der Hervorhebungen</x-ui.tab>
    @endif
</x-ui.tabs>
