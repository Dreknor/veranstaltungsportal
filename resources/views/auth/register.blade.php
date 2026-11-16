<x-layouts.public :title="__('Konto erstellen')">
    <x-ui.auth-frame>
        <x-ui.page-header :title="__('Konto erstellen')" />

        <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-5" x-data="{ userType: @js(old('account_type', 'participant')) }"
            data-recaptcha data-recaptcha-action="register">
            @csrf
            <x-ui.field label="Vollständiger Name" name="name" type="text" autocomplete="name" autofocus />

            <x-ui.field label="E-Mail" name="email" type="email" autocomplete="email" />

            <!-- Kontotyp -->
            <fieldset class="m-0 min-w-0 border-0 p-0">
                <x-ui.label as="legend">{{ __('Kontotyp') }}</x-ui.label>
                <div class="flex flex-col gap-3">
                    <x-ui.choice name="account_type" value="participant" :title="__('Teilnehmer')" :description="__('Ich möchte Veranstaltungen besuchen')" checked
                        x-model="userType" />

                    @if ($allowOrganizerRegistration)
                        <x-ui.choice name="account_type" value="organizer" :title="__('Veranstalter')" :description="__('Ich möchte Veranstaltungen anbieten')"
                            x-model="userType" />
                    @endif
                </div>
                <x-ui.field-error name="account_type" />
            </fieldset>

            {{-- Organisationsfelder, nur bei Kontotyp Veranstalter --}}
            <div x-show="userType === 'organizer'" class="flex flex-col gap-5">
                <x-ui.field label="Organisation / Firma" name="organization_name" type="text"
                    autocomplete="organization" x-bind:required="userType === 'organizer'" />

                <x-ui.textarea label="Beschreibung" name="organization_description" rows="3" optional
                    autocomplete="off" />
            </div>

            <x-ui.field label="Passwort" name="password" type="password" autocomplete="new-password" />

            <x-ui.field label="Passwort bestätigen" name="password_confirmation" type="password"
                autocomplete="new-password" />

            <!-- reCAPTCHA -->
            <x-recaptcha action="register" />

            <x-ui.button type="submit" full>{{ __('Konto erstellen') }}</x-ui.button>
        </form>

        @if (config('services.keycloak.client_id') || config('services.google.client_id') || config('services.github.client_id'))
            <div class="mt-8 flex flex-col gap-3 border-t border-line pt-8">
                <p class="m-0 text-small text-muted">{{ __('oder') }}</p>

                @if (config('services.keycloak.client_id'))
                    <x-ui.button variant="secondary" :href="route('sso.redirect', 'keycloak')"
                        full>{{ __('Mit KeyCloak registrieren') }}</x-ui.button>
                @endif

                @if (config('services.google.client_id'))
                    <x-ui.button variant="secondary" :href="route('sso.redirect', 'google')"
                        full>{{ __('Mit Google registrieren') }}</x-ui.button>
                @endif

                @if (config('services.github.client_id'))
                    <x-ui.button variant="secondary" :href="route('sso.redirect', 'github')"
                        full>{{ __('Mit GitHub registrieren') }}</x-ui.button>
                @endif
            </div>
        @endif

        <p class="mt-8 mb-0 text-small text-muted">
            {{ __('Bereits ein Konto?') }}
            <a href="{{ route('login') }}"
                class="font-medium text-ink underline underline-offset-[3px]">{{ __('Jetzt anmelden') }}</a>
        </p>
    </x-ui.auth-frame>
</x-layouts.public>
