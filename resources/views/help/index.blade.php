@php
    $topics = [
        [
            'getting-started',
            'Erste Schritte',
            'Lernen Sie die Grundlagen kennen und starten Sie erfolgreich mit dem Bildungsportal.',
        ],
        [
            'finding-events',
            'Veranstaltungen finden',
            'Entdecken Sie passende Fortbildungen mit unseren Such- und Filterfunktionen.',
        ],
        [
            'booking-events',
            'Veranstaltungen buchen',
            'Schritt-für-Schritt Anleitung zum Buchen von Tickets und zur Zahlungsabwicklung.',
        ],
        [
            'manage-bookings',
            'Buchungen verwalten',
            'Verwalten Sie Ihre Buchungen, laden Sie Tickets herunter und stornieren Sie bei Bedarf.',
        ],
        [
            'profile-settings',
            'Profil & Einstellungen',
            'Passen Sie Ihr Profil an und verwalten Sie Ihre Kontoeinstellungen.',
        ],
        [
            'notifications',
            'Benachrichtigungen',
            'Verwalten Sie Ihre Benachrichtigungseinstellungen und bleiben Sie auf dem Laufenden.',
        ],
        [
            'social-features',
            'Netzwerk & Kontakte',
            'Vernetzen Sie sich mit anderen Pädagogen und erweitern Sie Ihr Netzwerk.',
        ],
        [
            'badges',
            'Badges & Erfolge',
            'Sammeln Sie Badges für Ihre Teilnahme und verfolgen Sie Ihren Lernfortschritt.',
        ],
        [
            'favorites',
            'Favoriten & Merkliste',
            'Speichern Sie interessante Veranstaltungen und greifen Sie später darauf zu.',
        ],
        ['reviews', 'Bewertungen schreiben', 'Teilen Sie Ihre Erfahrungen und helfen Sie anderen bei der Auswahl.'],
        [
            'privacy',
            'Datenschutz & Privatsphäre',
            'Erfahren Sie, wie Ihre Daten geschützt werden und welche Rechte Sie haben.',
        ],
        ['troubleshooting', 'Häufige Probleme', 'Finden Sie Lösungen für die häufigsten Fragen und Probleme.'],
    ];
@endphp
<x-layouts.app title="Hilfe">
    <x-ui.page-header title="Hilfe & Anleitungen"
        subtitle="Hier finden Sie alle Informationen, die Sie benötigen, um das Bildungsportal optimal zu nutzen. Durchsuchen Sie unsere Anleitungen oder wählen Sie ein Thema aus den Kategorien unten." />

    <x-ui.search-bar :action="route('help.search')" name="q" :value="request('q')" label="Suche"
        placeholder="Durchsuchen Sie die Hilfe-Artikel..." aria-label="Hilfe durchsuchen" class="max-w-[960px]" />

    <section aria-labelledby="themen" class="pt-[clamp(48px,6vw,80px)]">
        <h2 id="themen" class="m-0 mb-4 text-h2 text-ink">Themen</h2>
        <ul class="m-0 grid list-none grid-cols-[repeat(auto-fit,minmax(296px,1fr))] gap-x-8 p-0">
            @if (in_array($userType, ['organizer', 'admin']))
                <li>
                    <a href="{{ route('help.category', 'organizer') }}"
                        class="flex min-h-15 items-center justify-between gap-4 border-t border-line px-2 py-3 text-ink no-underline transition-colors duration-150 hover:bg-zone">
                        <span class="flex min-w-0 flex-col">
                            <span class="text-base font-medium break-words">Veranstalter-Hilfe</span>
                            <span class="text-small text-muted">Leitfäden für Organisationen: Veranstaltungen erstellen,
                                Buchungen verwalten, Marketing und mehr.</span>
                        </span>
                        <x-icon.arrow-right class="h-4 w-4 flex-none text-muted" />
                    </a>
                </li>
            @endif
            @foreach ($topics as [$slug, $title, $description])
                <li>
                    <a href="{{ route('help.article', ['category' => 'user', 'article' => $slug]) }}"
                        class="flex min-h-15 items-center justify-between gap-4 border-t border-line px-2 py-3 text-ink no-underline transition-colors duration-150 hover:bg-zone">
                        <span class="flex min-w-0 flex-col">
                            <span class="text-base font-medium break-words">{{ $title }}</span>
                            <span class="text-small text-muted">{{ $description }}</span>
                        </span>
                        <x-icon.arrow-right class="h-4 w-4 flex-none text-muted" />
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    <section aria-labelledby="weitere-hilfe" class="pt-[clamp(48px,6vw,80px)]">
        <x-ui.box tone="zone" padding="lg" class="flex flex-wrap items-center justify-between gap-x-12 gap-y-6">
            <div class="flex max-w-prose flex-col gap-2">
                <h2 id="weitere-hilfe" class="m-0 text-h2 text-ink">Benötigen Sie weitere Hilfe?</h2>
                <p class="m-0 text-base text-muted">Wenn Sie keine Antwort auf Ihre Frage gefunden haben, kontaktieren
                    Sie uns gerne direkt.</p>
            </div>
            <x-ui.button variant="secondary" href="mailto:support@bildungsportal.de">
                <x-slot:icon><x-icon.mail /></x-slot:icon>
                Support kontaktieren
            </x-ui.button>
        </x-ui.box>
    </section>
</x-layouts.app>
