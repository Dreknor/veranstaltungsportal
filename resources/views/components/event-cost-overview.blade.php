{{--
    Kostenübersicht einer Veranstaltung für Veranstalter: Posten mit Status, Summen, Hinweise.
    Erwartet $costs = ['breakdown' => [...], 'total' => float, 'featured_fees' => ['active' => bool]].
--}}
@props(['costs', 'showDetails' => true])

<x-ui.box tone="zone" {{ $attributes->class('flex flex-col gap-5') }}>
    <h3 class="m-0 text-h3">Kostenübersicht</h3>

    @if ($showDetails && count($costs['breakdown']) > 0)
        <ul class="m-0 flex list-none flex-col divide-y divide-line border-y border-line p-0">
            @foreach ($costs['breakdown'] as $item)
                <li class="flex items-start justify-between gap-4 py-3">
                    <div class="flex min-w-0 flex-col gap-1">
                        <span class="text-ui font-medium">{{ $item['label'] }}</span>
                        @if (isset($item['status']) && $item['status'] === 'pending')
                            <x-ui.status tone="warning">Zahlung ausstehend</x-ui.status>
                        @elseif(isset($item['status']) && $item['status'] === 'paid')
                            <x-ui.status tone="success">Bezahlt</x-ui.status>
                        @elseif(isset($item['status']) && $item['status'] === 'estimated')
                            <x-ui.status tone="neutral">Geschätzt</x-ui.status>
                        @endif
                        @if (isset($item['description']))
                            <span class="text-small text-muted">{{ $item['description'] }}</span>
                        @endif
                    </div>
                    <span class="text-base font-semibold whitespace-nowrap tabular-nums">
                        {{ \App\Support\Ui::money($item['amount']) }}
                    </span>
                </li>
            @endforeach
        </ul>
    @endif

    <div class="flex flex-col gap-2">
        <x-ui.summary-line label="Zwischensumme (netto)" :amount="\App\Support\Ui::money($costs['total'])" />
        <x-ui.summary-line label="MwSt. (19 %)" :amount="\App\Support\Ui::money($costs['total'] * 0.19)" muted />
    </div>
    <x-ui.summary-total label="Gesamtkosten (brutto)" :amount="\App\Support\Ui::money($costs['total'] * 1.19)" />

    <x-ui.alert tone="warning" title="Wichtige Hinweise">
        <ul>
            <li>Plattformgebühren sind Schätzungen auf Grundlage Ihrer Ticket-Einstellungen.</li>
            <li>Die tatsächliche Abrechnung erfolgt nach dem Ende der Veranstaltung.</li>
            @if ($costs['featured_fees']['active'] ?? false)
                <li class="font-semibold">Gebühren für hervorgehobene Veranstaltungen müssen vor der Veröffentlichung
                    bezahlt werden.</li>
            @endif
            <li>Sie erhalten nach Abschluss der Veranstaltung eine detaillierte Rechnung.</li>
        </ul>
    </x-ui.alert>
</x-ui.box>
