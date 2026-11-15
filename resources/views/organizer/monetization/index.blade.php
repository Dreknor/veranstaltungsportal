<x-layouts.app title="Monetarisierung">
    <x-ui.page-header title="Monetarisierung und Einnahmen" subtitle="Übersicht über Ihre Einnahmen und Auszahlungen." />

    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Gesamtumsatz" :value="number_format($totalRevenue, 2, ',', '.') . ' €'" sub="Aus allen Ticketverkäufen" />
        <x-ui.stat label="Plattformgebühren" :value="number_format($totalFees, 2, ',', '.') . ' €'" :sub="$platformFeePercentage . ' % der Einnahmen'" />
        <x-ui.stat label="Netto-Einnahmen" :value="number_format($netRevenue, 2, ',', '.') . ' €'" sub="Nach Plattformgebühren" />
    </div>

    <x-ui.tabs label="Bereiche der Monetarisierung" class="mb-8">
        <x-ui.tab :href="route('organizer.monetization.index')" :active="true">Übersicht</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.earnings')">Einnahmen nach Veranstaltung</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.transactions')">Transaktionen</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.payout-settings')">Auszahlungseinstellungen</x-ui.tab>
    </x-ui.tabs>

    <x-ui.section title="Letzte Transaktionen" level="h2" :link-href="$recentTransactions->count() > 0 ? route('organizer.monetization.transactions') : null" link-label="Alle Transaktionen anzeigen">
        <x-ui.table label="Letzte Transaktionen">
            <thead>
                <tr>
                    <th scope="col">Datum</th>
                    <th scope="col">Veranstaltung</th>
                    <th scope="col">Buchung</th>
                    <th scope="col" class="num">Umsatz</th>
                    <th scope="col" class="num">Gebühr</th>
                    <th scope="col" class="num">Netto</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentTransactions as $transaction)
                    <tr>
                        <td class="tabular-nums whitespace-nowrap">{{ $transaction->created_at->format('d.m.Y') }}</td>
                        <td>
                            <a href="{{ route('organizer.events.edit', $transaction->event) }}" class="text-ink underline underline-offset-[3px]">{{ Str::limit($transaction->event->title, 40) }}</a>
                        </td>
                        <td class="tabular-nums">{{ $transaction->booking->booking_number }}</td>
                        <td class="num">{{ number_format($transaction->booking->total, 2, ',', '.') }} €</td>
                        <td class="num">−{{ number_format($transaction->fee_amount, 2, ',', '.') }} €</td>
                        <td class="num font-medium">{{ number_format($transaction->booking->total - $transaction->fee_amount, 2, ',', '.') }} €</td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="6" class="p-0">
                            <x-ui.empty :bordered="false">Noch keine Transaktionen vorhanden.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>

    <x-ui.alert tone="info" class="mt-8" title="Informationen zu Auszahlungen">
        <p class="m-0">Auszahlungen erfolgen monatlich auf Ihr hinterlegtes Bankkonto. Stellen Sie sicher, dass Ihre Auszahlungsdaten korrekt sind.</p>
        <p class="m-0 mt-2"><a href="{{ route('organizer.monetization.payout-settings') }}">Auszahlungseinstellungen verwalten</a></p>
    </x-ui.alert>
</x-layouts.app>
