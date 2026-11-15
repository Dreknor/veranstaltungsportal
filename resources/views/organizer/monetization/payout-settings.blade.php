<x-layouts.app title="Auszahlungseinstellungen">
    <x-ui.page-header title="Auszahlungseinstellungen" subtitle="Hinterlegen Sie Ihre Bankdaten für monatliche Auszahlungen." />

    <x-ui.tabs label="Bereiche der Monetarisierung" class="mb-8">
        <x-ui.tab :href="route('organizer.monetization.index')">Übersicht</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.earnings')">Einnahmen nach Veranstaltung</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.transactions')">Transaktionen</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.payout-settings')" :active="true">Auszahlungseinstellungen</x-ui.tab>
    </x-ui.tabs>

    <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between lg:gap-x-12">
        <form method="POST" action="{{ route('organizer.monetization.payout-settings.update') }}" class="flex flex-col gap-y-8">
            @csrf
            @method('PUT')

            <x-ui.section title="Bankverbindung" level="h2">
                <div class="flex flex-col gap-y-5">
                    <x-ui.field label="Bankname" name="bank_name" :value="$user->payout_settings['bank_name'] ?? ''" />
                    <x-ui.field label="Kontoinhaber" name="bank_account_holder" :value="$user->payout_settings['bank_account_holder'] ?? ''" />
                    <x-ui.field label="IBAN" name="iban" :value="$user->payout_settings['iban'] ?? ''" placeholder="DE89 3704 0044 0532 0130 00" />
                    <x-ui.field label="BIC/SWIFT" name="bic" :value="$user->payout_settings['bic'] ?? ''" placeholder="COBADEFFXXX" />
                </div>
            </x-ui.section>

            <x-ui.section title="Alternative: PayPal" level="h2" class="border-t border-line pt-8">
                <x-ui.field label="PayPal-E-Mail" name="paypal_email" type="email" optional
                    :value="$user->payout_settings['paypal_email'] ?? ''" placeholder="ihr-paypal@beispiel.de"
                    hint="Hinterlegen Sie Ihre PayPal-E-Mail-Adresse für schnellere Auszahlungen." />
            </x-ui.section>

            <div class="border-t border-line pt-8">
                <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
            </div>
        </form>

        <x-ui.alert tone="info" title="Wichtige Hinweise">
            <ul class="m-0 list-disc pl-5">
                <li>Auszahlungen erfolgen monatlich zum 15. des Folgemonats.</li>
                <li>Mindestbetrag für Auszahlungen: 50,00 €</li>
                <li>Ihre Daten werden verschlüsselt gespeichert.</li>
                <li>Bei Fragen kontaktieren Sie bitte unseren Support.</li>
            </ul>
        </x-ui.alert>
    </div>
</x-layouts.app>
<x-layouts.app title="Einnahmen nach Veranstaltung">
    <x-ui.page-header title="Einnahmen nach Veranstaltung" subtitle="Detaillierte Übersicht Ihrer Einnahmen pro Veranstaltung." />

    <x-ui.tabs label="Bereiche der Monetarisierung" class="mb-8">
        <x-ui.tab :href="route('organizer.monetization.index')">Übersicht</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.earnings')" :active="true">Einnahmen nach Veranstaltung</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.transactions')">Transaktionen</x-ui.tab>
        <x-ui.tab :href="route('organizer.monetization.payout-settings')">Auszahlungseinstellungen</x-ui.tab>
    </x-ui.tabs>

    <x-ui.table label="Einnahmen nach Veranstaltung">
        <thead>
            <tr>
                <th scope="col">Veranstaltung</th>
                <th scope="col" class="num">Bestätigte Buchungen</th>
                <th scope="col" class="num">Bruttoeinnahmen</th>
                <th scope="col" class="num">Plattformgebühr</th>
                <th scope="col" class="num">Nettoeinnahmen</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($eventsWithEarnings as $eventData)
                <tr>
                    <th scope="row">
                        {{ $eventData['event']->title }}
                        <span class="block text-small font-normal text-muted tabular-nums">{{ $eventData['event']->start_date->format('d.m.Y H:i') }}</span>
                        @if ($eventData['event']->is_cancelled)
                            <x-ui.status tone="danger" class="mt-1">Abgesagt</x-ui.status>
                        @endif
                    </th>
                    <td class="num">{{ $eventData['booking_count'] }}</td>
                    <td class="num">{{ number_format($eventData['revenue'], 2, ',', '.') }} €</td>
                    <td class="num">−{{ number_format($eventData['platform_fee'], 2, ',', '.') }} €</td>
                    <td class="num font-semibold">{{ number_format($eventData['net_earnings'], 2, ',', '.') }} €</td>
                    <td class="actions">
                        <x-ui.button size="sm" variant="secondary" :href="route('organizer.events.edit', $eventData['event'])">Details</x-ui.button>
                        <x-ui.button size="sm" variant="secondary" :href="route('organizer.statistics.event', $eventData['event'])">Statistiken</x-ui.button>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="6" class="p-0">
                        <x-ui.empty :bordered="false" action-href="{{ route('organizer.events.create') }}" action-label="Erste Veranstaltung anlegen" action-variant="primary">
                            Noch keine Einnahmen vorhanden.
                        </x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($eventsWithEarnings->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="2">Gesamt</td>
                    <td class="num">{{ number_format($eventsWithEarnings->sum('revenue'), 2, ',', '.') }} €</td>
                    <td class="num">−{{ number_format($eventsWithEarnings->sum('platform_fee'), 2, ',', '.') }} €</td>
                    <td class="num">{{ number_format($eventsWithEarnings->sum('net_earnings'), 2, ',', '.') }} €</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </x-ui.table>
</x-layouts.app>
