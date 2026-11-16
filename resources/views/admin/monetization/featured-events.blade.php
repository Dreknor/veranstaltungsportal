<x-layouts.app title="Übersicht der Hervorhebungen">
    @php
        $payTone = ['paid' => 'success', 'pending' => 'warning', 'failed' => 'danger'];
        $payLabel = ['paid' => 'Bezahlt', 'pending' => 'Ausstehend', 'failed' => 'Fehlgeschlagen'];
    @endphp

    <x-ui.page-header title="Übersicht der Hervorhebungen"
        subtitle="Alle Gebühren und Transaktionen für hervorgehobene Veranstaltungen." />

    @include('admin.monetization._tabs', ['active' => 'featured'])

    <!-- Statistics -->
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Aktive Hervorhebungen" :value="$stats['active_featured']" />
        <x-ui.stat label="Gesamtumsatz" :value="\App\Support\Ui::money($stats['total_revenue'])" />
        <x-ui.stat label="Diesen Monat" :value="\App\Support\Ui::money($stats['this_month'])" />
        <x-ui.stat label="Ausstehend" :value="$stats['pending_payments']" :sub="\App\Support\Ui::money($stats['pending_revenue'])" />
    </div>

    <x-ui.section :title="'Alle Transaktionen (' . $featuredFees->total() . ')'">
        <x-ui.table label="Transaktionen für Hervorhebungen">
            <thead>
                <tr>
                    <th scope="col">Veranstaltung</th>
                    <th scope="col" class="max-sm:hidden">Veranstalter</th>
                    <th scope="col" class="max-sm:hidden">Zeitraum</th>
                    <th scope="col" class="max-sm:hidden">Dauer</th>
                    <th scope="col" class="num">Gebühr</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="max-sm:hidden">Bezahlt am</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($featuredFees as $fee)
                    <tr>
                        <th scope="row" class="max-sm:w-32 max-sm:hyphens-auto">
                            {{ $fee->event->title ?? 'Veranstaltung gelöscht' }}
                            @if ($fee->event)
                                <span
                                    class="block text-small font-normal tabular-nums text-muted">{{ $fee->event->start_date->format('d.m.Y H:i') }}</span>
                            @endif
                        </th>
                        <td class="max-sm:hidden">
                            {{ $fee->user->name ?? '–' }}
                            <span class="block text-small text-muted">{{ $fee->user->email ?? '' }}</span>
                        </td>
                        <td class="max-sm:hidden whitespace-nowrap tabular-nums">
                            {{ $fee->featured_start_date->format('d.m.Y') }}
                            <span class="block text-small text-muted">bis
                                {{ $fee->featured_end_date->format('d.m.Y') }}</span>
                        </td>
                        <td class="max-sm:hidden whitespace-nowrap">
                            @if ($fee->duration_type === 'daily')
                                1 Tag
                            @elseif ($fee->duration_type === 'weekly')
                                7 Tage
                            @elseif ($fee->duration_type === 'monthly')
                                30 Tage
                            @else
                                {{ $fee->duration_days }} Tage
                            @endif
                        </td>
                        <td class="num">{{ \App\Support\Ui::money($fee->fee_amount) }}</td>
                        <td>
                            <x-ui.status
                                :tone="$payTone[$fee->payment_status] ?? 'neutral'">{{ $payLabel[$fee->payment_status] ?? ucfirst($fee->payment_status) }}</x-ui.status>
                        </td>
                        <td class="max-sm:hidden whitespace-nowrap tabular-nums">
                            @if ($fee->paid_at)
                                {{ $fee->paid_at->format('d.m.Y H:i') }}
                                @if ($fee->payment_method)
                                    <span class="block text-small text-muted">per
                                        {{ \App\Support\AdminLabels::paymentMethod($fee->payment_method) }}</span>
                                @endif
                            @else
                                –
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="7" class="p-0">
                            <x-ui.empty :bordered="false" title="Keine Hervorhebungen">
                                Bisher wurden keine Gebühren für Hervorhebungen erstellt.
                            </x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>

        @if ($featuredFees->hasPages())
            <div class="mt-6">
                {{ $featuredFees->links() }}
            </div>
        @endif
    </x-ui.section>

    <x-ui.alert tone="info" title="Informationen" class="mt-8">
        <ul class="m-0 list-disc pl-5">
            <li>Hervorhebungen werden automatisch deaktiviert, wenn der Zeitraum abgelaufen ist.</li>
            <li>Die automatische Prüfung läuft täglich um 00:00 Uhr.</li>
            <li>Gebühren können in den Monetarisierungs-Einstellungen angepasst werden.</li>
            <li>Veranstalter erhalten 3 Tage vor Ablauf eine Benachrichtigung.</li>
        </ul>
    </x-ui.alert>
</x-layouts.app>
