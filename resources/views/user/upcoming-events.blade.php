<x-layouts.app title="Anstehende Veranstaltungen">
    <x-ui.page-header title="Anstehende Veranstaltungen" subtitle="Ihre bevorstehenden Fortbildungen." />

    @if ($bookings->count() > 0)
        <x-ui.row-list>
            @foreach ($bookings as $booking)
                @php
                    $quantity = $booking->items->sum('quantity');
                    // Ganze Kalendertage bis zum Beginn (Carbon 3 liefert sonst Bruchteile)
                    $daysUntil = (int) now()->startOfDay()->diffInDays($booking->event->start_date->copy()->startOfDay(), false);
                @endphp
                <x-ui.event-row :event="$booking->event" :href="route('bookings.show', $booking->booking_number)">
                    <span class="text-small text-muted tabular-nums">{{ $quantity }} {{ $quantity === 1 ? 'Ticket' : 'Tickets' }}</span>
                    @if ($daysUntil >= 0)
                        <span class="text-small font-medium text-ink-soft">
                            @if ($daysUntil === 0)
                                Heute
                            @elseif ($daysUntil === 1)
                                Morgen
                            @else
                                In {{ $daysUntil }} Tagen
                            @endif
                        </span>
                    @endif
                    <x-slot:aside>
                        <x-ui.status tone="success">Bestätigt</x-ui.status>
                    </x-slot:aside>
                    <x-slot:action>
                        <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)" full>Buchung ansehen</x-ui.button>
                        <x-ui.button variant="link" size="sm" :href="route('bookings.ticket', $booking->booking_number)">Ticket herunterladen</x-ui.button>
                    </x-slot:action>
                </x-ui.event-row>
            @endforeach
        </x-ui.row-list>

        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
    @else
        <x-ui.empty title="Keine anstehenden Veranstaltungen" :action-href="route('events.index')" action-label="Fortbildungen entdecken" action-variant="primary">
            Buchen Sie jetzt Ihre nächste Fortbildung.
        </x-ui.empty>
    @endif
</x-layouts.app>
