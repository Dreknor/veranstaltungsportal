{{-- Status- und Zahlungs-Badges einer Buchung (Liste & Karten) --}}
@php
    $statusClass = match ($booking->status) {
        'confirmed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'pending_approval' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        default => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    };
    $paymentClass = match ($booking->payment_status) {
        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'extern' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    };
@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">{{ $booking->statusLabel() }}</span>
@if(!$booking->isFree() && $booking->status !== 'cancelled' && !($booking->status === 'pending' && $booking->payment_status === 'pending' && $booking->payment_method !== 'paypal'))
    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $paymentClass }}">
        {{ $booking->paymentStatusLabel() }}@if($booking->payment_method === 'paypal') · PayPal @endif
    </span>
@endif
