<x-layouts.app>
    <x-ui.page-header title="Zahlung für die Hervorhebung"
        subtitle="Bitte schließen Sie die Zahlung ab, um Ihre Veranstaltung hervorzuheben."
        :back-href="route('organizer.featured-events.history')" back-label="Zu den Hervorhebungen" />

    <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between lg:gap-x-12">
        <form method="POST" action="{{ route('organizer.featured-events.process-payment', $featuredEventFee) }}" class="flex flex-col gap-y-8">
            @csrf
            <p class="m-0 text-small text-muted">* Pflichtfeld</p>

            <x-ui.select label="Zahlungsmethode" name="payment_method" required :options="[
                'stripe' => 'Kreditkarte (Stripe)',
                'paypal' => 'PayPal',
                'invoice' => 'Rechnung',
                'bank_transfer' => 'Banküberweisung',
            ]" />

            <x-ui.field label="Referenz" name="payment_reference" optional placeholder="z. B. Transaktions-ID" />

            <div>
                <x-ui.button type="submit">Zahlung abschließen</x-ui.button>
            </div>
        </form>

        <x-ui.summary title="Ihre Hervorhebung" :sticky="false">
            <x-slot:event>
                <span>{{ $featuredEventFee->event->title }}</span>
                <span>Start: {{ $featuredEventFee->featured_start_date->format('d.m.Y') }}</span>
            </x-slot:event>
            <x-ui.summary-lines>
                <x-ui.summary-line label="Dauer" :amount="['daily' => '1 Tag', 'weekly' => '7 Tage', 'monthly' => '30 Tage', 'custom' => 'Individuell'][$featuredEventFee->duration_type] ?? ucfirst($featuredEventFee->duration_type)" />
            </x-ui.summary-lines>
            <x-ui.summary-total label="Betrag" :amount="\App\Support\Ui::money($featuredEventFee->amount)" />
        </x-ui.summary>
    </div>
</x-layouts.app>
