{{--
    Suchzeile für Startseite und Veranstaltungsübersicht (B3): Suche, Format, Zeitraum, Button "Suchen".
    Kategorie, Ort sowie Von/Bis liegen hinter dem Button "Filter".

    Parameter:
      $categoryOptions – aktive Kategorien als [id => Name]
      $activeFormat    – gültiger Format-Schlüssel (optional; Standard aus der Anfrage)
      $activePeriod    – gültiger Zeitraum-Schlüssel (optional; Standard aus der Anfrage, entfällt bei Von/Bis)
      $searchClass     – zusätzliche Klassen für den Rahmen (optional)
--}}
@php
    $categoryOptions = collect($categoryOptions ?? [])->all();
    $hasDateRange = request()->filled('date_from') || request()->filled('date_to');
    $activeFormat ??= \App\Support\EventFilters::format(request()->query('format'));
    $activePeriod ??= $hasDateRange ? null : \App\Support\EventFilters::period(request()->query('zeitraum'));

    $filterCount = collect(['category', 'city', 'date_from', 'date_to'])
        ->filter(fn($key) => request()->filled($key))
        ->count();

    $activeCategory = request()->filled('category') ? $categoryOptions[(int) request('category')] ?? null : null;
    $formatDate = fn($value) => rescue(fn() => \Carbon\Carbon::parse($value)->format('d.m.Y'), $value, false);

    $activeFilters = array_filter([
        $activeFormat ? 'Format: ' . \App\Support\EventFilters::FORMAT_LABELS[$activeFormat] : null,
        $activePeriod ? 'Zeitraum: ' . \App\Support\EventFilters::PERIOD_LABELS[$activePeriod] : null,
        $activeCategory ? 'Kategorie: ' . $activeCategory : null,
        request()->filled('city') ? 'Ort: ' . request('city') : null,
        request()->filled('date_from') ? 'ab ' . $formatDate(request('date_from')) : null,
        request()->filled('date_to') ? 'bis ' . $formatDate(request('date_to')) : null,
    ]);
@endphp

<x-ui.search-bar :action="route('events.index')" :filter-count="$filterCount" class="{{ $searchClass ?? '' }}">
    <x-ui.search-cell label="Format" name="format" :options="\App\Support\EventFilters::formatOptions()" :selected="$activeFormat ?? ''" />
    <x-ui.search-cell label="Zeitraum" name="zeitraum" wide :options="\App\Support\EventFilters::periodOptions()" :selected="$activePeriod ?? ''" />
    <x-slot:filters>
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                <x-ui.select label="Kategorie" name="category" :options="$categoryOptions" :selected="request('category')"
                    placeholder="Alle Kategorien" :error="false" />
                <x-ui.field label="Ort" name="city" :value="request('city')" placeholder="Beliebig"
                    autocomplete="address-level2" :error="false" />
            </div>
            <fieldset class="m-0 min-w-0 border-0 p-0">
                <legend class="mb-1 p-0 text-h3 text-ink">Eigener Zeitraum</legend>
                <p class="m-0 mb-4 text-small text-muted">Von und Bis haben Vorrang vor der Auswahl „Zeitraum“.</p>
                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="Von" name="date_from" type="date" :value="request('date_from')" :error="false" />
                    <x-ui.field label="Bis" name="date_to" type="date" :value="request('date_to')" :error="false" />
                </div>
            </fieldset>
        </div>
    </x-slot:filters>
    @if ($activeFilters || request()->filled('search'))
        <x-slot:active>
            {{ implode(' · ', $activeFilters) }}@if ($activeFilters)
                ·
            @endif
            <a href="{{ route('events.index') }}" class="font-medium text-ink underline underline-offset-[3px]">Filter
                zurücksetzen</a>
        </x-slot:active>
    @endif
</x-ui.search-bar>
