<x-layouts.public :title="__('Passwort bestätigen')">
    <x-ui.auth-frame>
        <x-ui.page-header :title="__('Passwort bestätigen')"
            :subtitle="__('Bitte bestätigen Sie Ihr Passwort, bevor Sie fortfahren.')" />

        <form method="POST" action="{{ route('password.confirm') }}" class="flex flex-col gap-5">
            @csrf
            <x-ui.field label="Passwort" name="password" type="password" autocomplete="current-password" autofocus />

            <x-ui.button type="submit" full>{{ __('Passwort bestätigen') }}</x-ui.button>
        </form>

        <p class="mt-6 mb-0 text-small text-muted">
            <a href="{{ route('password.request') }}"
                class="font-medium text-ink underline underline-offset-[3px]">{{ __('Passwort vergessen?') }}</a>
        </p>
    </x-ui.auth-frame>
</x-layouts.public>
