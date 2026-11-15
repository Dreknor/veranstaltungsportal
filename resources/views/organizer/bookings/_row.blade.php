<tr>
    <td class="whitespace-nowrap">
        <a href="{{ route('organizer.bookings.show', $booking) }}" class="font-medium tabular-nums text-ink underline underline-offset-[3px]">{{ $booking->booking_number }}</a>
        <span class="block text-small text-muted tabular-nums">{{ $booking->created_at->format('d.m.Y') }}</span>
    </td>
    @if (!empty($showEvent))
        <td>
            {{ Str::limit($booking->event->title, 30) }}
            <span class="block text-small text-muted tabular-nums">{{ $booking->event->start_date->format('d.m.Y') }}</span>
        </td>
    @endif
    <td>
        {{ $booking->customer_name }}
        <span class="block text-small text-muted">{{ $booking->customer_email }}</span>
        @if ($booking->customer_organization && $booking->event->showsOrganizationField())
            <span class="block text-small font-medium text-ink-soft">{{ $booking->customer_organization }}</span>
        @endif
    </td>
    <td class="num">{{ $booking->items->sum('quantity') }}</td>
    <td class="num">{{ $booking->isFree() ? 'kostenfrei' : \App\Support\Ui::money($booking->total) }}</td>
    <td>
        <div class="flex flex-col items-start gap-1">
            @include('organizer.bookings._badges', ['booking' => $booking])
        </div>
    </td>
    @if ($organization->hasExternalInvoicing())
        <td>
            @if ($booking->total > 0)
                @if ($booking->externally_invoiced)
                    <x-ui.status tone="success">Fakturiert</x-ui.status>
                @else
                    <x-ui.status tone="warning">Offen</x-ui.status>
                @endif
            @endif
        </td>
    @endif
    <td class="actions">
        @if ($booking->status === 'pending_approval')
            <form method="POST" action="{{ route('organizer.bookings.approve', $booking) }}" class="inline-block"
                  onsubmit="return confirm('Anmeldung von {{ addslashes($booking->customer_name) }} bestätigen? Die Person erhält sofort ihre Bestätigung.')">
                @csrf
                <x-ui.button type="submit" size="sm">Bestätigen</x-ui.button>
            </form>
        @endif
        <x-ui.button size="sm" variant="secondary" :href="route('organizer.bookings.show', $booking)">Details</x-ui.button>
    </td>
</tr>
