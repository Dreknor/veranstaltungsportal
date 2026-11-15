{{-- Darstellung einer Buchung auf schmalen Bildschirmen --}}
<li class="flex flex-col gap-3 p-4">
    <div class="flex items-start justify-between gap-3">
        <a href="{{ route('organizer.bookings.show', $booking) }}" class="min-w-0 flex-1 text-ink no-underline">
            <span class="block truncate text-base font-semibold">{{ $booking->customer_name }}</span>
            <span class="block truncate text-small text-muted">{{ $booking->customer_email }}</span>
            @if (!empty($showEvent))
                <span class="mt-1 block truncate text-small text-ink-soft">{{ $booking->event->title }} · {{ $booking->event->start_date->format('d.m.Y') }}</span>
            @endif
        </a>
        <div class="shrink-0 text-right tabular-nums">
            <p class="m-0 text-base font-semibold">{{ $booking->isFree() ? 'kostenfrei' : \App\Support\Ui::money($booking->total) }}</p>
            <p class="m-0 text-small text-muted">{{ $booking->items->sum('quantity') }} Platz/Plätze</p>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
        @include('organizer.bookings._badges', ['booking' => $booking])
        @if ($organization->hasExternalInvoicing() && $booking->total > 0 && !$booking->externally_invoiced && $booking->status !== 'cancelled')
            <x-ui.status tone="warning">nicht fakturiert</x-ui.status>
        @endif
    </div>
    <div class="flex gap-2">
        @if ($booking->status === 'pending_approval')
            <form method="POST" action="{{ route('organizer.bookings.approve', $booking) }}" class="flex-1"
                  onsubmit="return confirm('Anmeldung von {{ addslashes($booking->customer_name) }} bestätigen?')">
                @csrf
                <x-ui.button type="submit" full>Bestätigen</x-ui.button>
            </form>
        @endif
        <x-ui.button variant="secondary" :href="route('organizer.bookings.show', $booking)" class="flex-1">Details</x-ui.button>
    </div>
</li>
