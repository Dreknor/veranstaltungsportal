<x-layouts.app title="Vergangene Veranstaltungen">
    <x-ui.page-header title="Vergangene Veranstaltungen" subtitle="Ihre besuchten Fortbildungen." />

    @if ($bookings->count() > 0)
        <x-ui.row-list>
            @foreach ($bookings as $booking)
                @php
                    $quantity = $booking->items->sum('quantity');
                    $userReview = $booking->event->reviews()->where('user_id', auth()->id())->first();
                @endphp
                <x-ui.event-row :event="$booking->event" :href="route('bookings.show', $booking->booking_number)">
                    <span class="text-small text-muted tabular-nums">{{ $quantity }} {{ $quantity === 1 ? 'Ticket' : 'Tickets' }}</span>
                    <x-slot:aside>
                        <span class="text-base font-semibold whitespace-nowrap">{{ $booking->total > 0 ? \App\Support\Ui::money($booking->total) : 'kostenfrei' }}</span>
                        @if ($userReview)
                            <x-ui.status tone="success">Bewertet</x-ui.status>
                        @else
                            <x-ui.status tone="neutral">Beendet</x-ui.status>
                        @endif
                    </x-slot:aside>
                    <x-slot:action>
                        @unless ($userReview)
                            <x-ui.button variant="secondary" :href="route('events.show', $booking->event->slug) . '#reviews'" full>Bewerten</x-ui.button>
                        @endunless
                        <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)" full>Buchung ansehen</x-ui.button>
                        <x-ui.button variant="link" size="sm" :href="route('bookings.invoice', $booking->booking_number)">Rechnung</x-ui.button>
                    </x-slot:action>
                </x-ui.event-row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
    @else
        <x-ui.empty title="Noch keine besuchten Veranstaltungen" :action-href="route('events.index')" action-label="Fortbildungen entdecken" action-variant="primary">
            Ihre abgeschlossenen Fortbildungen erscheinen hier.
        </x-ui.empty>
    @endif
</x-layouts.app>
