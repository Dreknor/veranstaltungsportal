<x-layouts.app title="Statistik: {{ $event->title }}">
    <x-ui.page-header :title="'Statistik: ' . $event->title" subtitle="Detaillierte Auswertung für diese Veranstaltung" :back-href="route('organizer.statistics.index')"
        back-label="Zur Übersicht">
    </x-ui.page-header>

    <div class="mb-12 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-ui.stat label="Buchungen gesamt" :value="$totalBookings"
            sub="{{ $confirmedBookings }} bestätigt · {{ $pendingBookings }} offen" />
        <x-ui.stat label="Umsatz gesamt" :value="\App\Support\Ui::money($totalRevenue)"
            sub="{{ \App\Support\Ui::money($confirmedRevenue) }} bestätigt" />
        <x-ui.stat label="Verkaufte Tickets">
            {{ $ticketsSold }}
            @if ($event->max_attendees)
                <span class="mt-2 flex flex-col gap-1 text-small font-normal tracking-normal text-ink-soft">
                    <span class="flex justify-between gap-2"><span>Auslastung</span><span
                            class="tabular-nums">{{ number_format((float) $capacityPercentage, 1, ',', '.') }}
                            %</span></span>
                    <x-ui.progress :value="$capacityPercentage" decorative />
                </span>
            @endif
        </x-ui.stat>
        <x-ui.stat label="Check-in-Quote" value="{{ number_format((float) $checkInRate, 1, ',', '.') }} %"
            sub="{{ $checkedInCount }} von {{ $ticketsSold }} eingecheckt" />
    </div>

    <x-ui.section title="Verkäufe nach Ticketart" class="mb-12">
        <x-ui.table label="Verkäufe nach Ticketart">
            <thead>
                <tr>
                    <th scope="col">Ticketart</th>
                    <th scope="col" class="num">Verkauft</th>
                    <th scope="col" class="num">Umsatz</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ticketTypeStats as $stat)
                    <tr>
                        <th scope="row">{{ $stat->name }}</th>
                        <td class="num">{{ $stat->quantity_sold }}</td>
                        <td class="num">{{ \App\Support\Ui::money($stat->revenue) }}</td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="3" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten verfügbar.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>

    <x-ui.section title="Buchungen pro Tag" class="mb-12">
        @php $maxDaily = max(1, (int) $dailyBookings->max('count')); @endphp
        <x-ui.table label="Buchungen pro Tag">
            <thead>
                <tr>
                    <th scope="col">Datum</th>
                    <th scope="col" class="num">Buchungen</th>
                    <th scope="col" class="hidden w-1/3 md:table-cell"><span class="sr-only">Verlauf</span></th>
                    <th scope="col" class="num">Umsatz</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dailyBookings as $day)
                    <tr>
                        <th scope="row" class="tabular-nums whitespace-nowrap">
                            {{ \App\Support\Ui::date(\Carbon\Carbon::parse($day->date)) }}</th>
                        <td class="num">{{ $day->count }}</td>
                        <td class="hidden md:table-cell">
                            <x-ui.progress :value="$day->count" :max="$maxDaily" decorative />
                        </td>
                        <td class="num">{{ \App\Support\Ui::money($day->revenue) }}</td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="4" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten verfügbar.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>

    @if ($discountCodeStats->isNotEmpty())
        <x-ui.section title="Nutzung der Rabattcodes" class="mb-12">
            <x-ui.table label="Nutzung der Rabattcodes">
                <thead>
                    <tr>
                        <th scope="col">Code</th>
                        <th scope="col" class="num">Verwendungen</th>
                        <th scope="col" class="num">Rabatt gesamt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($discountCodeStats as $stat)
                        <tr>
                            <th scope="row" class="tabular-nums">{{ $stat->discountCode->code ?? 'N/A' }}</th>
                            <td class="num">{{ $stat->usage_count }}</td>
                            <td class="num">−{{ \App\Support\Ui::money($stat->total_discount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        </x-ui.section>
    @endif

    <x-ui.section title="Export und Aktionen">
        <div class="flex flex-wrap gap-3">
            <x-ui.button variant="secondary" :href="route('organizer.bookings.export', ['event_id' => $event->id])">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                Teilnehmerliste exportieren (CSV)
            </x-ui.button>
            <x-ui.button variant="secondary" :href="route('organizer.bookings.index', ['event_id' => $event->id])">Alle Buchungen anzeigen</x-ui.button>
            <x-ui.button variant="secondary" :href="route('organizer.events.edit', $event)">Veranstaltung bearbeiten</x-ui.button>
        </div>
    </x-ui.section>
</x-layouts.app>
