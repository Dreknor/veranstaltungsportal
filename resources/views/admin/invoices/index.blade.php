<x-layouts.app title="Rechnungsverwaltung">
    <x-ui.page-header title="Rechnungsverwaltung">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.invoices.export', request()->query())">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                CSV exportieren
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Statistics -->
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Rechnungen gesamt" :value="$stats['total_count']" :sub="\App\Support\Ui::money($stats['total_amount'])" />
        <x-ui.stat label="Plattformgebühren" :value="\App\Support\Ui::money($stats['platform_fee_total'])" />
        <x-ui.stat label="Bezahlt" :value="$stats['paid_count']" :sub="\App\Support\Ui::money($stats['paid_amount'])" />
        <x-ui.stat label="Ausstehend" :value="$stats['pending_count']" :sub="\App\Support\Ui::money($stats['pending_amount'])" />
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.invoices.index') }}"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')"
            placeholder="Nummer, Name, E-Mail" />
        <x-ui.select label="Typ" name="type" :old="false" :selected="request('type')" :options="['' => 'Alle', 'platform_fee' => 'Plattformgebühr', 'participant' => 'Teilnehmende']" />
        <x-ui.select label="Status" name="status" :old="false" :selected="request('status')" :options="['' => 'Alle', 'sent' => 'Versendet', 'paid' => 'Bezahlt', 'overdue' => 'Überfällig']" />
        <x-ui.field label="Von Datum" name="from_date" type="date" :old="false" :value="request('from_date')" />
        <x-ui.field label="Bis Datum" name="to_date" type="date" :old="false" :value="request('to_date')" />
        <div>
            <x-ui.button type="submit" variant="secondary" size="lg" full>Filtern</x-ui.button>
        </div>
    </form>

    <x-ui.table label="Rechnungen">
        <thead>
            <tr>
                <th scope="col">Rechnung</th>
                <th scope="col" class="max-sm:hidden">Typ</th>
                <th scope="col" class="max-sm:hidden">Datum</th>
                <th scope="col" class="max-sm:hidden">Empfänger</th>
                <th scope="col" class="max-sm:hidden">Veranstaltung</th>
                <th scope="col" class="max-sm:hidden num">Betrag</th>
                <th scope="col">Status</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $invoice)
                <tr>
                    <th scope="row" class="whitespace-nowrap tabular-nums">
                        <a href="{{ route('admin.invoices.show', $invoice) }}"
                            class="text-ink underline underline-offset-[3px]">{{ $invoice->invoice_number }}</a>
                    </th>
                    <td class="max-sm:hidden">{{ $invoice->type === 'platform_fee' ? 'Plattformgebühr' : 'Teilnehmende' }}</td>
                    <td class="max-sm:hidden whitespace-nowrap tabular-nums">
                        {{ $invoice->invoice_date->format('d.m.Y') }}
                        <span class="block text-small text-muted">Fällig:
                            {{ $invoice->due_date->format('d.m.Y') }}</span>
                    </td>
                    <td class="max-sm:hidden">
                        {{ $invoice->recipient_name }}
                        <span class="block text-small text-muted">{{ $invoice->recipient_email }}</span>
                    </td>
                    <td class="max-sm:hidden">
                        @if ($invoice->event)
                            <a href="{{ route('events.show', $invoice->event->slug) }}"
                                class="text-ink underline underline-offset-[3px]">{{ Str::limit($invoice->event->title, 30) }}</a>
                        @else
                            –
                        @endif
                    </td>
                    <td class="max-sm:hidden num">{{ \App\Support\Ui::money($invoice->total_amount) }}</td>
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
                        <x-ui.dropdown label="Weitere Aktionen" icon-only>
                            <x-ui.dropdown-item :href="route('admin.invoices.show', $invoice)">Ansehen</x-ui.dropdown-item>
                            <x-ui.dropdown-item :href="route('admin.invoices.download', $invoice)">PDF herunterladen</x-ui.dropdown-item>
                            @if ($invoice->status !== 'paid')
                                <x-ui.dropdown-item type="submit" :form="'mark-paid-' . $invoice->id">Als bezahlt
                                    markieren</x-ui.dropdown-item>
                            @endif
                        </x-ui.dropdown>
                        @if ($invoice->status !== 'paid')
                            <form id="mark-paid-{{ $invoice->id }}" method="POST"
                                action="{{ route('admin.invoices.mark-paid', $invoice) }}" class="hidden">
                                @csrf
                                @method('PATCH')
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="8" class="p-0">
                        <x-ui.empty :bordered="false" title="Keine Rechnungen vorhanden">
                            Zu diesem Filter gibt es keine Rechnungen.
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
