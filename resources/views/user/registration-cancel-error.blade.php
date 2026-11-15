<x-layouts.public title="Fehler">
    <div class="mx-auto max-w-page px-page py-[clamp(32px,6vw,64px)]">
        <div class="mx-auto max-w-[440px]">
            <x-ui.page-header title="Fehler" />

            <x-ui.alert tone="danger" size="base" class="mb-8">{{ $message }}</x-ui.alert>

            <x-ui.button :href="route('home')" full>Zur Startseite</x-ui.button>
        </div>
    </div>
</x-layouts.public>
