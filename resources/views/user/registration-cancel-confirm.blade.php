<x-layouts.public title="Registrierung rückgängig machen">
    <div class="mx-auto max-w-page px-page py-[clamp(32px,6vw,64px)]">
        <div class="mx-auto max-w-[440px]">
            <x-ui.page-header title="Registrierung rückgängig machen?"
                subtitle="Sie sind dabei, die Registrierung für das folgende Konto zu stornieren:" />

            <p class="m-0 mb-6 border-y border-line py-4 text-base font-semibold break-words">{{ $email }}</p>

            <x-ui.alert tone="warning" class="mb-8">
                <strong>Achtung:</strong> Dieser Vorgang kann nicht rückgängig gemacht werden. Ihr Konto und alle damit verbundenen Daten werden unwiderruflich gelöscht.
            </x-ui.alert>

            <form method="POST" action="{{ route('user.cancel-registration.process', $token) }}" class="flex flex-col gap-3">
                @csrf
                <x-ui.button type="submit" variant="danger" full>Ja, Registrierung stornieren</x-ui.button>
                <x-ui.button variant="secondary" :href="route('home')" full>Abbrechen</x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.public>
