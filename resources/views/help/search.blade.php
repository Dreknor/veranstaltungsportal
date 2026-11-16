<x-layouts.app title="Hilfe durchsuchen">
    <x-ui.page-header title="Suchergebnisse" subtitle="Durchsuchen Sie unsere Hilfe-Artikel"
        back-label="Zur Hilfe-Übersicht" :back-href="route('help.index')" />

    <x-ui.search-bar :action="route('help.search')" name="q" :value="$query" label="Suche"
        placeholder="Durchsuchen Sie die Hilfe-Artikel..." aria-label="Hilfe durchsuchen" class="mb-12 max-w-[960px]"
        x-data x-init="$nextTick(() => $el.querySelector('input[name=q]').focus())" />

    @if ($query)
        @if (count($results) > 0)
            <section aria-labelledby="ergebnisse" class="max-w-prose">
                <h2 id="ergebnisse" class="m-0 mb-4 text-h3 text-ink">
                    {{ count($results) }} {{ count($results) === 1 ? 'Ergebnis' : 'Ergebnisse' }} für
                    "{{ $query }}"
                </h2>
                <ul class="m-0 list-none border-b border-line p-0">
                    @foreach ($results as $result)
                        <li class="border-t border-line">
                            <a href="{{ route('help.article', ['category' => $result['category'], 'article' => $result['slug']]) }}"
                                class="flex flex-col gap-1 px-2 py-4 text-ink no-underline transition-colors duration-150 hover:bg-zone-hover">
                                <span class="text-small text-muted">
                                    {{ $result['category'] === 'organizer' ? 'Veranstalter' : 'Nutzer' }}
                                </span>
                                <span class="text-h3">{{ $result['title'] }}</span>
                                <span class="text-base text-muted">{{ $result['description'] }}</span>
                                <span class="mt-1 inline-flex items-center gap-2 text-ui font-medium">
                                    Artikel lesen
                                    <x-icon.arrow-right class="h-4 w-4" />
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @else
            <p class="m-0 mb-4 text-h3 text-ink">Keine Ergebnisse für "{{ $query }}"</p>
            <x-ui.empty title="Keine passenden Artikel gefunden" class="max-w-prose" :action-href="route('help.index')"
                action-label="Zur Hilfe-Übersicht">
                Versuchen Sie es mit anderen Suchbegriffen oder durchsuchen Sie unsere Kategorien.
            </x-ui.empty>

            <section aria-labelledby="beliebte-artikel" class="mt-12 max-w-prose">
                <h2 id="beliebte-artikel" class="m-0 mb-3 text-h3 text-ink">Beliebte Hilfe-Artikel</h2>
                <div class="flex flex-col items-start">
                    <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'getting-started'])">Erste
                        Schritte</x-ui.button>
                    <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'booking-events'])">Veranstaltungen
                        buchen</x-ui.button>
                    <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'manage-bookings'])">Buchungen
                        verwalten</x-ui.button>
                    <x-ui.button variant="link" arrow :href="route('help.article', ['category' => 'user', 'article' => 'troubleshooting'])">Häufige
                        Probleme</x-ui.button>
                </div>
            </section>
        @endif
    @else
        <x-ui.empty title="Geben Sie einen Suchbegriff ein" class="max-w-prose">
            Durchsuchen Sie unsere Hilfe-Artikel nach Themen, Problemen oder Fragen.
        </x-ui.empty>
    @endif
</x-layouts.app>
