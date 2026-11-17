<x-layouts.public :title="'404 – Seite nicht gefunden – ' . (config('app.name'))">
    <div class="mx-auto max-w-page px-page py-[clamp(48px,8vw,96px)]">
        <div class="max-w-prose">
            <p class="m-0 mb-3 text-caption text-muted">Fehler 404</p>
            <x-ui.page-header title="Seite nicht gefunden"
                subtitle="Die von Ihnen gesuchte Seite existiert leider nicht. Möglicherweise wurde sie verschoben oder gelöscht." />

            <div class="flex flex-wrap items-center gap-3">
                <x-ui.button :href="route('home')">Zur Startseite</x-ui.button>
                <x-ui.button variant="secondary" onclick="window.history.back()">Zurück</x-ui.button>
            </div>

            <nav aria-label="Hilfreiche Links" class="mt-12 border-t border-line pt-4">
                <p class="m-0 text-small text-muted">Hilfreiche Links</p>
                <ul class="m-0 mt-1 flex list-none flex-wrap gap-x-6 p-0">
                    <li><x-ui.button variant="link" :href="route('events.index')">Veranstaltungen</x-ui.button></li>
                    <li><x-ui.button variant="link" :href="route('events.calendar')">Kalender</x-ui.button></li>
                    <li><x-ui.button variant="link" :href="route('contact.show')">Kontakt</x-ui.button></li>
                </ul>
            </nav>
        </div>
    </div>
</x-layouts.public>
