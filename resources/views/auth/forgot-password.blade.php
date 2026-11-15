<x-layouts.public :title="__('Passwort vergessen')">
    <div class="mx-auto max-w-page px-page py-[clamp(32px,6vw,64px)]">
        <div class="mx-auto max-w-[440px]">
            <x-ui.page-header :title="__('Passwort vergessen')"
                :subtitle="__('Geben Sie Ihre E-Mail-Adresse ein, um einen Link zum Zurücksetzen des Passworts zu erhalten')" />

            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5" data-recaptcha
                data-recaptcha-action="password_reset">
                @csrf
                <x-ui.field label="E-Mail" name="email" type="email" autocomplete="email" autofocus />

                <!-- reCAPTCHA -->
                <x-recaptcha action="password_reset" />

                <x-ui.button type="submit" full>{{ __('Link zum Zurücksetzen senden') }}</x-ui.button>
            </form>

            <p class="mt-6 mb-0 text-small text-muted">
                <a href="{{ route('login') }}"
                    class="font-medium text-ink underline underline-offset-[3px]">{{ __('Zurück zur Anmeldung') }}</a>
            </p>
        </div>
    </div>
</x-layouts.public>
