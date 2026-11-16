<x-layouts.app title="Darstellung">
    <x-ui.page-header title="Einstellungen" class="mb-4" />

    @include('settings.partials.navigation')

    <div class="max-w-form">
        <x-ui.section title="Darstellung">
            {{-- Umschalter bis zur Abnahme einer dunklen Palette ausgeblendet (Richtlinien B2) --}}
            <x-ui.alert tone="info">Die dunkle Darstellung ist derzeit nicht verfügbar.</x-ui.alert>
        </x-ui.section>
    </div>
</x-layouts.app>
