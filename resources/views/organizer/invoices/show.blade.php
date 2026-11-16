<x-layouts.app :title="'Rechnung ' . $invoice->invoice_number">
    <x-ui.page-header :title="'Rechnung ' . $invoice->invoice_number" :back-href="route('organizer.invoices.index')" back-label="Zu den Rechnungen">
        <x-slot:actions>
            @if ($invoice->pdf_path)
                <x-ui.button :href="route('organizer.invoices.download', $invoice)">
                    <x-slot:icon><x-icon.download /></x-slot:icon>
                    PDF herunterladen
                </x-ui.button>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    @if ($invoice->status === 'paid')
        <x-ui.alert tone="success" class="mb-8" title="Rechnung bezahlt">
            Bezahlt am {{ $invoice->paid_at->format('d.m.Y') }}
        </x-ui.alert>
    @elseif ($invoice->due_date->isPast())
        <x-ui.alert tone="danger" class="mb-8" title="Rechnung überfällig">
            Fällig seit {{ $invoice->due_date->format('d.m.Y') }}
        </x-ui.alert>
    @else
        <x-ui.alert tone="warning" class="mb-8" title="Zahlungsziel">
            Fällig am {{ $invoice->due_date->format('d.m.Y') }}
        </x-ui.alert>
    @endif

    <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,368px)] lg:gap-x-12">
        <div class="flex min-w-0 flex-col gap-12">
            <x-ui.section title="Rechnungsdetails">
                <x-ui.dl>
                    <x-ui.dl-item label="Rechnungsnummer" num>{{ $invoice->invoice_number }}</x-ui.dl-item>
                    <x-ui.dl-item label="Status">
                        @if ($invoice->status === 'paid')
                            <x-ui.status tone="success">Bezahlt</x-ui.status>
                        @elseif ($invoice->status === 'overdue')
                            <x-ui.status tone="danger">Überfällig</x-ui.status>
                        @else
                            <x-ui.status tone="warning">Offen</x-ui.status>
                        @endif
                    </x-ui.dl-item>
                    <x-ui.dl-item label="Rechnungsdatum"
                        num>{{ $invoice->invoice_date->format('d.m.Y') }}</x-ui.dl-item>
                    <x-ui.dl-item label="Fälligkeitsdatum" num>{{ $invoice->due_date->format('d.m.Y') }}</x-ui.dl-item>
                    <x-ui.dl-item label="Veranstaltung">
                        @if ($invoice->event)
                            <a href="{{ route('organizer.events.edit', $invoice->event) }}"
                                class="font-medium text-ink underline underline-offset-[3px]">{{ $invoice->event->title }}</a>
                        @endif
                    </x-ui.dl-item>
                    <x-ui.dl-item label="Datum der Veranstaltung" num>
                        @if ($invoice->event)
                            {{ $invoice->event->start_date->format('d.m.Y') }}
                        @endif
                    </x-ui.dl-item>
                </x-ui.dl>
            </x-ui.section>

            @if (isset($platformFees) && $platformFees->count() > 0)
                <x-ui.section title="Aufschlüsselung der Plattformgebühren">
                    <x-ui.table label="Plattformgebühren dieser Rechnung">
                        <thead>
                            <tr>
                                <th scope="col">Buchung</th>
                                <th scope="col" class="num max-sm:hidden">Buchungsbetrag</th>
                                <th scope="col" class="num max-sm:hidden">Gebührensatz</th>
                                <th scope="col" class="num">Gebühr</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($platformFees as $fee)
                                <tr>
                                    <td class="tabular-nums whitespace-nowrap">
                                        @if ($fee->booking)
                                            <a href="{{ route('organizer.bookings.show', $fee->booking) }}"
                                                class="text-ink underline underline-offset-[3px]">{{ $fee->booking->booking_number }}</a>
                                        @else
                                            –
                                        @endif
                                    </td>
                                    <td class="num max-sm:hidden">{{ \App\Support\Ui::money($fee->booking_amount) }}
                                    </td>
                                    <td class="num max-sm:hidden">
                                        {{ number_format((float) $fee->fee_percentage, 2, ',', '.') }} %</td>
                                    <td class="num">{{ \App\Support\Ui::money($fee->fee_amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="num max-sm:hidden">Gesamt (netto)</td>
                                <td class="num sm:hidden">Gesamt (netto)</td>
                                <td class="num">{{ \App\Support\Ui::money($platformFees->sum('fee_amount')) }}</td>
                            </tr>
                        </tfoot>
                    </x-ui.table>
                </x-ui.section>
            @endif

            @if (isset($invoice->billing_data['items']))
                <x-ui.section title="Rechnungspositionen">
                    <x-ui.table label="Rechnungspositionen">
                        <thead>
                            <tr>
                                <th scope="col">Beschreibung</th>
                                <th scope="col" class="num">Menge</th>
                                <th scope="col" class="num">Einzelpreis</th>
                                <th scope="col" class="num">Gesamt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->billing_data['items'] as $item)
                                <tr>
                                    <td>
                                        {{ $item['description'] ?? 'Platform-Fee' }}
                                        @if (isset($item['details']))
                                            <span class="block text-small text-muted">{{ $item['details'] }}</span>
                                        @endif
                                    </td>
                                    <td class="num">{{ $item['quantity'] ?? 1 }}</td>
                                    <td class="num">
                                        {{ \App\Support\Ui::money($item['unit_price'] ?? $item['total']) }}</td>
                                    <td class="num">{{ \App\Support\Ui::money($item['total']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-ui.table>
                </x-ui.section>
            @endif
        </div>

        <aside class="flex min-w-0 flex-col gap-8">
            <x-ui.box tone="zone">
                <h2 class="m-0 mb-4 text-h3">Rechnungsbetrag</h2>
                <x-ui.summary-lines>
                    <x-ui.summary-line label="Zwischensumme (netto)" :amount="\App\Support\Ui::money($invoice->amount)" />
                    <x-ui.summary-line :label="'MwSt. (' . number_format($invoice->tax_rate, 1, ',', '.') . ' %)'" :amount="\App\Support\Ui::money($invoice->tax_amount)" />
                </x-ui.summary-lines>
                <x-ui.summary-total label="Gesamtbetrag (brutto)" :amount="\App\Support\Ui::money($invoice->total_amount)" />
            </x-ui.box>

            @if ($invoice->status !== 'paid' && isset($invoice->billing_data['platform']))
                @php $platform = $invoice->billing_data['platform']; @endphp
                <section aria-labelledby="zahlungsinformationen" class="border-t border-line pt-6">
                    <h2 id="zahlungsinformationen" class="m-0 mb-4 text-h3">Zahlungsinformationen</h2>
                    <x-ui.dl layout="rows">
                        @if (!empty($platform['bank_name']))
                            <x-ui.dl-item label="Bank">{{ $platform['bank_name'] }}</x-ui.dl-item>
                        @endif
                        @if (!empty($platform['iban']))
                            <x-ui.dl-item label="IBAN" num>{{ $platform['iban'] }}</x-ui.dl-item>
                        @endif
                        @if (!empty($platform['bic']))
                            <x-ui.dl-item label="BIC" num>{{ $platform['bic'] }}</x-ui.dl-item>
                        @endif
                        <x-ui.dl-item label="Verwendungszweck" num>{{ $invoice->invoice_number }}</x-ui.dl-item>
                    </x-ui.dl>
                </section>
            @endif

            <section aria-labelledby="rechnungsempfaenger" class="border-t border-line pt-6">
                <h2 id="rechnungsempfaenger" class="m-0 mb-4 text-h3">Rechnungsempfänger</h2>
                <p class="m-0 text-base font-medium">{{ $invoice->recipient_name }}</p>
                @if ($invoice->recipient_address)
                    <p class="m-0 whitespace-pre-line text-muted">{{ $invoice->recipient_address }}</p>
                @endif
                <p class="m-0 mt-2 text-muted">{{ $invoice->recipient_email }}</p>
            </section>

            <section aria-labelledby="dokument" class="border-t border-line pt-6">
                <h2 id="dokument" class="m-0 mb-4 text-h3">Dokument</h2>
                @if ($invoice->pdf_path)
                    <x-ui.button variant="secondary" :href="route('organizer.invoices.download', $invoice)" full>
                        <x-slot:icon><x-icon.download /></x-slot:icon>
                        PDF herunterladen
                    </x-ui.button>
                @else
                    <p class="m-0 text-small text-muted">PDF wird generiert …</p>
                @endif
            </section>
        </aside>
    </div>
</x-layouts.app>
