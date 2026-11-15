<x-layouts.public :title="'Konto erstellen'">
    <div class="mx-auto max-w-page px-page pt-4 pb-[clamp(56px,7vw,96px)]">
        <div class="max-w-form">
            <x-ui.page-header :title="$existingUser ? 'Buchung mit Konto verknüpfen' : 'Benutzerkonto erstellen'"
                :back-href="route('bookings.show', $booking->booking_number)" back-label="Zurück zur Buchungsübersicht">
                <x-slot:meta>
                    <span class="tabular-nums">Buchung: <span
                            class="font-semibold text-ink">{{ $booking->booking_number }}</span></span>
                </x-slot:meta>
            </x-ui.page-header>

            @if ($existingUser)
                {{-- Mit bestehendem Konto verknüpfen --}}
                <x-ui.alert tone="info" title="Bestehendes Konto gefunden" class="mb-8">
                    <p>
                        Für die E-Mail-Adresse <strong class="font-semibold">{{ $booking->customer_email }}</strong>
                        existiert bereits ein Konto. Bitte geben Sie Ihr Passwort ein, um diese Buchung mit Ihrem Konto
                        zu verknüpfen.
                    </p>
                </x-ui.alert>

                <form method="POST" action="{{ route('bookings.link-account', $booking->booking_number) }}"
                    class="flex flex-col gap-6">
                    @csrf
                    <x-ui.field label="Passwort" name="password" type="password" required
                        autocomplete="current-password" placeholder="Ihr Passwort" />

                    <div class="flex flex-wrap gap-3">
                        <x-ui.button type="submit">Buchung verknüpfen</x-ui.button>
                        <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)">
                            Abbrechen
                        </x-ui.button>
                    </div>
                </form>

                <div class="mt-8 border-t border-line pt-6">
                    <p class="m-0 text-small text-muted">Passwort vergessen?</p>
                    <x-ui.button variant="link" :href="route('password.request')" arrow>Passwort zurücksetzen</x-ui.button>
                </div>

                <div class="mt-6 border-t border-line pt-6">
                    <p class="m-0 text-base">Möchten Sie stattdessen ein neues Konto erstellen?</p>
                    <p class="mt-1 mb-0 text-small text-muted">Dies wird ein separates Konto mit einer anderen
                        E-Mail-Adresse sein.</p>
                </div>
            @else
                {{-- Neues Konto anlegen --}}
                <section aria-labelledby="vorteile" class="mb-8">
                    <h2 id="vorteile" class="m-0 mb-3 text-h3">Vorteile eines Benutzerkontos</h2>
                    <ul class="m-0 flex list-none flex-col gap-2 p-0 text-base">
                        @foreach (['Alle Buchungen zentral verwalten', 'Schnellerer Checkout bei zukünftigen Buchungen', 'Persönliche Empfehlungen erhalten', 'Teilnahmezertifikate automatisch speichern'] as $benefit)
                            <li class="flex items-start gap-3">
                                <x-icon.check class="mt-1 h-4 w-4 flex-none text-ink" />
                                {{ $benefit }}
                            </li>
                        @endforeach
                    </ul>
                </section>

                <form method="POST" action="{{ route('bookings.store-account', $booking->booking_number) }}"
                    class="flex flex-col gap-5 border-t border-line pt-8">
                    @csrf
                    <p class="m-0 text-small text-muted">* Pflichtfeld</p>

                    <x-ui.field label="E-Mail-Adresse" id="account_email" type="email" :value="$booking->customer_email"
                        :old="false" disabled hint="Diese E-Mail-Adresse wird für Ihr Konto verwendet." />
                    <x-ui.field label="Name" id="account_name" :value="$booking->customer_name" :old="false" disabled />
                    <x-ui.field label="Passwort" name="password" type="password" required minlength="8"
                        autocomplete="new-password" placeholder="Mindestens 8 Zeichen" />
                    <x-ui.field label="Passwort bestätigen" name="password_confirmation" type="password" required
                        minlength="8" autocomplete="new-password" placeholder="Passwort wiederholen" />

                    <x-ui.checkbox name="terms" required>
                        Ich akzeptiere die <a href="{{ route('agb') }}" target="_blank"
                            class="font-medium underline underline-offset-[3px]">Nutzungsbedingungen</a>
                        und <a href="{{ route('datenschutz') }}" target="_blank"
                            class="font-medium underline underline-offset-[3px]">Datenschutzerklärung</a>
                    </x-ui.checkbox>

                    <div class="flex flex-wrap gap-3">
                        <x-ui.button type="submit">Konto erstellen</x-ui.button>
                        <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)">
                            Später
                        </x-ui.button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-layouts.public>
