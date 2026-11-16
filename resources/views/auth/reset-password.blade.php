<x-layouts.public :title="__('Passwort zurücksetzen')">
    <x-ui.auth-frame>
        <x-ui.page-header :title="__('Passwort zurücksetzen')"
            :subtitle="__('Geben Sie Ihre E-Mail-Adresse und Ihr neues Passwort ein.')" />

        <form method="POST" action="{{ route('password.store') }}" class="flex flex-col gap-5">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <x-ui.field label="E-Mail" name="email" type="email" :value="request('email')" autocomplete="email" />

            <x-ui.field label="Passwort" name="password" type="password" autocomplete="new-password" />

            <x-ui.field label="Passwort bestätigen" name="password_confirmation" type="password"
                autocomplete="new-password" />

            <x-ui.button type="submit" full>{{ __('Passwort zurücksetzen') }}</x-ui.button>
        </form>

        <p class="mt-6 mb-0 text-small text-muted">
            <a href="{{ route('login') }}"
                class="font-medium text-ink underline underline-offset-[3px]">{{ __('Zurück zur Anmeldung') }}</a>
        </p>
    </x-ui.auth-frame>
</x-layouts.public>
