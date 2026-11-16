<x-layouts.app title="Statistik">
    <x-ui.page-header title="Statistik" subtitle="Übersicht über Ihre Veranstaltungen und Buchungen" />

    <form method="GET" action="{{ route('organizer.statistics.index') }}"
        class="mb-8 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.field label="Von" name="start_date" type="date" :old="false" :value="$startDate" />
        <x-ui.field label="Bis" name="end_date" type="date" :old="false" :value="$endDate" />
        <div>
            <x-ui.button type="submit" variant="secondary" size="lg" full>Aktualisieren</x-ui.button>
        </div>
    </form>

    <div class="mb-12 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-ui.stat label="Veranstaltungen gesamt" :value="$totalEvents"
            sub="{{ $publishedEvents }} veröffentlicht · {{ $upcomingEvents }} kommend" />
        <x-ui.stat label="Buchungen gesamt" :value="$totalBookings"
            sub="{{ $confirmedBookings }} bestätigt · {{ $pendingBookings }} offen" />
        <x-ui.stat label="Umsatz gesamt" :value="\App\Support\Ui::money($totalRevenue)"
            sub="{{ \App\Support\Ui::money($confirmedRevenue) }} bestätigt" />
        <x-ui.stat label="Verkaufte Tickets" :value="$ticketsSold"
            sub="Ø {{ \App\Support\Ui::money($avgTicketPrice ?? 0) }} pro Ticket" />
    </div>

    <div class="mb-12 grid grid-cols-1 gap-x-12 gap-y-12 lg:grid-cols-2">
        <x-ui.section title="Konversionsrate">
            <x-ui.stat label="Bestätigte Buchungen von gesamt"
                value="{{ number_format((float) $conversionRate, 1, ',', '.') }} %" />
            <x-ui.progress :value="$conversionRate" label="Konversionsrate" class="mt-3" />
        </x-ui.section>

        <x-ui.section title="Zahlungsstatus">
            <x-ui.table label="Zahlungsstatus">
                <thead>
                    <tr>
                        <th scope="col">Status</th>
                        <th scope="col" class="num">Buchungen</th>
                        <th scope="col" class="num">Betrag</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paymentStats as $stat)
                        <tr>
                            <th scope="row">
                                <x-ui.status :tone="\App\Support\Ui::paymentTone($stat->payment_status)">
                                    {{ \App\Models\Booking::PAYMENT_STATUS_LABELS[$stat->payment_status] ?? ucfirst((string) $stat->payment_status) }}
                                </x-ui.status>
                            </th>
                            <td class="num">{{ $stat->count }}</td>
                            <td class="num">{{ \App\Support\Ui::money($stat->total) }}</td>
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
    </div>

    <x-ui.section title="Monatlicher Verlauf" class="mb-12">
        @php $maxMonthly = max(1, (int) $monthlyBookings->max('count')); @endphp
        <x-ui.table label="Monatlicher Verlauf">
            <thead>
                <tr>
                    <th scope="col">Monat</th>
                    <th scope="col" class="num">Buchungen</th>
                    <th scope="col" class="hidden w-1/3 md:table-cell"><span class="sr-only">Verlauf</span></th>
                    <th scope="col" class="num">Umsatz</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($monthlyBookings as $month)
                    <tr>
                        <th scope="row" class="whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($month->month . '-01')->translatedFormat('F Y') }}</th>
                        <td class="num">{{ $month->count }}</td>
                        <td class="hidden md:table-cell">
                            <x-ui.progress :value="$month->count" :max="$maxMonthly" decorative />
                        </td>
                        <td class="num">{{ \App\Support\Ui::money($month->revenue) }}</td>
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

    <div class="mb-12 grid grid-cols-1 gap-x-12 gap-y-12 lg:grid-cols-2">
        <x-ui.section title="Veranstaltungen nach Umsatz">
            <x-ui.table label="Veranstaltungen nach Umsatz">
                <thead>
                    <tr>
                        <th scope="col">Veranstaltung</th>
                        <th scope="col" class="num">Umsatz</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topEventsByRevenue->take(5) as $event)
                        <tr>
                            <th scope="row">
                                <a href="{{ route('organizer.statistics.event', $event) }}"
                                    class="text-ink underline underline-offset-[3px]">{{ $event->title }}</a>
                            </th>
                            <td class="num">{{ \App\Support\Ui::money($event->bookings_sum_total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr class="hover:bg-transparent">
                            <td colspan="2" class="p-0">
                                <x-ui.empty :bordered="false">Keine Daten verfügbar.</x-ui.empty>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.section>

        <x-ui.section title="Veranstaltungen nach Buchungen">
            <x-ui.table label="Veranstaltungen nach Buchungen">
                <thead>
                    <tr>
                        <th scope="col">Veranstaltung</th>
                        <th scope="col" class="num">Buchungen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topEventsByAttendees->take(5) as $event)
                        <tr>
                            <th scope="row">
                                <a href="{{ route('organizer.statistics.event', $event) }}"
                                    class="text-ink underline underline-offset-[3px]">{{ $event->title }}</a>
                            </th>
                            <td class="num">{{ $event->bookings_count }}</td>
                        </tr>
                    @empty
                        <tr class="hover:bg-transparent">
                            <td colspan="2" class="p-0">
                                <x-ui.empty :bordered="false">Keine Daten verfügbar.</x-ui.empty>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.section>
    </div>

    <x-ui.section title="Veranstaltungen nach Kategorie">
        @if ($categoryStats->isNotEmpty())
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($categoryStats as $stat)
                    <x-ui.stat :label="$stat->category->name ?? 'Unbekannt'" :value="$stat->count" />
                @endforeach
            </div>
        @else
            <x-ui.empty>Keine Daten verfügbar.</x-ui.empty>
        @endif
    </x-ui.section>
</x-layouts.app>
