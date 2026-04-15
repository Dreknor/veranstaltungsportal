<tr>
    <td class="px-4 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $booking->booking_number }}</div>
    </td>
    @if(!empty($showEvent))
    <td class="px-4 py-4">
        <div class="text-sm text-gray-900 dark:text-gray-100">{{ Str::limit($booking->event->title, 30) }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->event->start_date->format('d.m.Y') }}</div>
    </td>
    @endif
    <td class="px-4 py-4">
        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $booking->customer_name }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->customer_email }}</div>
        @if($booking->customer_organization && $booking->event->requiresOrganizationField())
            <div class="text-xs text-blue-600 dark:text-blue-400 mt-0.5 font-medium">{{ $booking->customer_organization }}</div>
        @endif
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
        {{ $booking->items->sum('quantity') }}
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
        {{ number_format($booking->total, 2) }} €
    </td>
    <td class="px-4 py-4 whitespace-nowrap">
        @if($booking->payment_status === 'paid')
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Bezahlt</span>
        @elseif($booking->payment_status === 'pending')
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Ausstehend</span>
        @elseif($booking->payment_status === 'failed')
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Fehlg.</span>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">{{ ucfirst($booking->payment_status) }}</span>
        @endif
        @if($booking->payment_method)
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                @if($booking->payment_method === 'paypal') PayPal
                @elseif($booking->payment_method === 'invoice') Rechnung
                @else {{ $booking->payment_method }}
                @endif
            </div>
        @endif
    </td>
    <td class="px-3 py-4 w-28">
        @if($booking->status === 'confirmed')
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">✓ Bestätigt</span>
        @elseif($booking->status === 'pending_approval')
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">⏳ Ausstehend</span>
        @elseif($booking->status === 'cancelled')
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">✕ Storniert</span>
        @elseif($booking->status === 'completed')
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Abgeschl.</span>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">{{ ucfirst($booking->status) }}</span>
        @endif
    </td>
    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
        {{ $booking->created_at->format('d.m.Y') }}
    </td>
    @if($organization->hasExternalInvoicing())
    <td class="px-3 py-4 text-center">
        @if($booking->total > 0)
            @if($booking->externally_invoiced)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200" title="Fakturiert">✓</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200" title="Noch nicht fakturiert">⏳</span>
            @endif
        @endif
    </td>
    @endif
    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
        <div class="flex items-center justify-end gap-2 flex-wrap">
            @if($booking->status === 'pending_approval')
                <form method="POST" action="{{ route('organizer.bookings.approve', $booking) }}"
                      onsubmit="return confirm('Buchung von {{ addslashes($booking->customer_name) }} wirklich bestätigen?')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-2.5 py-1.5 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition">
                        ✓ Bestätigen
                    </button>
                </form>
                <form method="POST" action="{{ route('organizer.bookings.reject', $booking) }}"
                      onsubmit="return confirm('Buchung von {{ addslashes($booking->customer_name) }} wirklich ablehnen?')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-2.5 py-1.5 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition">
                        ✕ Ablehnen
                    </button>
                </form>
            @endif
            <a href="{{ route('organizer.bookings.show', $booking) }}"
               class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400 text-xs font-medium rounded hover:bg-blue-100 dark:hover:bg-blue-800 transition border border-blue-200 dark:border-blue-700">
                Details
            </a>
        </div>
    </td>
</tr>

