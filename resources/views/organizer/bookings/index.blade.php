<x-layouts.app title="Buchungen">
    <x-ui.page-header title="Buchungen">
        <x-slot:meta>
            @if ($groupByEvent)
                {{ $isArchive ? 'Archiv – abgeschlossene Veranstaltungen und deren Buchungen' : 'Übersicht aller Buchungen, nach Veranstaltung gruppiert' }}
            @else
                Gefilterte Buchungsliste
            @endif
        </x-slot:meta>
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('organizer.bookings.export', array_merge(request()->all(), ['format' => 'csv']))">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                CSV-Export
            </x-ui.button>
            <x-ui.button variant="secondary" :href="route('organizer.bookings.export', array_merge(request()->all(), ['format' => 'excel']))">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                Excel-Export
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Archiv-Reiter --}}
    <x-ui.tabs label="Buchungen" class="mb-6">
        <x-ui.tab :href="route('organizer.bookings.index')" :active="!$isArchive" :count="$upcomingBookingsCount > 0 ? $upcomingBookingsCount : null">Aktuelle Buchungen</x-ui.tab>
        <x-ui.tab :href="route('organizer.bookings.index', ['archive' => 1])" :active="$isArchive" :count="$archiveBookingsCount > 0 ? $archiveBookingsCount : null">Buchungsarchiv</x-ui.tab>
    </x-ui.tabs>

    @if ($isArchive)
        <x-ui.alert tone="info" class="mb-6">
            Sie sehen das Buchungsarchiv – Buchungen zu abgeschlossenen Veranstaltungen der Vergangenheit.
        </x-ui.alert>
    @endif

    {{-- Handlungsbedarf --}}
    @if (!$isArchive && ($pendingApprovalCount > 0 || $unpaidCount > 0))
        <div class="mb-6 flex flex-col gap-3" aria-label="Handlungsbedarf">
            @if ($pendingApprovalCount > 0)
                <x-ui.alert tone="warning">
                    <a href="{{ route('organizer.bookings.index', ['status' => 'pending_approval']) }}" class="font-medium">{{ $pendingApprovalCount }} Anmeldung(en) warten auf Freigabe</a>
                </x-ui.alert>
            @endif
            @if ($unpaidCount > 0)
                <x-ui.alert tone="warning">
                    <a href="{{ route('organizer.bookings.index', ['status' => 'pending', 'payment_status' => 'pending']) }}" class="font-medium">{{ $unpaidCount }} Buchung(en) mit offener Zahlung</a>
                </x-ui.alert>
            @endif
        </div>
    @endif

    {{-- Filter --}}
    <form action="{{ route('organizer.bookings.index') }}" method="GET" class="mb-8">
        {{-- Archiv-Status im Filter erhalten --}}
        @if ($isArchive)
            <input type="hidden" name="archive" value="1">
        @endif
        <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
            <x-ui.field label="Suche" name="search" :value="request('search')" placeholder="Buchungsnummer, Name, E-Mail" :old="false" />
            <x-ui.select label="Veranstaltung" name="event_id" :old="false">
                <option value="">Alle Veranstaltungen</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}" @selected(request('event_id') == $event->id)>{{ $event->title }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select label="Zahlungsstatus" name="payment_status" :old="false">
                <option value="">Alle</option>
                <option value="pending" @selected(request('payment_status') === 'pending')>Ausstehend</option>
                <option value="paid" @selected(request('payment_status') === 'paid')>Bezahlt</option>
                @if ($organization->hasExternalInvoicing())
                    <option value="extern" @selected(request('payment_status') === 'extern')>Extern fakturiert</option>
                @endif
                <option value="failed" @selected(request('payment_status') === 'failed')>Fehlgeschlagen</option>
                <option value="refunded" @selected(request('payment_status') === 'refunded')>Erstattet</option>
            </x-ui.select>
            <x-ui.select label="Buchungsstatus" name="status" :old="false">
                <option value="">Alle</option>
                <option value="pending" @selected(request('status') === 'pending')>Zahlung ausstehend</option>
                <option value="pending_approval" @selected(request('status') === 'pending_approval')>Wartet auf Freigabe</option>
                <option value="confirmed" @selected(request('status') === 'confirmed')>Bestätigt</option>
                <option value="completed" @selected(request('status') === 'completed')>Abgeschlossen</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Storniert</option>
            </x-ui.select>
        </div>
        <div class="mt-5 flex flex-wrap gap-3">
            <x-ui.button type="submit" size="lg">Filtern</x-ui.button>
            <x-ui.button variant="secondary" size="lg" :href="route('organizer.bookings.index', $isArchive ? ['archive' => 1] : [])">Zurücksetzen</x-ui.button>
        </div>
    </form>

    {{-- ================================================================
         MODUS 1: Gruppierte Ansicht (kein aktiver Filter)
         ================================================================ --}}
    @if ($groupByEvent)
        <div class="flex flex-col gap-4">
            @forelse ($groupedEvents as $groupedEvent)
                <div class="overflow-hidden rounded-box border border-line bg-surface" x-data="{ open: false }">
                    {{-- Veranstaltungskopf --}}
                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-expanded="false"
                                class="focus-inset flex min-w-0 flex-1 cursor-pointer items-center justify-between gap-4 bg-transparent px-4 py-4 text-left text-ink transition-colors duration-150 hover:bg-zone-hover sm:px-6">
                            <span class="min-w-0">
                                <span class="block truncate text-h3">{{ $groupedEvent->title }}</span>
                                <span class="block text-small text-muted tabular-nums">
                                    {{ $groupedEvent->start_date->format('d.m.Y') }}
                                    @if ($groupedEvent->location)
                                        – {{ $groupedEvent->location }}
                                    @endif
                                </span>
                            </span>
                            <span class="flex shrink-0 flex-wrap items-center justify-end gap-x-4 gap-y-1">
                                <span class="text-small font-medium tabular-nums">{{ $groupedEvent->bookings_count }} Buchung(en)</span>
                                @if ($groupedEvent->confirmed_bookings_count > 0)
                                    <x-ui.status tone="success" class="tabular-nums">{{ $groupedEvent->confirmed_bookings_count }} bestätigt</x-ui.status>
                                @endif
                                @if ($groupedEvent->pending_bookings_count > 0)
                                    <x-ui.status tone="warning" class="tabular-nums">{{ $groupedEvent->pending_bookings_count }} offen</x-ui.status>
                                @endif
                                <x-icon.chevron-down class="h-5 w-5 text-muted transition-transform duration-150" x-bind:class="open && 'rotate-180'" />
                            </span>
                        </button>
                        <a href="{{ route('organizer.bookings.index', array_filter(['event_id' => $groupedEvent->id, 'archive' => $isArchive ? 1 : null])) }}"
                           class="inline-flex min-h-11 items-center px-4 pb-3 text-small font-medium whitespace-nowrap text-ink underline underline-offset-[3px] sm:px-6 sm:pb-0">
                            Nur diese Veranstaltung
                        </a>
                    </div>

                    {{-- Buchungen (aufgeklappt) --}}
                    <div x-show="open" x-cloak class="border-t border-line">
                        <ul class="m-0 list-none divide-y divide-line p-0 md:hidden">
                            @foreach ($groupedEvent->bookings as $booking)
                                @include('organizer.bookings._card', ['booking' => $booking, 'organization' => $organization])
                            @endforeach
                        </ul>
                        <div class="hidden md:block">
                            <x-ui.table label="Buchungen zu {{ $groupedEvent->title }}" class="rounded-none border-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Buchungsnr.</th>
                                        <th scope="col">Kunde</th>
                                        <th scope="col" class="num">Tickets</th>
                                        <th scope="col" class="num">Betrag</th>
                                        <th scope="col">Status</th>
                                        @if ($organization->hasExternalInvoicing())
                                            <th scope="col">Fakturiert</th>
                                        @endif
                                        <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($groupedEvent->bookings as $booking)
                                        @include('organizer.bookings._row', ['booking' => $booking, 'organization' => $organization])
                                    @empty
                                        <tr class="hover:bg-transparent">
                                            <td colspan="7" class="p-0">
                                                <x-ui.empty :bordered="false">Keine Buchungen vorhanden.</x-ui.empty>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </x-ui.table>
                        </div>
                    </div>
                </div>
            @empty
                <x-ui.empty title="Noch keine Buchungen">Sobald sich jemand anmeldet, erscheint die Buchung hier.</x-ui.empty>
            @endforelse
        </div>

        @if (isset($groupedEvents) && $groupedEvents->hasPages())
            <div class="mt-6">
                {{ $groupedEvents->appends(request()->query())->links() }}
            </div>
        @endif

    {{-- ================================================================
         MODUS 2: Gefilterte Flachliste
         ================================================================ --}}
    @else
        <ul class="m-0 list-none divide-y divide-line overflow-hidden rounded-box border border-line bg-surface p-0 md:hidden">
            @forelse ($bookings as $booking)
                @include('organizer.bookings._card', ['booking' => $booking, 'organization' => $organization, 'showEvent' => true])
            @empty
                <li class="p-6 text-base text-muted">Keine Buchungen gefunden.</li>
            @endforelse
        </ul>
        <div class="hidden md:block">
            <x-ui.table label="Gefilterte Buchungen">
                <thead>
                    <tr>
                        <th scope="col">Buchungsnr.</th>
                        <th scope="col">Veranstaltung</th>
                        <th scope="col">Kunde</th>
                        <th scope="col" class="num">Tickets</th>
                        <th scope="col" class="num">Betrag</th>
                        <th scope="col">Status</th>
                        @if ($organization->hasExternalInvoicing())
                            <th scope="col">Fakturiert</th>
                        @endif
                        <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        @include('organizer.bookings._row', ['booking' => $booking, 'organization' => $organization, 'showEvent' => true])
                    @empty
                        <tr class="hover:bg-transparent">
                            <td colspan="8" class="p-0">
                                <x-ui.empty :bordered="false">Keine Buchungen gefunden.</x-ui.empty>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </div>

        @if (isset($bookings) && $bookings->hasPages())
            <div class="mt-6">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        @endif
    @endif
</x-layouts.app>
