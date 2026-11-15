{{--
    Suchzeile (B3): ein Rahmen mit Suchfeld, weiteren Zellen (<x-ui.search-cell>) und Button "Suchen".
    Unter 768 px: Suche volle Breite, Zellen je zur Hälfte, Button volle Breite.

    Slots:
      Standard-Slot – Zellen zwischen Suchfeld und Button (<x-ui.search-cell>)
      hidden        – versteckte Felder (z. B. Sortierung beibehalten)
      filters       – weitere Filter; erscheinen hinter einem Sekundärbutton "Filter" in einem Aufklappfeld
      active        – Textzeile der aktiven Filter unter der Suche ("Kategorie: … · Zurücksetzen")
--}}
@props([
    'action' => null, // Ziel-URL; Standard: aktuelle Seite
    'method' => 'get',
    'name' => 'search',
    'value' => null, // Standard: request($name)
    'label' => 'Suche',
    'placeholder' => 'Thema, Titel oder Ort',
    'button' => 'Suchen',
    'ariaLabel' => 'Veranstaltungen suchen',
    'filterCount' => 0, // Zahl der aktiven weiteren Filter (für den Button "Filter")
    'hidden' => null,
    'filters' => null,
    'active' => null,
])

@php
    $id = \App\Support\Ui::id($name) ?? 'search';
    $value ??= request()->input(\App\Support\Ui::key($name) ?? $name);
    $hasFilters = $filters instanceof \Illuminate\View\ComponentSlot && $filters->isNotEmpty();
    $hasActive = $active instanceof \Illuminate\View\ComponentSlot && $active->isNotEmpty();
@endphp

<form
    role="search"
    aria-label="{{ $ariaLabel }}"
    action="{{ $action ?? url()->current() }}"
    method="{{ strtolower($method) === 'get' ? 'get' : 'post' }}"
    @if ($hasFilters) x-data="{ filtersOpen: false }" @endif
    {{ $attributes->class('m-0 flex flex-col gap-3') }}
>
    @if (strtolower($method) !== 'get')
        @csrf
    @endif
    {{ $hidden }}

    <div class="ui-search">
        <div class="ui-search-q">
            <label for="{{ $id }}" class="ui-search-label">{{ $label }}</label>
            <input
                id="{{ $id }}"
                name="{{ $name }}"
                type="search"
                value="{{ is_string($value) ? $value : '' }}"
                placeholder="{{ $placeholder }}"
                class="ui-search-control"
            >
        </div>

        {{ $slot }}

        <div class="ui-search-button">
            <button type="submit" class="inline-flex min-h-14 w-full cursor-pointer items-center justify-center gap-2 rounded-control border border-primary bg-primary px-6 text-base font-medium text-on-primary transition-colors duration-150 hover:border-ink hover:bg-ink">
                <x-icon.search class="h-[18px] w-[18px]" />
                {{ $button }}
            </button>
        </div>
    </div>

    @if ($hasFilters || $hasActive)
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
            @if ($hasFilters)
                <x-ui.button
                    variant="secondary"
                    x-on:click="filtersOpen = ! filtersOpen"
                    x-bind:aria-expanded="filtersOpen.toString()"
                    aria-expanded="false"
                    aria-controls="{{ $id }}-filters"
                >
                    <x-slot:icon><x-icon.filter /></x-slot:icon>
                    Filter
                    @if ($filterCount > 0)
                        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary px-1.5 text-caption font-semibold text-on-primary tabular-nums">{{ $filterCount }}</span>
                        <span class="sr-only">aktiv</span>
                    @endif
                </x-ui.button>
            @endif
            @if ($hasActive)
                <p class="m-0 text-small text-ink-soft">{{ $active }}</p>
            @endif
        </div>
    @endif

    @if ($hasFilters)
        <div id="{{ $id }}-filters" x-show="filtersOpen" x-cloak class="rounded-box border border-line bg-surface p-6">
            {{ $filters }}
        </div>
    @endif
</form>
