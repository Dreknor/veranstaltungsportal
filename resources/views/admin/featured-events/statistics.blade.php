<x-layouts.app title="Hervorgehobene Veranstaltungen: Statistiken">
    @php
        $durationLabel = ['daily' => 'Täglich', 'weekly' => 'Wöchentlich', 'monthly' => 'Monatlich'];
    @endphp

    <x-ui.page-header title="Hervorgehobene Veranstaltungen: Statistiken" :back-href="route('admin.featured-events.index')" back-label="Zur Übersicht" />

    <!-- Period Filter -->
    <form method="GET" action="{{ route('admin.featured-events.statistics') }}" class="mb-10 max-w-64">
        <x-ui.select label="Zeitraum" name="period" id="period" :old="false" :selected="$period" onchange="this.form.submit()"
            :options="['7' => 'Letzte 7 Tage', '30' => 'Letzte 30 Tage', '90' => 'Letzte 90 Tage', '365' => 'Letztes Jahr']" />
    </form>

    <!-- By Duration Type -->
    <x-ui.section title="Nach Zeitraum-Typ" class="mb-12">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($byDuration as $duration)
                <x-ui.stat :label="$durationLabel[$duration->duration_type] ?? 'Monatlich'" :value="$duration->count"
                    :sub="\App\Support\Ui::money($duration->total)" />
            @endforeach
        </div>
    </x-ui.section>

    <!-- Revenue Over Time -->
    <x-ui.section title="Umsatz über Zeit" class="mb-12">
        <x-ui.table label="Umsatz über Zeit">
            <thead>
                <tr>
                    <th scope="col">Datum</th>
                    <th scope="col" class="num">Anzahl</th>
                    <th scope="col" class="num">Umsatz</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($revenueByDay as $day)
                    <tr>
                        <td class="tabular-nums">{{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}</td>
                        <td class="num">{{ $day->count }}</td>
                        <td class="num">{{ \App\Support\Ui::money($day->total) }}</td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="3" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten für diesen Zeitraum.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($revenueByDay->isNotEmpty())
                <tfoot>
                    <tr>
                        <td>Gesamt</td>
                        <td class="num">{{ $revenueByDay->sum('count') }}</td>
                        <td class="num">{{ \App\Support\Ui::money($revenueByDay->sum('total')) }}</td>
                    </tr>
                </tfoot>
            @endif
        </x-ui.table>
    </x-ui.section>

    <!-- Top Events -->
    <x-ui.section title="Die 10 umsatzstärksten Veranstaltungen">
        <x-ui.table label="Umsatzstärkste Veranstaltungen">
            <thead>
                <tr>
                    <th scope="col">Rang</th>
                    <th scope="col">Veranstaltung</th>
                    <th scope="col" class="max-sm:hidden">Veranstalter</th>
                    <th scope="col" class="max-sm:hidden num">Anzahl</th>
                    <th scope="col" class="num">Umsatz</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topOrganizers as $index => $item)
                    <tr>
                        <td class="tabular-nums">{{ $index + 1 }}</td>
                        <td>{{ Str::limit($item->event->title, 40) }}</td>
                        <td class="max-sm:hidden">{{ $item->event->organizer->name }}</td>
                        <td class="max-sm:hidden num">{{ $item->count }}</td>
                        <td class="num">{{ \App\Support\Ui::money($item->total) }}</td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="5" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten für diesen Zeitraum.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>
</x-layouts.app>
