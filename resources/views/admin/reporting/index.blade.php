<x-layouts.app title="Auswertungen" :header="false">
    <x-ui.page-header title="Reporting und Auswertungen" subtitle="Übersicht über System-Statistiken und Reports" />

    <form method="GET" class="mb-8 flex flex-wrap items-end gap-x-4 gap-y-3">
        <x-ui.select label="Zeitraum" name="period" :old="false" onchange="this.form.submit()"
            class="w-full max-w-64">
            <option value="7days" @selected($period === '7days')>Letzte 7 Tage</option>
            <option value="30days" @selected($period === '30days')>Letzte 30 Tage</option>
            <option value="90days" @selected($period === '90days')>Letzte 90 Tage</option>
            <option value="365days" @selected($period === '365days')>Letztes Jahr</option>
            <option value="ytd" @selected($period === 'ytd')>Jahr bis heute</option>
            <option value="all" @selected($period === 'all')>Gesamt</option>
        </x-ui.select>
        <x-ui.button type="submit" variant="secondary" size="lg">Anzeigen</x-ui.button>
    </form>

    <div class="mb-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Benutzer (gesamt)" :value="number_format($metrics['total_users'])" :sub="'+' . number_format($metrics['new_users']) . ' neu'" />
        <x-ui.stat label="Veranstaltungen (gesamt)" :value="number_format($metrics['total_events'])" :sub="'+' . number_format($metrics['new_events']) . ' neu'" />
        <x-ui.stat label="Buchungen (gesamt)" :value="number_format($metrics['total_bookings'])" :sub="'+' . number_format($metrics['new_bookings']) . ' neu'" />
        <x-ui.stat label="Umsatz (gesamt)" :value="\App\Support\Ui::money($metrics['total_revenue'])" :sub="'+' . \App\Support\Ui::money($metrics['period_revenue']) . ' im Zeitraum'" />
    </div>

    <x-ui.section title="Konversions-Trichter" description="Buchungen im gewählten Zeitraum" class="mb-12">
        <ul class="m-0 flex list-none flex-col gap-5 p-0">
            <li>
                <div class="mb-2 flex justify-between gap-4 text-small">
                    <span class="font-medium">Aufrufe der Veranstaltungen</span>
                    <span
                        class="text-muted tabular-nums">{{ number_format($conversionFunnel['event_views'], 0, ',', '.') }}</span>
                </div>
                <x-ui.progress :value="100" decorative />
            </li>
            <li>
                <div class="mb-2 flex justify-between gap-4 text-small">
                    <span class="font-medium">Buchungen</span>
                    <span
                        class="text-muted tabular-nums">{{ number_format($conversionFunnel['bookings_started'], 0, ',', '.') }}
                        ({{ number_format($conversionFunnel['start_rate'], 1, ',', '.') }} %)</span>
                </div>
                <x-ui.progress :value="$conversionFunnel['start_rate']" decorative />
            </li>
            <li>
                <div class="mb-2 flex justify-between gap-4 text-small">
                    <span class="font-medium">Bezahlte Buchungen</span>
                    <span
                        class="text-muted tabular-nums">{{ number_format($conversionFunnel['bookings_completed'], 0, ',', '.') }}
                        ({{ number_format($conversionFunnel['completion_rate'], 1, ',', '.') }} %)</span>
                </div>
                <x-ui.progress :value="$conversionFunnel['completion_rate']" decorative />
            </li>
        </ul>
    </x-ui.section>

    <div class="mb-12 grid gap-4 md:grid-cols-3">
        <x-ui.stat label="Report" value="Benutzer" sub="Detaillierte Benutzerstatistiken" :href="route('admin.reporting.users') . '?period=' . $period" />
        <x-ui.stat label="Report" value="Veranstaltungen" sub="Performance und Trends" :href="route('admin.reporting.events') . '?period=' . $period" />
        <x-ui.stat label="Report" value="Umsatz" sub="Finanzielle Übersicht" :href="route('admin.reporting.revenue') . '?period=' . $period" />
    </div>

    <x-ui.section title="Top 10 Veranstaltungen (nach Umsatz)" class="mb-12">
        <x-ui.table label="Top 10 Veranstaltungen nach Umsatz">
            <thead>
                <tr>
                    <th scope="col">Veranstaltung</th>
                    <th scope="col" class="max-sm:hidden num">Buchungen</th>
                    <th scope="col" class="num">Umsatz</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topEvents as $event)
                    <tr>
                        <th scope="row">
                            <a href="{{ route('events.show', $event->slug) }}"
                                class="text-ink underline underline-offset-[3px]">{{ $event->title }}</a>
                        </th>
                        <td class="max-sm:hidden num">{{ $event->bookings_count }}</td>
                        <td class="num">{{ \App\Support\Ui::money($event->bookings_sum_total ?? 0) }}</td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="3" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten verfügbar</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>

    <x-ui.section title="Top 10 Veranstalter (nach Umsatz)">
        <x-ui.table label="Top 10 Veranstalter nach Umsatz">
            <thead>
                <tr>
                    <th scope="col">Veranstalter</th>
                    <th scope="col" class="num">Veranstaltungen</th>
                    <th scope="col" class="num">Umsatz</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topOrganizers as $organizer)
                    <tr>
                        <th scope="row">{{ $organizer->name }}</th>
                        <td class="num">{{ $organizer->events_count }}</td>
                        <td class="num">{{ \App\Support\Ui::money($organizer->events_bookings_sum_total ?? 0) }}
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="3" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten verfügbar</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>
</x-layouts.app>
