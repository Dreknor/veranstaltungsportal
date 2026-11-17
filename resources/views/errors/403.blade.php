<x-layouts.public :title="'403 – Zugriff verweigert – ' . (config('app.name'))">
    <div class="mx-auto max-w-page px-page py-[clamp(48px,8vw,96px)]">
        <div class="max-w-prose">
            <p class="m-0 mb-3 text-caption text-muted">Fehler 403</p>
            <x-ui.page-header title="Zugriff verweigert"
                subtitle="Sie haben keine Berechtigung, auf diese Seite zuzugreifen. Falls Sie glauben, dass dies ein Fehler ist, wenden Sie sich bitte an den Administrator." />

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button :href="route('home')">Zur Startseite</x-ui.button>
                <x-ui.button variant="secondary" onclick="window.history.back()">Zurück</x-ui.button>
            </div>
        </div>
    </div>
</x-layouts.public>
