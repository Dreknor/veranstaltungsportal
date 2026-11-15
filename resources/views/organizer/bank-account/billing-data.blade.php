<x-layouts.app title="Rechnungsdaten">
    <x-ui.page-header title="Rechnungsdaten verwalten"
        subtitle="Verwalten Sie Ihre Bankdaten, Firmendaten und Rechnungseinstellungen." />

    @if (!auth()->user()->hasCompleteBillingData())
        <x-ui.alert tone="warning" class="mb-6" title="Rechnungsdaten unvollständig">
            Bitte füllen Sie alle mit * markierten Felder aus, um Veranstaltungen veröffentlichen zu können.
        </x-ui.alert>
    @endif

    <x-ui.tabs label="Rechnungsdaten" class="mb-8">
        <x-ui.tab :href="route('organizer.bank-account.index')">Kontoverbindung</x-ui.tab>
        <x-ui.tab :href="route('organizer.bank-account.billing-data')" :active="true">Rechnungsdaten</x-ui.tab>
        <x-ui.tab :href="route('organizer.settings.invoice.index')">Rechnungsnummern</x-ui.tab>
    </x-ui.tabs>

    <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between lg:gap-x-12">
        <x-ui.section title="Firmendaten" description="Diese Daten werden als Absender auf Rechnungen an Ihre Teilnehmenden angezeigt.">
            <form method="POST" action="{{ route('organizer.bank-account.billing-data.update') }}" class="flex flex-col gap-y-5">
                @csrf
                @method('PUT')
                <p class="m-0 text-small text-muted">* Pflichtfeld</p>

                <x-ui.field label="Firmenname / Name" name="company_name" required
                    :value="$billingData['company_name'] ?? ''" placeholder="Muster GmbH" />

                <div class="flex flex-wrap gap-x-4 gap-y-5">
                    <x-ui.field label="Straße und Hausnummer" name="company_address" required
                        :value="$billingData['company_address'] ?? ''" placeholder="Musterstraße 123" class="min-w-0 flex-[3_1_200px]" />
                    <x-ui.field label="PLZ" name="company_postal_code" required inputmode="numeric"
                        :value="$billingData['company_postal_code'] ?? ''" placeholder="12345" class="max-w-40 flex-[1_1_112px]" />
                </div>

                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="Stadt" name="company_city" required
                        :value="$billingData['company_city'] ?? ''" placeholder="Berlin" />
                    <x-ui.field label="Land" name="company_country" required
                        :value="$billingData['company_country'] ?? 'Deutschland'" />
                </div>

                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="Steuernummer" name="tax_id" required
                        :value="$billingData['tax_id'] ?? ''" placeholder="12/345/67890" />
                    <x-ui.field label="USt-IdNr." name="vat_id" optional
                        :value="$billingData['vat_id'] ?? ''" placeholder="DE123456789" />
                </div>

                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="E-Mail" name="company_email" type="email" required
                        :value="$billingData['company_email'] ?? ''" placeholder="info@firma.de" />
                    <x-ui.field label="Telefon" name="company_phone" type="tel" required
                        :value="$billingData['company_phone'] ?? ''" placeholder="+49 123 456789" />
                </div>

                <div class="pt-3">
                    <x-ui.button type="submit">Rechnungsdaten speichern</x-ui.button>
                </div>
            </form>
        </x-ui.section>

        <x-ui.alert tone="info" title="Verwendung der Rechnungsdaten">
            Diese Daten werden als Absender auf den Rechnungen an Ihre Teilnehmenden angezeigt. Stellen Sie sicher,
            dass alle Angaben korrekt sind.
        </x-ui.alert>
    </div>
</x-layouts.app>
