{{-- Status und Zahlung einer Buchung (Liste und Karten) --}}
<x-ui.status :tone="\App\Support\Ui::bookingTone($booking->status)">{{ $booking->statusLabel() }}</x-ui.status>
@if (!$booking->isFree() && $booking->status !== 'cancelled' && !($booking->status === 'pending' && $booking->payment_status === 'pending' && $booking->payment_method !== 'paypal'))
    <x-ui.status :tone="\App\Support\Ui::paymentTone($booking->payment_status)">
        {{ $booking->paymentStatusLabel() }}@if ($booking->payment_method === 'paypal') · PayPal @endif
    </x-ui.status>
@endif
