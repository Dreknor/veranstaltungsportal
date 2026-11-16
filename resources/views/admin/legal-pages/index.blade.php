<x-layouts.app title="Rechtliche Seiten">
    <x-ui.page-header title="Rechtliche Seiten" subtitle="Impressum, Datenschutzerklärung und AGB verwalten." />

    <x-ui.row-list>
        @foreach (\App\Models\LegalPage::TYPES as $type => $label)
            @php
                $page = isset($pages) ? $pages->get($type) : null;
                $routeName = match ($type) {
                    'agb' => 'agb',
                    'datenschutz' => 'datenschutz',
                    'impressum' => 'impressum',
                };
            @endphp
            <x-ui.row>
                <h2 class="m-0 text-h3">{{ $label }}</h2>
                @if ($page)
                    <p class="m-0 text-small text-muted">Titel: {{ $page->title }}</p>
                @else
                    <p class="m-0 text-small text-muted">Diese Seite wurde noch nicht bearbeitet.</p>
                @endif

                <x-slot:meta>
                    @if ($page && $page->last_updated_at)
                        <span class="text-ui font-medium tabular-nums">Zuletzt bearbeitet: {{ $page->last_updated_at->format('d.m.Y H:i') }} Uhr</span>
                    @endif
                    @if ($page && $page->editor)
                        <span class="text-small text-muted">Von {{ $page->editor->name }}</span>
                    @endif
                </x-slot:meta>

                <x-slot:aside>
                    @if ($page)
                        <x-ui.status tone="success">Gepflegt</x-ui.status>
                    @else
                        <x-ui.status tone="warning">Nicht angelegt</x-ui.status>
                    @endif
                </x-slot:aside>

                <x-slot:action>
                    <div class="flex flex-col gap-2">
                        <x-ui.button variant="secondary" :href="route('admin.legal-pages.edit', $type)" full>Bearbeiten</x-ui.button>
                        <x-ui.button variant="link" :href="route($routeName)" target="_blank" rel="noopener">
                            <x-slot:icon><x-icon.external-link /></x-slot:icon>
                            Vorschau
                        </x-ui.button>
                    </div>
                </x-slot:action>
            </x-ui.row>
        @endforeach
    </x-ui.row-list>
</x-layouts.app>
