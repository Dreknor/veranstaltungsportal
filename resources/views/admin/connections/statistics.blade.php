<x-layouts.app title="Verbindungen: Statistiken">
    @php
        $statusLabel = ['accepted' => 'Akzeptiert', 'pending' => 'Ausstehend', 'blocked' => 'Blockiert'];
    @endphp

    <x-ui.page-header title="Verbindungen: Statistiken" :back-href="route('admin.connections.index')" back-label="Zur Übersicht der Verbindungen" />

    <!-- Period Filter -->
    <form method="GET" action="{{ route('admin.connections.statistics') }}" class="mb-10 max-w-64">
        <x-ui.select label="Zeitraum" name="period" id="period" :old="false" :selected="$period" onchange="this.form.submit()"
            :options="['7' => 'Letzte 7 Tage', '30' => 'Letzte 30 Tage', '90' => 'Letzte 90 Tage', '365' => 'Letztes Jahr']" />
    </form>

    <!-- By Status -->
    <x-ui.section title="Nach Status" class="mb-12">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($byStatus as $status)
                <x-ui.stat :label="$statusLabel[$status->status] ?? ucfirst($status->status)" :value="$status->count" />
            @endforeach
        </div>
    </x-ui.section>

    <!-- Connections Over Time -->
    <x-ui.section title="Verbindungen über Zeit" class="mb-12">
        <x-ui.table label="Verbindungen über Zeit">
            <thead>
                <tr>
                    <th scope="col">Datum</th>
                    <th scope="col" class="num">Anzahl</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($connectionsByDay as $day)
                    <tr>
                        <td class="tabular-nums">{{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}</td>
                        <td class="num">{{ $day->count }}</td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="2" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten für diesen Zeitraum.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($connectionsByDay->isNotEmpty())
                <tfoot>
                    <tr>
                        <td>Gesamt</td>
                        <td class="num">{{ $connectionsByDay->sum('count') }}</td>
                    </tr>
                </tfoot>
            @endif
        </x-ui.table>
    </x-ui.section>

    <!-- Most Active Users -->
    <x-ui.section title="Die 10 aktivsten Benutzer">
        <x-ui.table label="Aktivste Benutzer">
            <thead>
                <tr>
                    <th scope="col">Rang</th>
                    <th scope="col">Benutzer</th>
                    <th scope="col" class="num">Verbindungen</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mostActiveUsers as $index => $item)
                    @if ($item['user'])
                        <tr>
                            <td class="tabular-nums">{{ $index + 1 }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item['user']->profilePhotoUrl() }}" alt="" class="h-10 w-10 flex-none rounded-full max-sm:hidden">
                                    <div class="min-w-0">
                                        {{ $item['user']->name }}
                                        <span class="block text-small break-all text-muted">{{ $item['user']->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="num">{{ $item['count'] }}</td>
                        </tr>
                    @endif
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="3" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten für diesen Zeitraum.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>
</x-layouts.app>
