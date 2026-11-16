<x-layouts.app title="Veranstalter-Dashboard">
    <x-ui.page-header title="Veranstalter-Dashboard" subtitle="Willkommen zurück, {{ auth()->user()->name }}.">
        <x-slot:actions>
            <x-ui.button :href="route('organizer.events.create')">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Veranstaltung anlegen
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @php
        $showPendingInvoices = $organization->hasExternalInvoicing() && $stats['pending_invoice_count'] > 0;
    @endphp

    <div @class([
        'mb-12 grid gap-4 sm:grid-cols-2',
        'lg:grid-cols-4' => !$showPendingInvoices,
        'lg:grid-cols-3 xl:grid-cols-5' => $showPendingInvoices,
    ])>
        <x-ui.stat label="Veranstaltungen gesamt" :value="$stats['total_events']" :href="route('organizer.events.index')" />
        <x-ui.stat label="Veröffentlicht" :value="$stats['published_events']" />
        <x-ui.stat label="Buchungen" :value="$stats['total_bookings']" :href="route('organizer.bookings.index')" />
        <x-ui.stat label="Umsatz" :value="\App\Support\Ui::money($stats['total_revenue'])" />
        {{-- Nicht-fakturierte Buchungen (nur bei externer Rechnungsstellung) --}}
        @if ($showPendingInvoices)
            <x-ui.stat label="Noch nicht fakturiert" :value="$stats['pending_invoice_count']"
                sub="kostenpflichtige Buchungen warten auf externe Rechnung – exportieren" :href="route('organizer.billing-data.index', ['filter' => 'pending'])" />
        @endif
    </div>

    @include('organizer.partials.todo')

    <x-ui.section title="Kommende Veranstaltungen" :link-href="route('organizer.events.index')" link-label="Alle Veranstaltungen" class="mb-12">
        @if ($upcomingEvents->isNotEmpty())
            <x-ui.row-list>
                @foreach ($upcomingEvents as $event)
                    <x-ui.event-row :event="$event" :href="route('organizer.events.edit', $event)">
                        <x-slot:aside>
                            <span
                                class="text-ui font-medium whitespace-nowrap">{{ trans_choice('{1} :count Buchung|[0,*] :count Buchungen', $event->bookings->count()) }}</span>
                            @if ($event->is_published)
                                <x-ui.status tone="success">Veröffentlicht</x-ui.status>
                            @else
                                <x-ui.status tone="neutral">Entwurf</x-ui.status>
                            @endif
                        </x-slot:aside>
                        <x-slot:action>
                            <x-ui.button variant="secondary" :href="route('organizer.events.edit', $event)" full>Bearbeiten</x-ui.button>
                            <x-ui.button variant="link" :href="route('organizer.bookings.index', ['event_id' => $event->id])">Buchungen</x-ui.button>
                        </x-slot:action>
                    </x-ui.event-row>
                @endforeach
            </x-ui.row-list>
        @else
            <x-ui.empty title="Keine kommenden Veranstaltungen" :action-href="route('organizer.events.create')"
                action-label="Erste Veranstaltung anlegen">
                Sobald Sie eine Veranstaltung veröffentlichen, erscheint sie hier.
            </x-ui.empty>
        @endif
    </x-ui.section>

    <x-ui.section title="Aktuelle Buchungen" :link-href="route('organizer.bookings.index')" link-label="Alle Buchungen" class="mb-12">
        @if ($recentBookings->isNotEmpty())
            <x-ui.row-list>
                @foreach ($recentBookings as $booking)
                    <x-ui.row>
                        <a href="{{ route('organizer.bookings.show', $booking) }}"
                            class="text-base font-semibold text-ink no-underline underline-offset-[3px] hover:underline">{{ $booking->customer_name }}</a>
                        <span class="text-small text-muted">{{ $booking->event->title }}</span>
                        <x-slot:meta>
                            <span
                                class="text-ui font-medium tabular-nums">{{ trans_choice('{1} :count Ticket|[0,*] :count Tickets', $booking->items->count()) }}</span>
                            <span class="text-small text-muted">{{ $booking->created_at->diffForHumans() }}</span>
                        </x-slot:meta>
                        <x-slot:aside>
                            <span
                                class="text-base font-semibold whitespace-nowrap">{{ \App\Support\Ui::money($booking->total) }}</span>
                            <x-ui.status :tone="\App\Support\Ui::bookingTone($booking->status)">{{ $booking->statusLabel() }}</x-ui.status>
                        </x-slot:aside>
                        <x-slot:action>
                            <x-ui.button variant="secondary" :href="route('organizer.bookings.show', $booking)" full>Buchung ansehen</x-ui.button>
                        </x-slot:action>
                    </x-ui.row>
                @endforeach
            </x-ui.row-list>
        @else
            <x-ui.empty>Keine Buchungen vorhanden.</x-ui.empty>
        @endif
    </x-ui.section>

    <nav aria-labelledby="schnellzugriff-heading" class="mb-12">
        <h2 id="schnellzugriff-heading" class="m-0 mb-6 text-h2">Schnellzugriff</h2>
        @php
            $quickLinks = [
                [
                    'Veranstaltung anlegen',
                    route('organizer.events.create'),
                    'Veranstaltung anlegen und veröffentlichen',
                ],
                ['Buchungen verwalten', route('organizer.bookings.index'), 'Buchungen ansehen und bearbeiten'],
                ['Daten exportieren', route('organizer.bookings.export'), 'Buchungsdaten als CSV'],
                [
                    'Hervorgehobene Veranstaltungen',
                    route('organizer.featured-events.history'),
                    'Anträge, Zahlungen und Verlängerungen',
                ],
                [
                    'Hilfe für Veranstalter',
                    route('help.category', 'organizer'),
                    'Erste Schritte, Veranstaltungen, Buchungen',
                ],
            ];
        @endphp
        <ul class="m-0 grid list-none grid-cols-1 gap-x-8 p-0 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($quickLinks as [$label, $url, $note])
                <li class="border-t border-line">
                    <a href="{{ $url }}"
                        class="flex min-h-15 items-center justify-between gap-3 py-3 text-ui font-medium text-ink no-underline underline-offset-[3px] hover:underline">
                        <span>
                            {{ $label }}
                            <span class="block text-small font-normal text-muted">{{ $note }}</span>
                        </span>
                        <x-icon.arrow-right class="h-4 w-4 shrink-0" />
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <nav aria-labelledby="hilfe-heading">
        <h2 id="hilfe-heading" class="m-0 mb-6 text-h2">Hilfe und Ressourcen</h2>
        @php
            $helpLinks = [
                ['getting-started', 'Erste Schritte als Veranstalter'],
                ['creating-events', 'Veranstaltungen erstellen und verwalten'],
                ['managing-bookings', 'Buchungen und Teilnehmende verwalten'],
                ['tickets-pricing', 'Tickets und Preisgestaltung'],
                ['analytics-reports', 'Statistiken und Berichte'],
                ['communication', 'Kommunikation mit Teilnehmenden'],
                ['marketing-promotion', 'Marketing und Werbung'],
                ['settings-preferences', 'Einstellungen und Präferenzen'],
                ['troubleshooting', 'Häufige Probleme und FAQ'],
            ];
        @endphp
        <ul class="m-0 grid list-none grid-cols-1 gap-x-8 p-0 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($helpLinks as [$slug, $label])
                <li class="border-t border-line">
                    <a href="{{ route('help.article', ['organizer', $slug]) }}"
                        class="flex min-h-11 items-center justify-between gap-3 py-2 text-ui font-medium text-ink no-underline underline-offset-[3px] hover:underline">
                        {{ $label }}
                        <x-icon.arrow-right class="h-4 w-4 shrink-0" />
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</x-layouts.app>
