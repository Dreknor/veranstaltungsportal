<?php

if (!function_exists('format_currency')) {
    /**
     * Format price to currency
     */
    function format_currency(float $amount, string $currency = '€'): string
    {
        return number_format($amount, 2, ',', '.') . ' ' . $currency;
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date in German format
     */
    function format_date($date, string $format = 'd.m.Y'): string
    {
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }
        return $date->format($format);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime in German format
     */
    function format_datetime($date, string $format = 'd.m.Y H:i'): string
    {
        return format_date($date, $format);
    }
}

if (!function_exists('booking_status_badge')) {
    /**
     * Get badge HTML for booking status
     */
    function booking_status_badge(string $status): string
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Ausstehend</span>',
            'confirmed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Bestätigt</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Storniert</span>',
            'completed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Abgeschlossen</span>',
        ];

        return $badges[$status] ?? $status;
    }
}

if (!function_exists('payment_status_badge')) {
    /**
     * Get badge HTML for payment status
     */
    function payment_status_badge(string $status): string
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Ausstehend</span>',
            'paid' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Bezahlt</span>',
            'refunded' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Erstattet</span>',
            'failed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Fehlgeschlagen</span>',
        ];

        return $badges[$status] ?? $status;
    }
}

if (!function_exists('generate_ticket_code')) {
    /**
     * Generate unique ticket code
     */
    function generate_ticket_code(): string
    {
        return 'TKT-' . strtoupper(\Illuminate\Support\Str::random(12));
    }
}

if (!function_exists('generate_booking_number')) {
    /**
     * Generate unique booking number
     */
    function generate_booking_number(): string
    {
        return 'BK-' . strtoupper(\Illuminate\Support\Str::random(10));
    }
}

if (!function_exists('event_status')) {
    /**
     * Get event status (upcoming, ongoing, past)
     */
    function event_status(\App\Models\Event $event): string
    {
        $now = now();

        if ($event->end_date < $now) {
            return 'past';
        }

        if ($event->start_date <= $now && $event->end_date >= $now) {
            return 'ongoing';
        }

        return 'upcoming';
    }
}

if (!function_exists('can_book_event')) {
    /**
     * Check if event can be booked
     */
    function can_book_event(\App\Models\Event $event): bool
    {
        if (!$event->is_published) {
            return false;
        }

        if ($event->start_date->isPast()) {
            return false;
        }

        $availableTickets = $event->ticketTypes()
            ->where('is_available', true)
            ->get()
            ->filter(fn($ticket) => $ticket->isOnSale() && $ticket->availableQuantity() > 0);

        return $availableTickets->isNotEmpty();
    }
}

