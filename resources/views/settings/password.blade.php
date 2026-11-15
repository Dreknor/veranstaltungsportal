<x-layouts.app>
    <x-ui.page-header title="Einstellungen" class="mb-4" />

    @include('settings.partials.navigation')

    <div class="max-w-form">
        <x-ui.section :title="__('Update password')"
            :description="__('Ensure your account is using a long, random password to stay secure')">
            @if ($user->isSsoUser())
                <x-ui.alert tone="info" :title="__('SSO-Konto')">
                    {{ __('Sie sind über') }} <strong>{{ $user->ssoProviderName() }}</strong>
                    {{ __('angemeldet. Passwortänderungen müssen über Ihren SSO-Provider vorgenommen werden.') }}
                </x-ui.alert>
            @else
                <form action="{{ route('settings.password.update') }}" method="POST" class="flex flex-col gap-y-5">
                    @csrf
                    @method('PUT')

                    <x-ui.field label="Current Password" name="current_password" type="password"
                        autocomplete="current-password" />
                    <x-ui.field label="New Password" name="password" type="password" autocomplete="new-password" />
                    <x-ui.field label="Confirm Password" name="password_confirmation" type="password"
                        autocomplete="new-password" />

                    <div>
                        <x-ui.button type="submit">{{ __('Update Password') }}</x-ui.button>
                    </div>
                </form>
            @endif
        </x-ui.section>
    </div>
</x-layouts.app>
