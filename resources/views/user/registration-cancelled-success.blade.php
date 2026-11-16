<x-layouts.public title="Registrierung storniert">
    <x-ui.auth-frame>
        <x-ui.page-header title="Registrierung erfolgreich storniert" />

        <p class="m-0 mb-3 text-base">
            Ihr Konto <strong class="font-semibold break-words">{{ $email }}</strong> wurde erfolgreich gelöscht.
        </p>
        <p class="m-0 mb-8 text-base text-muted">
            Sie erhalten eine Bestätigungs-E-Mail an Ihre E-Mail-Adresse.
        </p>

        <x-ui.button :href="route('home')" full>Zur Startseite</x-ui.button>
    </x-ui.auth-frame>
</x-layouts.public>
