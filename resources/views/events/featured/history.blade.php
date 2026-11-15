<x-layouts.app>
    <x-ui.page-header title="Meine hervorgehobenen Veranstaltungen"
        subtitle="Übersicht der hervorgehobenen Veranstaltungen und Zahlungen." />

    <x-ui.table label="Hervorhebungen">
        <thead>
            <tr>
                <th scope="col">Veranstaltung</th>
                <th scope="col">Zeitraum</th>
                <th scope="col">Status</th>
                <th scope="col" class="num">Betrag</th>
                <th scope="col" class="actions"><span class="sr-only">Aktion</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($featuredHistory as $fee)
                <tr>
                    <th scope="row">{{ $fee->event->title }}</th>
                    <td class="tabular-nums whitespace-nowrap">{{ optional($fee->featured_start_date)->format('d.m.Y') }}–{{ optional($fee->featured_end_date)->format('d.m.Y') }}</td>
                    <td>
                        <x-ui.status :tone="\App\Support\Ui::paymentTone($fee->payment_status)">{{ ucfirst($fee->payment_status) }}</x-ui.status>
                    </td>
                    <td class="num">{{ \App\Support\Ui::money($fee->amount) }}</td>
                    <td class="actions">
                        @if ($fee->payment_status !== 'paid')
                            <x-ui.button size="sm" variant="secondary" :href="route('organizer.featured-events.payment', $fee)">Zahlung</x-ui.button>
                        @else
                            <x-ui.button size="sm" variant="secondary" :href="route('organizer.featured-events.extend', $fee->event)">Verlängern</x-ui.button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="5" class="p-0">
                        <x-ui.empty :bordered="false">Keine hervorgehobenen Veranstaltungen gefunden.</x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>
</x-layouts.app>
