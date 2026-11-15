<x-layouts.public :title="__('E-Mail bestätigen')">
    <div class="mx-auto max-w-page px-page py-[clamp(32px,6vw,64px)]">
        <div class="mx-auto max-w-[440px]">
            <x-ui.page-header :title="__('E-Mail-Adresse bestätigen')" />

            <p class="mt-0 mb-6 text-base text-muted">
                {{ __('Bitte überprüfen Sie Ihre E-Mails auf einen Bestätigungslink.') }}
                {{ __('Falls Sie keine E-Mail erhalten haben, können Sie unten eine neue anfordern.') }}
            </p>

            <form method="POST" action="{{ route('verification.store') }}">
                @csrf
                <x-ui.button type="submit" full>{{ __('Bestätigungs-E-Mail erneut senden') }}</x-ui.button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <x-ui.button type="submit" variant="link">{{ __('Abmelden') }}</x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.public>
