<x-layouts.app :title="'Rechnungen - ' . config('app.name')" :header="false">
    <x-ui.page-header title="Rechnungen" subtitle="Rechnungen über Plattformgebühren für Ihre Veranstaltungen.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('organizer.invoices.platform-fees')">Plattformgebühren im Überblick</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Gesamt" :value="\App\Support\Ui::money($totals['total'])" />
        <x-ui.stat label="Bezahlt" :value="\App\Support\Ui::money($totals['paid'])" />
        <x-ui.stat label="Offen" :value="\App\Support\Ui::money($totals['pending'])" />
    </div>

    <form method="GET" action="{{ route('organizer.invoices.index') }}"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.select label="Status" name="status" :old="false"
            :options="['' => 'Alle', 'sent' => 'Versendet', 'paid' => 'Bezahlt', 'overdue' => 'Überfällig']"
            :selected="request('status')" />
        <x-ui.field label="Von Datum" name="from_date" type="date" :old="false" :value="request('from_date')" />
        <x-ui.field label="Bis Datum" name="to_date" type="date" :old="false" :value="request('to_date')" />
        <div>
            <x-ui.button type="submit" variant="secondary" size="lg" full>Filtern</x-ui.button>
        </div>
    </form>

    <p class="mb-4 mt-0">
        <x-ui.button variant="link" :href="route('organizer.invoices.export', ['format' => 'csv'])">
            <x-slot:icon><x-icon.download /></x-slot:icon>
            Als CSV exportieren
        </x-ui.button>
    </p>

    <x-ui.table label="Rechnungen">
        <thead>
            <tr>
                <th scope="col">Rechnungsnummer</th>
                <th scope="col">Datum</th>
                <th scope="col">Veranstaltung</th>
                <th scope="col" class="num">Betrag</th>
                <th scope="col">Fällig am</th>
                <th scope="col">Status</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $invoice)
                <tr>
                    <th scope="row" class="tabular-nums">
                        <a href="{{ route('organizer.invoices.show', $invoice) }}" class="text-ink underline underline-offset-[3px]">{{ $invoice->invoice_number }}</a>
                    </th>
                    <td class="tabular-nums whitespace-nowrap">{{ $invoice->invoice_date->format('d.m.Y') }}</td>
                    <td>{{ $invoice->event->title ?? '–' }}</td>
                    <td class="num">{{ \App\Support\Ui::money($invoice->total_amount) }}</td>
                    <td class="tabular-nums whitespace-nowrap">
                        {{ $invoice->due_date->format('d.m.Y') }}
                        @if ($invoice->due_date->isPast() && $invoice->status !== 'paid')
                            <span class="block text-small text-danger">überfällig</span>
                        @endif
                    </td>
                    <td>
                        @if ($invoice->status === 'paid')
                            <x-ui.status tone="success">Bezahlt</x-ui.status>
                        @elseif ($invoice->status === 'overdue')
                            <x-ui.status tone="danger">Überfällig</x-ui.status>
                        @else
                            <x-ui.status tone="warning">Offen</x-ui.status>
                        @endif
                    </td>
                    <td class="actions">
                        <x-ui.button size="sm" variant="secondary" :href="route('organizer.invoices.show', $invoice)">Ansehen</x-ui.button>
                        <x-ui.button size="sm" variant="secondary" :href="route('organizer.invoices.download', $invoice)">PDF</x-ui.button>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="7" class="p-0">
                        <x-ui.empty :bordered="false" title="Keine Rechnungen vorhanden">
                            Rechnungen werden automatisch nach Veranstaltungsende erstellt.
                        </x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if ($invoices->hasPages())
        <div class="mt-6">
            {{ $invoices->links() }}
        </div>
    @endif
</x-layouts.app>
