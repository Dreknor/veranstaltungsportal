<x-layouts.app title="Hervorgehobene Veranstaltungen verwalten">
    @php
        $payTone = ['paid' => 'success', 'pending' => 'warning', 'failed' => 'danger'];
        $payLabel = [
            'paid' => 'Bezahlt',
            'pending' => 'Ausstehend',
            'failed' => 'Fehlgeschlagen',
            'refunded' => 'Erstattet',
        ];
    @endphp

    <x-ui.page-header title="Hervorgehobene Veranstaltungen verwalten">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.featured-events.statistics')">
                <x-slot:icon><x-icon.chart /></x-slot:icon>
                Statistiken
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Statistics -->
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <x-ui.stat label="Gesamt" :value="$stats['total']" />
        <x-ui.stat label="Aktiv" :value="$stats['active']" />
        <x-ui.stat label="Ausstehend" :value="$stats['pending']" />
        <x-ui.stat label="Umsatz" :value="\App\Support\Ui::money($stats['revenue'])" />
        <x-ui.stat label="Offen" :value="\App\Support\Ui::money($stats['pending_revenue'])" />
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.featured-events.index') }}"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')"
            placeholder="Veranstaltung suchen" />
        <x-ui.select label="Status" name="status" :old="false" :selected="request('status')" :options="[
            '' => 'Alle Status',
            'pending' => 'Ausstehend',
            'paid' => 'Bezahlt',
            'failed' => 'Fehlgeschlagen',
            'refunded' => 'Erstattet',
        ]" />
        <x-ui.select label="Zeitraum" name="duration" :old="false" :selected="request('duration')" :options="['' => 'Alle Zeiträume', 'daily' => 'Täglich', 'weekly' => 'Wöchentlich', 'monthly' => 'Monatlich']" />
        <div class="flex flex-wrap gap-3">
            <x-ui.button type="submit" variant="secondary" size="lg">Filtern</x-ui.button>
            @if (request()->hasAny(['search', 'status', 'duration']))
                <x-ui.button variant="link" :href="route('admin.featured-events.index')">Zurücksetzen</x-ui.button>
            @endif
        </div>
    </form>

    <form id="bulkActionForm" method="POST" action="{{ route('admin.featured-events.bulk') }}">
        @csrf
        <div class="mb-4 flex flex-wrap items-end justify-between gap-x-6 gap-y-3">
            <h2 class="m-0 text-h3">Gebühren für Hervorhebungen ({{ $fees->total() }})</h2>
            <div class="flex flex-wrap items-end gap-3">
                <x-ui.select label="Sammelaktion" name="action" id="bulkAction" :old="false" class="min-w-52"
                    :options="[
                        '' => 'Sammelaktion wählen...',
                        'mark_paid' => 'Als bezahlt markieren',
                        'mark_failed' => 'Als fehlgeschlagen markieren',
                        'send_reminder' => 'Erinnerung senden',
                    ]" />
                <x-ui.button type="submit" variant="secondary" size="lg"
                    onclick="return confirm('Sind Sie sicher?')">Ausführen</x-ui.button>
            </div>
        </div>

        <x-ui.table label="Gebühren für Hervorhebungen">
            <thead>
                <tr>
                    <th scope="col">
                        <input type="checkbox" id="selectAll" class="ui-checkbox" aria-label="Alle Gebühren auswählen">
                    </th>
                    <th scope="col">Veranstaltung</th>
                    <th scope="col">Veranstalter</th>
                    <th scope="col" class="num">Betrag</th>
                    <th scope="col">Zeitraum</th>
                    <th scope="col">Status</th>
                    <th scope="col">Läuft ab</th>
                    <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fees as $fee)
                    @php
                        $endDate = \Carbon\Carbon::parse($fee->featured_end_date);
                        $startDate = \Carbon\Carbon::parse($fee->featured_start_date);
                        $daysLeft = (int) now()
                            ->startOfDay()
                            ->diffInDays($endDate->copy()->startOfDay(), false);
                    @endphp
                    <tr>
                        <td>
                            <input type="checkbox" name="fee_ids[]" value="{{ $fee->id }}"
                                class="fee-checkbox ui-checkbox"
                                aria-label="Gebühr für {{ $fee->event->title }} auswählen">
                        </td>
                        <td>
                            {{ Str::limit($fee->event->title, 40) }}
                            <span
                                class="block text-small tabular-nums text-muted">{{ $fee->created_at->format('d.m.Y H:i') }}</span>
                        </td>
                        <td>{{ $fee->event->organizer->name }}</td>
                        <td class="num">{{ \App\Support\Ui::money($fee->fee_amount) }}</td>
                        <td class="whitespace-nowrap">
                            {{ (int) $startDate->diffInDays($endDate) }}
                            {{ (int) $startDate->diffInDays($endDate) === 1 ? 'Tag' : 'Tage' }}
                            <span class="block text-small tabular-nums text-muted">{{ $startDate->format('d.m.Y') }} –
                                {{ $endDate->format('d.m.Y') }}</span>
                        </td>
                        <td>
                            <x-ui.status
                                :tone="$payTone[$fee->payment_status] ?? 'neutral'">{{ $payLabel[$fee->payment_status] ?? ucfirst($fee->payment_status) }}</x-ui.status>
                        </td>
                        <td class="whitespace-nowrap tabular-nums">
                            {{ $endDate->format('d.m.Y') }}
                            @if ($endDate->isPast())
                                <span class="block text-small text-danger">Abgelaufen</span>
                            @elseif ($daysLeft <= 3)
                                <span class="block text-small text-warning">Läuft bald ab</span>
                            @endif
                        </td>
                        <td class="actions">
                            <x-ui.button size="sm" variant="secondary" :href="route('admin.featured-events.show', $fee)">Details</x-ui.button>
                            @if ($fee->payment_status === 'pending')
                                <x-ui.button size="sm" variant="secondary" type="submit"
                                    :form="'remind-fee-' . $fee->id">Erinnern</x-ui.button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="8" class="p-0">
                            <x-ui.empty :bordered="false">Keine Gebühren für Hervorhebungen gefunden.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </form>

    {{-- Erinnerungs-Formulare außerhalb des Sammelformulars (Formulare dürfen nicht geschachtelt sein) --}}
    @foreach ($fees as $fee)
        @if ($fee->payment_status === 'pending')
            <form id="remind-fee-{{ $fee->id }}" method="POST"
                action="{{ route('admin.featured-events.send-reminder', $fee) }}" class="hidden">
                @csrf
            </form>
        @endif
    @endforeach

    @if ($fees->hasPages())
        <div class="mt-6">
            {{ $fees->links() }}
        </div>
    @endif

    @push('scripts')
        <script>
            document.getElementById('selectAll').addEventListener('change', function(e) {
                document.querySelectorAll('.fee-checkbox').forEach(checkbox => {
                    checkbox.checked = e.target.checked;
                });
            });
        </script>
    @endpush
</x-layouts.app>
