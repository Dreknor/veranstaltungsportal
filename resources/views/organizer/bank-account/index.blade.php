<x-layouts.app title="Kontoverbindung">
    <x-ui.page-header title="Rechnungsdaten verwalten"
        subtitle="Verwalten Sie Ihre Bankdaten, Firmendaten und Rechnungseinstellungen." />

    @if (!auth()->user()->hasCompleteBankAccount())
        <x-ui.alert tone="warning" class="mb-6" title="Bankverbindung unvollständig">
            Bitte füllen Sie alle Felder aus, um Veranstaltungen veröffentlichen zu können.
        </x-ui.alert>
    @endif

    <x-ui.tabs label="Rechnungsdaten" class="mb-8">
        <x-ui.tab :href="route('organizer.bank-account.index')" :active="true">Kontoverbindung</x-ui.tab>
        <x-ui.tab :href="route('organizer.bank-account.billing-data')">Rechnungsdaten</x-ui.tab>
        <x-ui.tab :href="route('organizer.settings.invoice.index')">Rechnungsnummern</x-ui.tab>
    </x-ui.tabs>

    <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between lg:gap-x-12">
        <x-ui.section title="Bankverbindung" description="Hinterlegen Sie Ihre Bankdaten für Teilnehmerzahlungen.">
            <form method="POST" action="{{ route('organizer.bank-account.update') }}" class="flex flex-col gap-y-5">
                @csrf
                @method('PUT')
                <p class="m-0 text-small text-muted">* Pflichtfeld</p>

                <x-ui.field label="Kontoinhaber" name="account_holder" required
                    :value="$bankAccount['account_holder'] ?? ''" placeholder="Max Mustermann" />
                <x-ui.field label="Bankname" name="bank_name" required
                    :value="$bankAccount['bank_name'] ?? ''" placeholder="z. B. Sparkasse München" />
                <x-ui.field label="IBAN" name="iban" required
                    :value="$bankAccount['iban'] ?? ''" placeholder="DE89 3704 0044 0532 0130 00" input-class="tabular-nums" />
                <x-ui.field label="BIC/SWIFT" name="bic" required
                    :value="$bankAccount['bic'] ?? ''" placeholder="COBADEFFXXX" input-class="tabular-nums" />

                <div class="pt-3">
                    <x-ui.button type="submit">Kontoverbindung speichern</x-ui.button>
                </div>
            </form>
        </x-ui.section>

        <x-ui.alert tone="info" title="Wichtige Informationen">
            <ul class="m-0 list-disc pl-5">
                <li>Diese Kontoverbindung wird auf den Rechnungen an Ihre Teilnehmenden angezeigt.</li>
                <li>Teilnehmende überweisen die Ticketpreise direkt auf dieses Konto.</li>
                <li>Stellen Sie sicher, dass alle Daten korrekt sind.</li>
                <li>Ihre Daten werden sicher und verschlüsselt gespeichert.</li>
            </ul>
        </x-ui.alert>
    </div>
</x-layouts.app>
