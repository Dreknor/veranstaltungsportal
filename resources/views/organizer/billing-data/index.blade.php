<x-layouts.app title="Rechnungsdaten Export">
    <x-ui.page-header title="Rechnungsdaten" subtitle="Übersicht der Buchungsdaten für externe Fakturierung.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('organizer.billing-data.export', request()->only('filter'))">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                CSV exportieren
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if (!$organization->hasExternalInvoicing())
        <x-ui.alert tone="warning" class="mb-6">
            Ihr Rechnungsmodus ist auf <strong>Automatisch</strong> gestellt. Diese Seite ist für Organisationen mit
            externer Rechnungsstellung gedacht.
            <a href="{{ route('organizer.settings.invoice.index') }}">Rechnungsmodus ändern</a>
        </x-ui.alert>
    @endif

    <form method="GET" class="mb-6 flex flex-wrap items-end gap-x-4 gap-y-5">
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')"
            placeholder="Buchungsnr., Name oder E-Mail" class="min-w-0 flex-[2_1_260px]" />
        <x-ui.select label="Anzeigen" name="filter" :old="false" :selected="request('filter')"
            :options="['' => 'Alle Buchungen', 'pending' => 'Nicht fakturiert', 'invoiced' => 'Fakturiert']"
            class="min-w-0 flex-[1_1_200px]" />
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button type="submit" variant="secondary" size="lg">Filtern</x-ui.button>
            @if (request()->hasAny(['search', 'filter']))
                <x-ui.button variant="link" :href="route('organizer.billing-data.index')">Zurücksetzen</x-ui.button>
            @endif
        </div>
    </form>

    <form method="POST" action="{{ route('organizer.billing-data.bulk-mark-invoiced') }}" id="bulk-form" class="hidden">
        @csrf
        @method('PUT')
    </form>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-x-6 gap-y-3">
        <p class="m-0 text-ui text-muted tabular-nums">{{ $bookings->total() }} Buchung(en)</p>
        <x-ui.button type="submit" form="bulk-form" size="sm" variant="secondary">Auswahl als fakturiert markieren</x-ui.button>
    </div>

    <x-ui.table label="Buchungen für die externe Fakturierung">
        <thead>
            <tr>
                <th scope="col">
                    <label class="flex min-h-11 min-w-11 cursor-pointer items-center justify-center">
                        <input type="checkbox" id="select-all" class="ui-checkbox">
                        <span class="sr-only">Alle auswählen</span>
                    </label>
                </th>
                <th scope="col">Buchungsnr.</th>
                <th scope="col">Veranstaltung</th>
                <th scope="col">Kunde</th>
                <th scope="col">Firma</th>
                <th scope="col" class="num">Betrag</th>
                <th scope="col">Datum</th>
                <th scope="col">Status</th>
                <th scope="col">Ext. Rechnungsnr.</th>
                <th scope="col" class="actions"><span class="sr-only">Aktion</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bookings as $booking)
                <tr>
                    <td>
                        <label class="flex min-h-11 min-w-11 cursor-pointer items-center justify-center">
                            <input type="checkbox" name="booking_ids[]" value="{{ $booking->id }}" form="bulk-form"
                                class="booking-checkbox ui-checkbox">
                            <span class="sr-only">Buchung {{ $booking->booking_number }} auswählen</span>
                        </label>
                    </td>
                    <td class="tabular-nums whitespace-nowrap">{{ $booking->booking_number }}</td>
                    <td>
                        {{ $booking->event->title ?? '–' }}
                        <span class="block text-small text-muted tabular-nums">{{ $booking->event->start_date?->format('d.m.Y') }}</span>
                    </td>
                    <td>
                        {{ $booking->customer_name }}
                        <span class="block text-small text-muted">{{ $booking->customer_email }}</span>
                    </td>
                    <td>
                        {{ $booking->billing_company ?: '–' }}
                        @if ($booking->billing_vat_id)
                            <span class="block text-small text-muted">USt: {{ $booking->billing_vat_id }}</span>
                        @endif
                    </td>
                    <td class="num">{{ format_currency($booking->total) }}</td>
                    <td class="tabular-nums whitespace-nowrap">{{ $booking->created_at->format('d.m.Y') }}</td>
                    <td>
                        @if ($booking->externally_invoiced)
                            <x-ui.status tone="success">Fakturiert</x-ui.status>
                            @if ($booking->externally_invoiced_at)
                                <span class="mt-0.5 block text-small text-muted tabular-nums">{{ $booking->externally_invoiced_at->format('d.m.Y') }}</span>
                            @endif
                        @else
                            <x-ui.status tone="warning">Ausstehend</x-ui.status>
                        @endif
                    </td>
                    <td class="tabular-nums">{{ $booking->external_invoice_number ?: '–' }}</td>
                    <td class="actions">
                        @if (!$booking->externally_invoiced)
                            <form method="POST" action="{{ route('organizer.billing-data.mark-invoiced', $booking) }}"
                                class="inline-flex flex-wrap items-center justify-end gap-2" x-data="{ open: false }">
                                @csrf
                                @method('PUT')
                                <div x-show="open" x-cloak>
                                    <x-ui.input type="text" name="external_invoice_number" :id="'ext-invoice-' . $booking->id" size="sm" :old="false"
                                        aria-label="Externe Rechnungsnummer (optional)"
                                        placeholder="Ext. Rechnungsnr. (optional)" class="w-48" />
                                </div>
                                <x-ui.button size="sm" variant="secondary" x-show="!open" @click="open = true">Als fakturiert markieren</x-ui.button>
                                <x-ui.button type="submit" size="sm" x-show="open" x-cloak>Speichern</x-ui.button>
                            </form>
                        @else
                            –
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="10" class="p-0">
                        <x-ui.empty :bordered="false">Keine Buchungen gefunden.</x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if ($bookings->hasPages())
        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @endif

    @push('scripts')
        <script>
            document.getElementById('select-all')?.addEventListener('change', function() {
                document.querySelectorAll('.booking-checkbox').forEach(cb => cb.checked = this.checked);
            });
        </script>
    @endpush
</x-layouts.app>
