<x-layouts.public title="Kontotypen">
    <div class="mx-auto max-w-page px-page py-[clamp(32px,6vw,64px)]">
        <x-ui.page-header title="Kontotypen" subtitle="Die verschiedenen Kontotypen im Überblick" />

        <div class="grid gap-x-12 gap-y-12 md:grid-cols-2">
            <!-- Teilnehmer -->
            <section aria-labelledby="kontotyp-teilnehmer" class="border-t border-line pt-6">
                <h2 id="kontotyp-teilnehmer" class="m-0 mb-1 text-h2">Teilnehmer</h2>
                <p class="m-0 mb-6 text-base text-muted">Für Besucher von Veranstaltungen</p>

                <ul class="m-0 flex list-none flex-col gap-3 p-0 text-base">
                    @foreach (['Veranstaltungen durchsuchen und ansehen', 'Tickets buchen', 'Veranstaltungen bewerten', 'Veranstaltungen als Favoriten speichern', 'Buchungsverlauf einsehen'] as $feature)
                        <li class="flex items-start gap-3">
                            <x-icon.check class="mt-0.5 h-5 w-5 flex-none text-ink" />
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>

                @auth
                    @if (auth()->user()->isParticipant())
                        <p class="mt-6 mb-0"><x-ui.status tone="success">Ihr aktueller Kontotyp</x-ui.status></p>
                    @endif
                @endauth
            </section>

            <!-- Veranstalter -->
            <section aria-labelledby="kontotyp-organisator" class="border-t border-line pt-6">
                <h2 id="kontotyp-organisator" class="m-0 mb-1 text-h2">Veranstalter</h2>
                <p class="m-0 mb-6 text-base text-muted">Für alle, die Veranstaltungen anbieten</p>

                <ul class="m-0 flex list-none flex-col gap-3 p-0 text-base">
                    @foreach (['Alle Teilnehmer-Funktionen', 'Veranstaltungen erstellen und verwalten', 'Ticketarten konfigurieren', 'Rabattcodes erstellen', 'Buchungen verwalten', 'Statistiken und Berichte'] as $feature)
                        <li class="flex items-start gap-3">
                            <x-icon.check class="mt-0.5 h-5 w-5 flex-none text-ink" />
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>

                @auth
                    @if (auth()->user()->isOrganizer())
                        <p class="mt-6 mb-0"><x-ui.status tone="success">Ihr aktueller Kontotyp</x-ui.status></p>
                    @endif
                @endauth
            </section>
        </div>

        @guest
            <div class="mt-12 flex flex-col items-start gap-4 border-t border-line pt-8">
                <p class="m-0 max-w-prose text-base text-muted">
                    Wählen Sie bei der Registrierung den passenden Kontotyp für Ihre Bedürfnisse.
                </p>
                <x-ui.button :href="route('register')">Jetzt registrieren</x-ui.button>
            </div>
        @endguest

        @auth
            <x-ui.alert tone="info" class="mt-12 max-w-prose">
                Möchten Sie Ihren Kontotyp ändern? Kontaktieren Sie bitte unseren Support.
            </x-ui.alert>
        @endauth
    </div>
</x-layouts.public>
