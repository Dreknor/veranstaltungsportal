<x-layouts.public title="Vom Newsletter abmelden">
    <x-ui.auth-frame>
        <x-ui.page-header title="Vom Newsletter abmelden"
            subtitle="Sie erhalten dann keine Newsletter mehr an {{ $user->email }}." />

        <p class="m-0 mb-8 text-base text-muted">
            Wichtige Nachrichten zu Ihren Buchungen und Ihrem Konto erhalten Sie weiterhin. Sie können den
            Newsletter jederzeit in Ihren Einstellungen wieder abonnieren.
        </p>

        <form method="POST" action="{{ $actionUrl }}" class="flex flex-col gap-3">
            @csrf
            <x-ui.button type="submit" full>Vom Newsletter abmelden</x-ui.button>
            <x-ui.button variant="secondary" :href="route('home')" full>Abbrechen</x-ui.button>
        </form>
    </x-ui.auth-frame>
</x-layouts.public>
