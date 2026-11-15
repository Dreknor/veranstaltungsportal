<x-layouts.app title="Transaktionen">
    <x-ui.page-header title="Transaktionshistorie" subtitle="Vollständige Übersicht aller Transaktionen.">
        @if ($transactions->count() > 0)
            <x-slot:actions>
                <x-ui.button variant="secondary" :href="route('organizer.monetization.transactions', array_merge(request()->all(), ['export' => 'csv']))">
                    <x-slot:icon><x-icon.download /></x-slot:icon>
                    Als CSV exportieren
                </x-ui.button>
            </x-slot:actions>
        @endif
    </x-ui.page-header>

    <x-ui.tabs label="Bereiche der Monetarisierung" class="mb-8">
        <x-ui.tab :href="route('organizer.monetization.index')">Übersicht</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.earnings')">Einnahmen nach Veranstaltung</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.transactions')" :active="true">Transaktionen</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.payout-settings')">Auszahlungseinstellungen</x-ui.tab>
    </x-ui.tabs>

    <form method="GET" action="{{ route('organizer.monetization.transactions') }}"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.field label="Von Datum" name="from" type="date" :old="false" :value="request('from')" />
        <x-ui.field label="Bis Datum" name="to" type="date" :old="false" :value="request('to')" />
        <div>
            <x-ui.button type="submit" variant="secondary" size="lg" full>Filtern</x-ui.button>
        </div>
    </form>

    <x-ui.table label="Transaktionen">
        <thead>
            <tr>
                <th scope="col">Datum</th>
                <th scope="col">Veranstaltung</th>
                <th scope="col">Buchungsnr.</th>
                <th scope="col">Kunde</th>
                <th scope="col" class="num">Betrag</th>
                <th scope="col" class="num">Gebühr</th>
                <th scope="col" class="num">Netto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td class="tabular-nums whitespace-nowrap">{{ $transaction->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        <a href="{{ route('organizer.events.edit', $transaction->event) }}" class="text-ink underline underline-offset-[3px]">{{ Str::limit($transaction->event->title, 30) }}</a>
                    </td>
                    <td class="tabular-nums">
                        <a href="{{ route('organizer.bookings.show', $transaction->booking) }}" class="text-ink underline underline-offset-[3px]">{{ $transaction->booking->booking_number }}</a>
                    </td>
                    <td>{{ $transaction->booking->customer_name }}</td>
                    <td class="num">{{ number_format($transaction->booking->total, 2, ',', '.') }} €</td>
                    <td class="num">−{{ number_format($transaction->fee_amount, 2, ',', '.') }} €</td>
                    <td class="num font-medium">{{ number_format($transaction->booking->total - $transaction->fee_amount, 2, ',', '.') }} €</td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="7" class="p-0">
                        <x-ui.empty :bordered="false">Keine Transaktionen gefunden.</x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if ($transactions->hasPages())
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    @endif
</x-layouts.app>
