<x-layouts.public title="Fehler">
    <x-ui.auth-frame>
        <x-ui.page-header title="Fehler" />

        <x-ui.alert tone="danger" size="base" class="mb-8">{{ $message }}</x-ui.alert>

        <x-ui.button :href="route('home')" full>Zur Startseite</x-ui.button>
    </x-ui.auth-frame>
</x-layouts.public>
