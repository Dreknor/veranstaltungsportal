{{-- Mobile Darstellung einer Buchung --}}
<li class="p-4">
    <div class="flex items-start justify-between gap-3">
        <a href="{{ route('organizer.bookings.show', $booking) }}" class="min-w-0 flex-1">
            <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $booking->customer_name }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $booking->customer_email }}</p>
            @if(!empty($showEvent))
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 truncate">{{ $booking->event->title }} · {{ $booking->event->start_date->format('d.m.Y') }}</p>
            @endif
        </a>
        <div class="text-right shrink-0">
            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $booking->isFree() ? 'frei' : number_format($booking->total, 2, ',', '.') . ' €' }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->items->sum('quantity') }} Platz/Plätze</p>
        </div>
    </div>
    <div class="mt-2 flex flex-wrap items-center gap-1.5">
        @include('organizer.bookings._badges', ['booking' => $booking])
        @if($organization->hasExternalInvoicing() && $booking->total > 0 && !$booking->externally_invoiced && $booking->status !== 'cancelled')
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">nicht fakturiert</span>
        @endif
    </div>
    <div class="mt-3 flex gap-2">
        @if($booking->status === 'pending_approval')
            <form method="POST" action="{{ route('organizer.bookings.approve', $booking) }}" class="flex-1"
                  onsubmit="return confirm('Anmeldung von {{ addslashes($booking->customer_name) }} bestätigen?')">
                @csrf
                <button type="submit" class="w-full px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg">✓ Bestätigen</button>
            </form>
        @endif
        <a href="{{ route('organizer.bookings.show', $booking) }}" class="flex-1 text-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-lg">Details</a>
    </div>
</li>
