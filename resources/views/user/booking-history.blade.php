<x-layouts.app title="Meine Buchungen">
    <x-ui.page-header title="Meine Buchungen" subtitle="Übersicht aller Ihrer Fortbildungs-Buchungen." />

    @if ($bookings->count() > 0)
        <x-ui.row-list>
            @foreach ($bookings as $booking)
                @continue(!$booking->event)
                @php
                    $event = $booking->event;
                    $isReady = $booking->isReadyForParticipation();
                    $hint = match (true) {
                        $booking->status === 'cancelled' => null,
                        $booking->status === 'pending_approval' => ['Wartet auf Freigabe durch den Veranstalter', 'text-warning'],
                        !$booking->isPaymentComplete() && !$booking->isFree() => ['Zahlung offen – Details ansehen', 'text-warning'],
                        $booking->canBePersonalized() && !$booking->tickets_personalized => ['Bitte Teilnehmende eintragen', 'text-warning'],
                        default => null,
                    };
                    $badge = match (true) {
                        $booking->status === 'cancelled' => ['Storniert', 'danger'],
                        $booking->status === 'pending_approval' => ['Freigabe ausstehend', 'warning'],
                        $isReady && $event->end_date?->isPast() => ['Teilgenommen', 'neutral'],
                        $isReady => ['Bestätigt', 'success'],
                        default => ['Zahlung offen', 'warning'],
                    };
                    $quantity = $booking->items->sum('quantity');
                    $showOnline = $isReady && $booking->canAccessOnlineContent() && $event->online_url && !$event->end_date?->isPast();
                    $showTicket = $isReady && $booking->hasTicketDocument() && $booking->canSendTickets() && !$event->end_date?->isPast();
                @endphp
                <x-ui.event-row :event="$event" :href="route('bookings.show', $booking->booking_number)">
                    <span class="text-small text-muted tabular-nums">Buchung {{ $booking->booking_number }} · {{ $quantity }} {{ $quantity === 1 ? 'Platz' : 'Plätze' }}</span>
                    @if ($hint)
                        <span class="text-small font-medium {{ $hint[1] }}">{{ $hint[0] }}</span>
                    @endif
                    <x-slot:aside>
                        @unless ($booking->isFree())
                            <span class="text-base font-semibold whitespace-nowrap">{{ $booking->total > 0 ? \App\Support\Ui::money($booking->total) : 'kostenfrei' }}</span>
                        @endunless
                        <x-ui.status :tone="$badge[1]">{{ $badge[0] }}</x-ui.status>
                    </x-slot:aside>
                    <x-slot:action>
                        @if ($showOnline)
                            <x-ui.button :href="$event->online_url" target="_blank" rel="noopener" full>Zur Online-Veranstaltung</x-ui.button>
                        @endif
                        @if ($showTicket)
                            <x-ui.button variant="secondary" :href="route('bookings.ticket', $booking->booking_number)" full>Ticket (PDF)</x-ui.button>
                        @endif
                        @unless ($showOnline || $showTicket)
                            <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)" full>Buchung ansehen</x-ui.button>
                        @endunless
                    </x-slot:action>
                </x-ui.event-row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @else
        <x-ui.empty title="Noch keine Buchungen" :action-href="route('events.index')" action-label="Veranstaltungen entdecken" action-variant="primary">
            Entdecken Sie Fortbildungen und sichern Sie sich Ihren Platz.
        </x-ui.empty>
    @endif
</x-layouts.app>
