<x-layouts.app :title="'Plattformgebühren - ' . config('app.name')" :header="false">
    <x-ui.page-header title="Plattformgebühren im Überblick"
        subtitle="Aufschlüsselung der Plattformgebühren für Ihre Veranstaltungen."
        :back-href="route('organizer.invoices.index')" back-label="Zu den Rechnungen" />

    <div class="mb-8 grid gap-4 sm:grid-cols-2">
        <x-ui.stat label="Gesamter Buchungsumsatz" :value="\App\Support\Ui::money($totalBookings)" sub="Summe aller bezahlten Buchungen" />
        <x-ui.stat label="Gesamte Plattformgebühren" :value="\App\Support\Ui::money($totalFees)" :sub="$platformFees->count() . ' Transaktionen'" />
    </div>

    <form method="GET" action="{{ route('organizer.invoices.platform-fees') }}"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.select label="Veranstaltung" name="event_id" :old="false" placeholder="Alle Veranstaltungen"
            :options="$events->pluck('title', 'id')" :selected="request('event_id')" />
        <x-ui.field label="Von Datum" name="from_date" type="date" :old="false" :value="request('from_date')" />
        <x-ui.field label="Bis Datum" name="to_date" type="date" :old="false" :value="request('to_date')" />
        <div>
            <x-ui.button type="submit" variant="secondary" size="lg" full>Filtern</x-ui.button>
        </div>
    </form>

    <x-ui.table label="Plattformgebühren">
        <thead>
            <tr>
                <th scope="col">Veranstaltung</th>
                <th scope="col">Datum der Veranstaltung</th>
                <th scope="col" class="num">Buchungsumsatz</th>
                <th scope="col" class="num">Gebührensatz</th>
                <th scope="col" class="num">Plattformgebühr</th>
                <th scope="col">Berechnet am</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($platformFees as $fee)
                <tr>
                    <th scope="row">
                        <a href="{{ route('organizer.events.edit', $fee->event) }}" class="text-ink underline underline-offset-[3px]">{{ $fee->event->title }}</a>
                    </th>
                    <td class="tabular-nums whitespace-nowrap">{{ $fee->event->start_date->format('d.m.Y') }}</td>
                    <td class="num">{{ \App\Support\Ui::money($fee->booking_amount) }}</td>
                    <td class="num">{{ $fee->fee_percentage }} %</td>
                    <td class="num">{{ \App\Support\Ui::money($fee->fee_amount) }}</td>
                    <td class="tabular-nums whitespace-nowrap">{{ $fee->created_at->format('d.m.Y H:i') }}</td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="6" class="p-0">
                        <x-ui.empty :bordered="false" title="Keine Plattformgebühren vorhanden">
                            Gebühren werden automatisch nach Veranstaltungsende berechnet.
                        </x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($platformFees->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="2">Gesamt</td>
                    <td class="num">{{ \App\Support\Ui::money($totalBookings) }}</td>
                    <td class="num">–</td>
                    <td class="num">{{ \App\Support\Ui::money($totalFees) }}</td>
                    <td>–</td>
                </tr>
            </tfoot>
        @endif
    </x-ui.table>

    @if ($platformFees->hasPages())
        <div class="mt-6">
            {{ $platformFees->links() }}
        </div>
    @endif

    <x-ui.alert tone="info" class="mt-8" title="Hinweise zu Plattformgebühren">
        <ul class="m-0 list-disc pl-5">
            <li>Plattformgebühren werden automatisch nach Veranstaltungsende berechnet.</li>
            <li>Der Gebührensatz kann für Ihre Organisation individuell angepasst sein.</li>
            <li>Rechnungen werden separat erstellt und per E-Mail versandt.</li>
            <li>Bei Fragen zu Gebühren wenden Sie sich bitte an den Support.</li>
        </ul>
    </x-ui.alert>
</x-layouts.app>
