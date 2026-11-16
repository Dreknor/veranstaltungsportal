<x-layouts.public title="Link ungültig">
    <x-ui.auth-frame>
        <x-ui.page-header title="Dieser Link ist ungültig"
            subtitle="Der Abmelde-Link ist abgelaufen oder wurde verändert." />

        <p class="m-0 mb-8 text-base text-muted">
            Bitte öffnen Sie den Link direkt aus der Newsletter-E-Mail. Sie können sich auch nach der Anmeldung
            in Ihren Einstellungen vom Newsletter abmelden.
        </p>

        <x-ui.button :href="route('home')" full>Zur Startseite</x-ui.button>
    </x-ui.auth-frame>
</x-layouts.public>
