<x-layouts.app title="System-Log-Statistiken" :header="false">
    <x-ui.page-header title="System-Log-Statistiken" subtitle="Übersicht und Analyse der System-Logs" :back-href="route('admin.system-logs.index')"
        back-label="Zurück zur Übersicht" />

    <div class="mb-12 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Logs gesamt" :value="number_format($stats['total'])" />
        <x-ui.stat label="Heute" :value="number_format($stats['today'])" />
        <x-ui.stat label="Letzte 7 Tage" :value="number_format($stats['week'])" />
    </div>

    <x-ui.section title="Verteilung nach Schweregrad" class="mb-12">
        <x-ui.table label="Verteilung nach Schweregrad">
            <thead>
                <tr>
                    <th scope="col">Schweregrad</th>
                    <th scope="col" class="num">Anzahl</th>
                    <th scope="col" class="hidden w-1/3 md:table-cell"><span class="sr-only">Anteil</span></th>
                    <th scope="col" class="num">Anteil</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stats['by_level'] as $level)
                    @php
                        $percentage = $stats['total'] > 0 ? ($level->count / $stats['total']) * 100 : 0;
                        $levelTone = \App\Support\Ui::logLevelTone($level->level_name);
                    @endphp
                    <tr>
                        <th scope="row"><x-ui.status :tone="$levelTone">{{ $level->level_name }}</x-ui.status></th>
                        <td class="num">{{ number_format($level->count) }}</td>
                        <td class="hidden md:table-cell">
                            <x-ui.progress :value="$percentage" decorative />
                        </td>
                        <td class="num">{{ \App\Support\Ui::percent($percentage) }}</td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="4" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten verfügbar</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>

    <x-ui.section title="Häufigste Kanäle" class="mb-12">
        <x-ui.table label="Häufigste Kanäle">
            <thead>
                <tr>
                    <th scope="col">Kanal</th>
                    <th scope="col" class="num">Anzahl</th>
                    <th scope="col" class="num">Prozent</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stats['by_channel'] as $channel)
                    @php
                        $percentage = $stats['total'] > 0 ? ($channel->count / $stats['total']) * 100 : 0;
                    @endphp
                    <tr>
                        <th scope="row">{{ $channel->channel }}</th>
                        <td class="num">{{ number_format($channel->count) }}</td>
                        <td class="num">{{ \App\Support\Ui::percent($percentage) }}</td>
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

    <x-ui.section title="Letzte Fehler (ERROR, CRITICAL, ALERT, EMERGENCY)">
        @forelse ($stats['recent_errors'] as $error)
            <div class="flex items-start justify-between gap-4 border-t border-line py-4 last:border-b">
                <div class="min-w-0 flex-1">
                    <div class="mb-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <x-ui.status tone="danger">{{ $error->level_name }}</x-ui.status>
                        <span class="text-small text-muted tabular-nums">
                            @php
                                try {
                                    echo \Carbon\Carbon::parse($error->datetime)->format('d.m.Y H:i:s');
                                } catch (\Exception $e) {
                                    $cleanDate = preg_replace('/:\d{4}$/', '', $error->datetime);
                                    try {
                                        echo \Carbon\Carbon::parse($cleanDate)->format('d.m.Y H:i:s');
                                    } catch (\Exception $e2) {
                                        echo htmlspecialchars($error->datetime);
                                    }
                                }
                            @endphp
                        </span>
                        @if ($error->channel)
                            <span class="text-small text-muted">[{{ $error->channel }}]</span>
                        @endif
                    </div>
                    <p class="m-0 line-clamp-2 text-small">{{ $error->message }}</p>
                </div>
                <x-ui.button variant="link" :href="route('admin.system-logs.show', $error->id)" arrow>Details</x-ui.button>
            </div>
        @empty
            <x-ui.empty>Keine kritischen Fehler gefunden</x-ui.empty>
        @endforelse
    </x-ui.section>
</x-layouts.app>
