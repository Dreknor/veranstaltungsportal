<x-layouts.public title="500 – Serverfehler – {{ config('app.name') }}">
    <div class="mx-auto max-w-page px-page py-[clamp(48px,8vw,96px)]">
        <div class="max-w-prose">
            <p class="m-0 mb-3 text-caption text-muted">Fehler 500</p>
            <x-ui.page-header title="Interner Serverfehler"
                subtitle="Entschuldigung! Etwas ist schief gelaufen. Unser Team wurde benachrichtigt und arbeitet an einer Lösung." />

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button :href="route('home')">Zur Startseite</x-ui.button>
                <x-ui.button variant="secondary" onclick="window.location.reload()">Seite neu laden</x-ui.button>
            </div>

            @if (config('app.debug'))
                <x-ui.alert tone="danger" title="Debug-Information" class="mt-12">
                    <pre class="m-0 mt-2 overflow-auto text-caption">{{ $exception ?? 'Keine Details verfügbar' }}</pre>
                </x-ui.alert>
            @endif
        </div>
    </div>
</x-layouts.public>
