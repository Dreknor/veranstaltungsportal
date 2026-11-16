<x-layouts.public title="Vom Newsletter abgemeldet">
    <x-ui.auth-frame>
        <x-ui.page-header title="Sie sind abgemeldet"
            subtitle="Sie erhalten keine Newsletter mehr von uns." />

        <x-ui.button :href="route('home')" full>Zur Startseite</x-ui.button>
    </x-ui.auth-frame>
</x-layouts.public>
