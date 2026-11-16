@php
    $topics = [
        [
            'getting-started',
            'Erste Schritte als Veranstalter',
            'Organisation anlegen, Einstellungen, Team und erste Veranstaltung.',
        ],
        [
            'creating-events',
            'Veranstaltungen erstellen & verwalten',
            'Datenpflege, Online/Hybrid, Veröffentlichung und Medien.',
        ],
        ['managing-bookings', 'Buchungen & Teilnehmer verwalten', 'Zahlungen, Check-In, Export und Kommunikation.'],
        ['tickets-pricing', 'Tickets & Preisgestaltung', 'Ticketarten, Preise, Rabatte und Kapazitäten.'],
        ['analytics-reports', 'Statistiken & Berichte', 'Kennzahlen, Trends und Exporte.'],
        ['communication', 'Kommunikation mit Teilnehmern', 'E-Mails, Erinnerungen und Datenschutz.'],
        ['marketing-promotion', 'Marketing & Promotion', 'Featured, Social Sharing und Sichtbarkeit.'],
        ['settings-preferences', 'Einstellungen & Präferenzen', 'Organisation, Rechnung, Team & Profil.'],
        ['troubleshooting', 'Häufige Probleme & FAQ', 'Typische Fehler und Lösungen.'],
    ];
@endphp
<x-layouts.app title="Hilfe für Veranstalter">
    <x-ui.page-header title="Hilfe für Veranstalter"
        subtitle="Leitfäden und Best Practices für Organisationen und Veranstalter" :back-href="route('help.index')"
        back-label="Zur Hilfe-Übersicht" />

    <ul class="m-0 grid list-none grid-cols-[repeat(auto-fit,minmax(296px,1fr))] gap-x-8 p-0">
        @foreach ($topics as [$slug, $title, $description])
            <li>
                <a href="{{ route('help.article', ['organizer', $slug]) }}"
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
</x-layouts.app>
