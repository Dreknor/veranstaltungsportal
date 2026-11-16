<x-layouts.app title="Veranstaltungs-Report" :header="false">
    <x-ui.page-header title="Veranstaltungs-Report" subtitle="Performance und Trends der Veranstaltungen"
        :back-href="route('admin.reporting.index', ['period' => $period])" back-label="Zurück zur Übersicht" />

    <form method="GET" class="mb-8 flex flex-wrap items-end gap-x-4 gap-y-3">
        <x-ui.select label="Zeitraum" name="period" :old="false" onchange="this.form.submit()" class="w-full max-w-64">
            <option value="7days" @selected($period === '7days')>Letzte 7 Tage</option>
            <option value="30days" @selected($period === '30days')>Letzte 30 Tage</option>
            <option value="90days" @selected($period === '90days')>Letzte 90 Tage</option>
            <option value="365days" @selected($period === '365days')>Letztes Jahr</option>
            <option value="all" @selected($period === 'all')>Gesamt</option>
        </x-ui.select>
        <x-ui.button type="submit" variant="secondary" size="lg">Anzeigen</x-ui.button>
    </form>

    <div class="mb-12 grid gap-4 md:grid-cols-3">
        <x-ui.box padding="md">
            <h2 class="m-0 mb-3 text-small font-normal text-muted">Veranstaltungen nach Typ</h2>
            <dl class="m-0 flex flex-col gap-1">
                @foreach ($data['events_by_type'] as $type)
                    <div class="flex justify-between gap-4">
                        <dt>{{ ucfirst($type->event_type) }}</dt>
                        <dd class="m-0 font-semibold tabular-nums">{{ $type->count }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.box>

        <x-ui.box padding="md">
            <h2 class="m-0 mb-3 text-small font-normal text-muted">Status</h2>
            <dl class="m-0 flex flex-col gap-1">
                @foreach ($data['published_vs_draft'] as $status)
                    <div class="flex justify-between gap-4">
                        <dt>{{ $status->is_published ? 'Veröffentlicht' : 'Entwurf' }}</dt>
                        <dd class="m-0 font-semibold tabular-nums">{{ $status->count }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.box>

        <x-ui.stat label="Durchschnittliche Kapazität" :value="number_format($data['average_capacity'], 0)" sub="Teilnehmer pro Veranstaltung" class="p-6" />
    </div>

    <x-ui.section title="Veranstaltungen nach Kategorie">
        <x-ui.table label="Veranstaltungen nach Kategorie">
            <thead>
                <tr>
                    <th scope="col">Kategorie</th>
                    <th scope="col" class="num">Anzahl Veranstaltungen</th>
                    <th scope="col" class="num">Umsatz</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['events_by_category'] as $category)
                    <tr>
                        <th scope="row">{{ $category->name }}</th>
                        <td class="num">{{ $category->events_count }}</td>
                        <td class="num">{{ \App\Support\Ui::money($category->bookings_sum_total_amount ?? 0) }}</td>
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
