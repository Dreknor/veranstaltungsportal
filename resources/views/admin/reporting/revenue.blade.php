<x-layouts.app title="Umsatz-Report" :header="false">
    <x-ui.page-header title="Umsatz-Report" subtitle="Finanzielle Übersicht und Umsatzanalyse"
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

    <div class="mb-12">
        <x-ui.stat label="Gesamte Platform-Gebühren (Featured-Veranstaltungen)" :value="\App\Support\Ui::money($data['platform_fees'])" class="p-6" />
    </div>

    <div class="mb-12 grid grid-cols-1 gap-x-12 gap-y-12 lg:grid-cols-2">
        <x-ui.section title="Zahlungsstatus">
            <x-ui.table label="Zahlungsstatus">
                <thead>
                    <tr>
                        <th scope="col">Status</th>
                        <th scope="col" class="num">Anzahl</th>
                        <th scope="col" class="num">Summe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['payment_status_breakdown'] as $status)
                        @php
                            $paymentTone = match ($status->payment_status) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                default => 'danger',
                            };
                        @endphp
                        <tr>
                            <th scope="row"><x-ui.status :tone="$paymentTone">{{ ucfirst($status->payment_status) }}</x-ui.status></th>
                            <td class="num">{{ number_format($status->count) }}</td>
                            <td class="num">{{ \App\Support\Ui::money($status->total) }}</td>
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

        <x-ui.section title="Zahlungsmethoden">
            <x-ui.table label="Zahlungsmethoden">
                <thead>
                    <tr>
                        <th scope="col">Methode</th>
                        <th scope="col" class="num">Anzahl</th>
                        <th scope="col" class="num">Summe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['payment_methods'] as $method)
                        <tr>
                            <th scope="row">{{ ucfirst($method->payment_method ?? 'Nicht angegeben') }}</th>
                            <td class="num">{{ number_format($method->count) }}</td>
                            <td class="num">{{ \App\Support\Ui::money($method->total) }}</td>
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
    </div>

    <x-ui.section title="Umsatz nach Kategorie">
        <x-ui.table label="Umsatz nach Kategorie">
            <thead>
                <tr>
                    <th scope="col">Kategorie</th>
                    <th scope="col" class="num">Umsatz</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['revenue_by_category'] as $category)
                    <tr>
                        <th scope="row">{{ $category->name }}</th>
                        <td class="num">{{ \App\Support\Ui::money($category->bookings_sum_total_amount ?? 0) }}</td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="2" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten verfügbar</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>
</x-layouts.app>
