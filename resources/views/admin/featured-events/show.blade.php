<x-layouts.app title="Hervorgehobene Veranstaltung">
    @php
        $payTone = ['paid' => 'success', 'pending' => 'warning', 'failed' => 'danger'];
        $payLabel = [
            'paid' => 'Bezahlt',
            'pending' => 'Offen',
            'failed' => 'Fehlgeschlagen',
            'refunded' => 'Erstattet',
        ];
        $endDate = \Carbon\Carbon::parse($fee->featured_end_date);
        $startDate = \Carbon\Carbon::parse($fee->featured_start_date);
        $daysLeft = (int) now()
            ->startOfDay()
            ->diffInDays($endDate->copy()->startOfDay(), false);
    @endphp

    <x-ui.page-header title="Hervorgehobene Veranstaltung" :back-href="route('admin.featured-events.index')" back-label="Zur Übersicht" />

    <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,368px)] lg:gap-x-12">
        <div class="flex min-w-0 flex-col gap-10">
            <!-- Event Info -->
            <x-ui.section title="Veranstaltung" level="h2">
                <x-ui.dl>
                    <x-ui.dl-item label="Veranstaltung">
                        <a href="{{ route('events.show', $fee->event->slug) }}" target="_blank" rel="noopener"
                            class="inline-flex items-center gap-2 font-medium text-ink underline underline-offset-[3px]">
                            {{ $fee->event->title }}
                            <x-icon.external-link class="h-4 w-4" />
                        </a>
                    </x-ui.dl-item>
                    <x-ui.dl-item label="Veranstalter">
                        {{ $fee->event->organizer->name }}
                        <span class="block text-small text-muted">{{ $fee->event->organizer->email }}</span>
                    </x-ui.dl-item>
                    <x-ui.dl-item label="Beginn"
                        num>{{ $fee->event->start_date?->format('d.m.Y H:i') ?? '–' }}</x-ui.dl-item>
                    <x-ui.dl-item label="Status der Veranstaltung">
                        <span class="inline-flex flex-wrap gap-x-6 gap-y-1">
                            <x-ui.status
                                :tone="$fee->event->is_published ? 'success' : 'neutral'">{{ $fee->event->is_published ? 'Veröffentlicht' : 'Entwurf' }}</x-ui.status>
                            @if ($fee->event->is_featured)
                                <x-ui.status tone="info">Hervorgehoben</x-ui.status>
                            @endif
                        </span>
                    </x-ui.dl-item>
                </x-ui.dl>
            </x-ui.section>

            <!-- Fee Details -->
            <x-ui.section title="Gebührendetails" level="h2">
                <x-ui.dl>
                    <x-ui.dl-item label="Betrag" num><span
                            class="text-h3">{{ \App\Support\Ui::money($fee->fee_amount) }}</span></x-ui.dl-item>
                    <x-ui.dl-item label="Zeitraum" num>
                        {{ (int) $startDate->diffInDays($endDate) }}
                        {{ (int) $startDate->diffInDays($endDate) === 1 ? 'Tag' : 'Tage' }}
                        <span class="block text-small text-muted">{{ $startDate->format('d.m.Y') }} –
                            {{ $endDate->format('d.m.Y') }}</span>
                    </x-ui.dl-item>
                    <x-ui.dl-item label="Erstellt am" num>{{ $fee->created_at->format('d.m.Y H:i') }}</x-ui.dl-item>
                    <x-ui.dl-item label="Läuft ab am" num>
                        {{ $endDate->format('d.m.Y') }}
                        @if ($endDate->isPast())
                            <span class="block text-small text-danger">Abgelaufen</span>
                        @elseif ($daysLeft <= 3)
                            <span class="block text-small text-warning">Läuft in {{ $daysLeft }}
                                {{ $daysLeft === 1 ? 'Tag' : 'Tagen' }} ab</span>
                        @else
                            <span class="block text-small text-muted">Noch {{ $daysLeft }}
                                {{ $daysLeft === 1 ? 'Tag' : 'Tage' }}</span>
                        @endif
                    </x-ui.dl-item>
                    @if ($fee->paid_at)
                        <x-ui.dl-item label="Bezahlt am" num>{{ $fee->paid_at->format('d.m.Y H:i') }}</x-ui.dl-item>
                    @endif
                    @if ($fee->expiry_notified_at)
                        <x-ui.dl-item label="Ablauf-Benachrichtigung gesendet"
                            num>{{ $fee->expiry_notified_at->format('d.m.Y H:i') }}</x-ui.dl-item>
                    @endif
                </x-ui.dl>
            </x-ui.section>

            <!-- Extend Period -->
            @if ($fee->payment_status === 'paid')
                <x-ui.section title="Zeitraum verlängern" level="h2">
                    <form method="POST" action="{{ route('admin.featured-events.extend', $fee) }}" class="max-w-form">
                        @csrf
                        <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-x-4 gap-y-5">
                            <x-ui.select label="Zeitraum-Typ" name="duration_type" id="duration_type" required
                                :old="false" :options="['daily' => 'Täglich', 'weekly' => 'Wöchentlich', 'monthly' => 'Monatlich']" />
                            <x-ui.field label="Anzahl" name="duration_count" type="number" min="1"
                                max="365" value="1" required :old="false" />
                        </div>
                        <div class="mt-6">
                            <x-ui.button type="submit"
                                onclick="return confirm('Sind Sie sicher, dass Sie den Zeitraum verlängern möchten?')">Zeitraum
                                verlängern</x-ui.button>
                        </div>
                    </form>
                </x-ui.section>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="flex flex-col gap-8">
            <!-- Status -->
            <section aria-labelledby="payment-status">
                <h2 id="payment-status" class="m-0 mb-4 text-h3">Zahlungsstatus</h2>
                <p class="m-0 mb-4">
                    <x-ui.status
                        :tone="$payTone[$fee->payment_status] ?? 'neutral'">{{ $payLabel[$fee->payment_status] ?? ucfirst($fee->payment_status) }}</x-ui.status>
                </p>

                <form method="POST" action="{{ route('admin.featured-events.update-status', $fee) }}"
                    class="flex flex-col gap-5">
                    @csrf
                    <x-ui.select label="Status ändern" name="payment_status" id="payment_status" :old="false"
                        :selected="$fee->payment_status" :options="[
                            'pending' => 'Offen',
                            'paid' => 'Bezahlt',
                            'failed' => 'Fehlgeschlagen',
                            'refunded' => 'Erstattet',
                        ]" />
                    <div>
                        <x-ui.button type="submit" full
                            onclick="return confirm('Sind Sie sicher, dass Sie den Status ändern möchten?')">Status
                            aktualisieren</x-ui.button>
                    </div>
                </form>
            </section>

            <!-- Actions -->
            <section aria-labelledby="fee-actions" class="border-t border-line pt-8">
                <h2 id="fee-actions" class="m-0 mb-4 text-h3">Aktionen</h2>

                <div class="flex flex-col gap-3">
                    @if ($fee->payment_status === 'pending')
                        <form method="POST" action="{{ route('admin.featured-events.send-reminder', $fee) }}"
                            class="m-0">
                            @csrf
                            <x-ui.button type="submit" variant="secondary" full>
                                <x-slot:icon><x-icon.mail /></x-slot:icon>
                                Zahlungserinnerung senden
                            </x-ui.button>
                        </form>
                    @endif

                    @if ($fee->event?->organization)
                        <x-ui.button variant="secondary" full :href="route('admin.organizations.show', $fee->event->organization)">Veranstalter-Profil</x-ui.button>
                    @endif

                    @if ($fee->payment_status !== 'paid')
                        <form method="POST" action="{{ route('admin.featured-events.cancel', $fee) }}" class="m-0">
                            @csrf
                            <x-ui.button type="submit" variant="danger" full
                                onclick="return confirm('Sind Sie sicher, dass Sie diese Gebühr stornieren möchten?')">Gebühr
                                stornieren</x-ui.button>
                        </form>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
