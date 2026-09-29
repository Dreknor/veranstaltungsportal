<?php

use App\Mail\PaymentConfirmed;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\TicketType;

function pcBooking(array $eventAttributes, array $bookingAttributes, int $items = 1, bool $personalized = true): Booking
{
    ['organization' => $organization] = createOrganizerWithOrganization();
    $event = Event::factory()->create(array_merge([
        'organization_id' => $organization->id,
        'is_published' => true,
        'start_date' => now()->addWeeks(2),
        'end_date' => now()->addWeeks(2)->addHours(3),
        'cancellation_allowed' => true,
        'cancellation_days_before' => 7,
    ], $eventAttributes));
    $ticketType = TicketType::factory()->create(['event_id' => $event->id, 'price' => $bookingAttributes['total'] ?? 50]);

    $booking = Booking::factory()->create(array_merge([
        'event_id' => $event->id,
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'payment_method' => 'invoice',
        'subtotal' => 50,
        'discount' => 0,
        'total' => 50,
        'tickets_personalized' => $personalized,
    ], $bookingAttributes));

    for ($i = 0; $i < $items; $i++) {
        BookingItem::create(['booking_id' => $booking->id, 'ticket_type_id' => $ticketType->id, 'price' => $ticketType->price, 'quantity' => 1]);
    }

    return $booking->fresh(['items.ticketType', 'event.organization']);
}

test('HTML-Struktur der Zahlungsbestätigung ist vollständig geschlossen', function () {
    $html = (new PaymentConfirmed(pcBooking(['event_type' => 'physical', 'requires_ticket' => true], [])))->render();

    expect(substr_count($html, '<table'))->toBe(substr_count($html, '</table>'))
        ->and(substr_count($html, '<tr'))->toBe(substr_count($html, '</tr>'))
        ->and(substr_count($html, '<td'))->toBe(substr_count($html, '</td>'))
        ->and(substr_count($html, '<div'))->toBe(substr_count($html, '</div>'));
});

test('Online-Veranstaltung mit mehreren Plätzen fordert zum Eintragen der Teilnehmenden auf', function () {
    $booking = pcBooking(
        ['event_type' => 'online', 'online_url' => 'https://meet.example.com/x', 'requires_ticket' => false],
        [],
        3,
        false
    );

    $html = (new PaymentConfirmed($booking))->render();

    expect($html)->toContain('Teilnehmende eintragen')
        ->toContain('Zugangsdaten direkt')
        ->toContain('https://meet.example.com/x');
});

test('Kostenfreie Anmeldung zeigt keine Beträge', function () {
    $html = (new PaymentConfirmed(pcBooking(['event_type' => 'physical'], ['total' => 0, 'subtotal' => 0])))->render();

    expect($html)->toContain('Anmeldung bestätigt')
        ->toContain('kostenfrei')
        ->not->toContain('Gesamtbetrag')
        ->not->toContain('0,00');
});

test('Abgelaufene Stornofrist wird nicht mehr angeboten', function () {
    $html = (new PaymentConfirmed(pcBooking([
        'start_date' => now()->addDays(3),
        'end_date' => now()->addDays(3)->addHours(2),
        'cancellation_days_before' => 7,
    ], [])))->render();

    expect($html)->not->toContain('Stornierung:');
});
