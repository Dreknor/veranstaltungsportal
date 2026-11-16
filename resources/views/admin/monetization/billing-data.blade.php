<x-layouts.app title="Plattform-Rechnungsdaten">
    @php $d = $billingData; @endphp

    <x-ui.page-header title="Plattform-Rechnungsdaten" subtitle="Firmendaten für Rechnungen an Organisatoren." />

    <x-ui.tabs label="Bereiche der Monetarisierung" class="mb-8">
        <x-ui.tab :href="route('admin.monetization.index')">Gebühren-Einstellungen</x-ui.tab>
        <x-ui.tab :href="route('admin.monetization.billing-data')" active>Plattform-Rechnungsdaten</x-ui.tab>
        <x-ui.tab :href="route('admin.settings.invoice.index')">Rechnungsnummern</x-ui.tab>
    </x-ui.tabs>

    <form method="POST" action="{{ route('admin.monetization.billing-data.update') }}" class="max-w-form">
        @csrf
        @method('PUT')

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <x-ui.section title="Firma" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Firmenname" name="company_name" required :value="$d['company_name'] ?? ''" placeholder="Ihre Plattform GmbH" />

                <div class="flex flex-wrap gap-x-4 gap-y-5">
                    <x-ui.field label="Straße und Hausnummer" name="company_address" required :value="$d['company_address'] ?? ''"
                        placeholder="Musterstraße 123" class="min-w-0 flex-[3_1_200px]" />
                    <x-ui.field label="PLZ" name="company_postal_code" required :value="$d['company_postal_code'] ?? ''"
                        placeholder="12345" class="max-w-40 flex-[1_1_112px]" />
                </div>

                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="Stadt" name="company_city" required :value="$d['company_city'] ?? ''" placeholder="Berlin" />
                    <x-ui.field label="Land" name="company_country" required :value="$d['company_country'] ?? 'Deutschland'" />
                    <x-ui.field label="Steuernummer" name="tax_id" optional :value="$d['tax_id'] ?? ''" placeholder="12345/67890" />
                    <x-ui.field label="USt-IdNr." name="vat_id" optional :value="$d['vat_id'] ?? ''" placeholder="DE123456789" />
                    <x-ui.field label="E-Mail" name="company_email" type="email" required :value="$d['company_email'] ?? ''" placeholder="info@ihre-plattform.de" />
                    <x-ui.field label="Telefon" name="company_phone" required :value="$d['company_phone'] ?? ''" placeholder="+49 30 12345678" />
                </div>
            </div>
        </x-ui.section>

        <x-ui.section title="Bankverbindung" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Bankname" name="bank_name" required :value="$d['bank_name'] ?? ''" placeholder="Sparkasse Berlin" />
                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="IBAN" name="bank_iban" required :value="$d['bank_iban'] ?? ''" placeholder="DE89 3704 0044 0532 0130 00" />
                    <x-ui.field label="BIC/SWIFT" name="bank_bic" required :value="$d['bank_bic'] ?? ''" placeholder="COBADEFFXXX" />
                </div>
            </div>
        </x-ui.section>

        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Rechnungsdaten speichern</x-ui.button>
        </div>
    </form>

    <x-ui.alert tone="info" title="Verwendung der Rechnungsdaten" class="mt-8 max-w-form">
        Diese Daten werden als Absender auf allen Rechnungen über Plattformgebühren an Organisatoren angezeigt. Die Bankverbindung ist für die Zahlung der Gebühren erforderlich.
    </x-ui.alert>
</x-layouts.app>
