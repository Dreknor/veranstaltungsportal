<x-layouts.public :title="__('Anmelden')">
    <div class="mx-auto max-w-page px-page py-[clamp(32px,6vw,64px)]">
        <div class="mx-auto max-w-[440px]">
            <x-ui.page-header :title="__('Bei Ihrem Konto anmelden')" />

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5" data-recaptcha
                data-recaptcha-action="login">
                @csrf
                <x-ui.field label="E-Mail" name="email" type="email" autocomplete="email" autofocus />

                <div>
                    <x-ui.field label="Passwort" name="password" type="password" autocomplete="current-password" />
                    @if (Route::has('password.request'))
                        <p class="mt-2 mb-0 text-small">
                            <a href="{{ route('password.request') }}"
                                class="font-medium text-ink underline underline-offset-[3px]">{{ __('Passwort vergessen?') }}</a>
                        </p>
                    @endif
                </div>

                <x-ui.checkbox label="Angemeldet bleiben" name="remember" />

                <!-- reCAPTCHA -->
                <x-recaptcha action="login" />

                <x-ui.button type="submit" full>{{ __('Anmelden') }}</x-ui.button>
            </form>

            @if (config('services.keycloak.client_id') || config('services.google.client_id') || config('services.github.client_id'))
                <div class="mt-8 flex flex-col gap-3 border-t border-line pt-8">
                    <p class="m-0 text-small text-muted">{{ __('oder') }}</p>

                    @if (config('services.keycloak.client_id'))
                        <x-ui.button variant="secondary" :href="route('sso.redirect', 'keycloak')" full>{{ __('Mit KeyCloak anmelden') }}</x-ui.button>
                    @endif

                    @if (config('services.google.client_id'))
                        <x-ui.button variant="secondary" :href="route('sso.redirect', 'google')" full>{{ __('Mit Google anmelden') }}</x-ui.button>
                    @endif

                    @if (config('services.github.client_id'))
                        <x-ui.button variant="secondary" :href="route('sso.redirect', 'github')" full>{{ __('Mit GitHub anmelden') }}</x-ui.button>
                    @endif
                </div>
            @endif

            @if (Route::has('register'))
                <p class="mt-8 mb-0 text-small text-muted">
                    {{ __('Noch kein Konto?') }}
                    <a href="{{ route('register') }}"
                        class="font-medium text-ink underline underline-offset-[3px]">{{ __('Jetzt registrieren') }}</a>
                </p>
            @endif
        </div>
    </div>
</x-layouts.public>
