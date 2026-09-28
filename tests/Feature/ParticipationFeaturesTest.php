<?php

/**
 * Tickets vor Rechnungsstellung, persönliche Teilnehmer-Tickets, Nachbereitung,
 * Wartelisten-Reservierung, Mehrfachtermine, Teilnehmer-Nachrichten und DSGVO-Anonymisierung.
 */

use App\Mail\AttendeeMessageMail;
use App\Mail\AttendeeTicketMail;
use App\Mail\BookingConfirmation;
use App\Mail\EventFollowUpMail;
use App\Mail\EventReminderMail;
use App\Mail\PaymentConfirmed;
use App\Mail\WaitlistTicketAvailable;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\EventDate;
use App\Models\EventReview;
use App\Models\EventWaitlist;
use App\Models\TicketType;
use Illuminate\Support\Facades\Mail;

function pfSetup(array $eventAttributes = [], array $orgAttributes = []): array
{
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization([], $orgAttributes);

    $event = Event::factory()->create(array_merge([
        'organization_id' => $organization->id,
        'is_published' => true,
        'start_date' => now()->addWeeks(2),
        'end_date' => now()->addWeeks(2)->addHours(3),
        'max_attendees' => 10,
        'requires_ticket' => true,
    ], $eventAttributes));

    $ticketType = TicketType::factory()->create([
        'event_id' => $event->id, 'price' => 50, 'quantity' => null, 'quantity_sold' => 0,
        'sale_start' => null, 'sale_end' => null, 'min_per_order' => 1, 'max_per_order' => 10,
    ]);

    return compact('organizer', 'organization', 'event', 'ticketType');
}

function pfBooking(Event $event, TicketType $ticketType, array $attributes = [], array $attendees = [null]): Booking
{
    $booking = Booking::factory()->create(array_merge([
        'event_id' => $event->id,
        'user_id' => null,
        'customer_name' => 'Susanne Schulleitung',
        'customer_email' => 'schulleitung@example.com',
        'subtotal' => 50 * count($attendees),
        'discount' => 0,
        'total' => 50 * count($attendees),
        'status' => 'pending',
        'payment_status' => 'pending',
        'payment_method' => 'invoice',
        'confirmed_at' => null,
        'created_at' => now()->subDays(3),
        'tickets_personalized' => true,
    ], $attributes));

    foreach ($attendees as $attendee) {
        BookingItem::create([
            'booking_id' => $booking->id,
            'ticket_type_id' => $ticketType->id,
            'price' => $ticketType->price,
            'quantity' => 1,
            'attendee_name' => $attendee[0] ?? $booking->customer_name,
            'attendee_email' => $attendee[1] ?? $booking->customer_email,
        ]);
    }

    return $booking->fresh();
}

function pfBookingData(TicketType $ticketType, int $quantity = 1): array
{
    return [
        'customer_name' => 'Bea Bucherin',
        'customer_email' => 'bea@example.com',
        'billing_address' => 'Weg 1',
        'billing_postal_code' => '01067',
        'billing_city' => 'Dresden',
        'billing_country' => 'Germany',
        'privacy_accepted' => '1',
        'tickets' => [['ticket_type_id' => $ticketType->id, 'quantity' => $quantity]],
    ];
}

// ─── Tickets vor Rechnungsstellung ──────────────────────────────────────────

test('Event-Einstellung: bei externer Fakturierung werden Tickets sofort versendet, Rechnung bleibt offen', function () {
    Mail::fake();
    ['event' => $event, 'ticketType' => $ticketType] = pfSetup(['tickets_before_invoice' => true], ['invoice_mode' => 'external']);

    $this->withoutMiddleware(\App\Http\Middleware\VerifyRecaptcha::class)
        ->post(route('bookings.store', $event), pfBookingData($ticketType))
        ->assertRedirect();

    $booking = Booking::where('customer_email', 'bea@example.com')->sole();
    expect($booking->status)->toBe('confirmed')
        ->and($booking->payment_status)->toBe('pending')
        ->and($booking->isReadyForParticipation())->toBeTrue()
        ->and($booking->ticketsReleasedBeforePayment())->toBeTrue()
        ->and($booking->canSendTickets())->toBeTrue();

    Mail::assertSent(BookingConfirmation::class, fn ($mail) => count($mail->attachments()) === 1);

    // Später fakturiert: keine zweite Ticket-Mail
    ['organizer' => $organizer] = ['organizer' => $event->organization->users()->first()];
    $this->actingAs($organizer)->put(route('organizer.billing-data.mark-invoiced', $booking), ['external_invoice_number' => 'R-9']);
    expect($booking->fresh()->payment_status)->toBe('extern');
    Mail::assertNotSent(PaymentConfirmed::class);
});

test('Ohne Event-Einstellung wartet die Buchung auf die Rechnungsstellung', function () {
    Mail::fake();
    ['event' => $event, 'ticketType' => $ticketType] = pfSetup(['tickets_before_invoice' => false], ['invoice_mode' => 'external']);

    $this->withoutMiddleware(\App\Http\Middleware\VerifyRecaptcha::class)
        ->post(route('bookings.store', $event), pfBookingData($ticketType));

    $booking = Booking::where('customer_email', 'bea@example.com')->sole();
    expect($booking->status)->toBe('pending')->and($booking->isReadyForParticipation())->toBeFalse();
});

test('Veranstalter gibt Tickets für eine einzelne Buchung vorab frei', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] = pfSetup([], ['invoice_mode' => 'external']);
    $booking = pfBooking($event, $ticketType);

    $this->actingAs($organizer)
        ->post(route('organizer.bookings.release-tickets', $booking))
        ->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('confirmed')
        ->and($booking->release_tickets_before_payment)->toBeTrue()
        ->and($booking->payment_status)->toBe('pending');
    Mail::assertSent(PaymentConfirmed::class, 1);

    // Check-in ist möglich, obwohl noch nicht fakturiert
    $this->actingAs($organizer)->get(route('organizer.check-in.index', $event))
        ->assertOk()->assertSee($booking->customer_name);
});

test('PayPal-Buchungen können nicht vorab freigegeben werden', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] = pfSetup();
    $booking = pfBooking($event, $ticketType, ['payment_method' => 'paypal']);

    $this->actingAs($organizer)->post(route('organizer.bookings.release-tickets', $booking))->assertSessionHas('error');
    expect($booking->fresh()->status)->toBe('pending');
});

// ─── Persönliche Tickets für Teilnehmende ───────────────────────────────────

test('Eingetragene Teilnehmende erhalten ihr eigenes Ticket genau einmal', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] = pfSetup();
    $booking = pfBooking($event, $ticketType, [], [
        ['Susanne Schulleitung', 'schulleitung@example.com'],
        ['Lars Lehrer', 'lars@example.com'],
        ['Lena Lehrerin', 'lena@example.com'],
    ]);

    $this->actingAs($organizer)->put(route('organizer.bookings.update-payment', $booking), ['payment_status' => 'paid']);

    Mail::assertSent(PaymentConfirmed::class, 1);
    Mail::assertSent(AttendeeTicketMail::class, 2);
    Mail::assertSent(AttendeeTicketMail::class, fn ($mail) => $mail->hasTo('lars@example.com') && count($mail->attachments()) === 1);
    expect(BookingItem::where('attendee_email', 'lena@example.com')->value('ticket_sent_to'))->toBe('lena@example.com');

    // Adresse ändern → nur diese Person erhält erneut ein Ticket
    $item = $booking->items()->where('attendee_email', 'lena@example.com')->first();
    $item->update(['attendee_email' => 'lena.neu@example.com']);
    app(\App\Services\BookingWorkflowService::class)->deliverAttendeeTickets($booking->fresh(['items', 'event']));
    Mail::assertSent(AttendeeTicketMail::class, 3);
    Mail::assertSent(AttendeeTicketMail::class, fn ($mail) => $mail->hasTo('lena.neu@example.com'));
});

// ─── Nachbereitung ──────────────────────────────────────────────────────────

test('Nach der Veranstaltung gibt es Bescheinigungen und Feedback-Anfrage – einmalig', function () {
    Mail::fake();
    ['event' => $event, 'ticketType' => $ticketType] = pfSetup([
        'start_date' => now()->subDay()->subHours(3),
        'end_date' => now()->subDay(),
    ]);
    $booking = pfBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid'], [
        ['Susanne Schulleitung', 'schulleitung@example.com'],
        ['Lars Lehrer', 'lars@example.com'],
    ]);
    $booking->items()->update(['checked_in' => true, 'checked_in_at' => now()->subDay()]);

    $this->artisan('events:send-follow-ups')->assertSuccessful();

    Mail::assertQueued(EventFollowUpMail::class, fn ($mail) => $mail->hasTo('schulleitung@example.com') && $mail->item === null);
    Mail::assertQueued(EventFollowUpMail::class, fn ($mail) => $mail->hasTo('lars@example.com') && $mail->item !== null);
    expect($booking->fresh()->follow_up_sent_at)->not->toBeNull();

    $this->artisan('events:send-follow-ups')->assertSuccessful();
    Mail::assertQueued(EventFollowUpMail::class, 2);
});

test('Gäste können über ihre Buchung ein Feedback abgeben', function () {
    \Illuminate\Support\Facades\Notification::fake();
    ['event' => $event, 'ticketType' => $ticketType] = pfSetup([
        'start_date' => now()->subDays(2),
        'end_date' => now()->subDays(2)->addHours(3),
    ]);
    $booking = pfBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid']);

    $this->withSession(['booking_access_' . $booking->id => true])
        ->post(route('bookings.feedback', $booking->booking_number), ['rating' => 5, 'comment' => 'Sehr praxisnah'])
        ->assertRedirect();

    $review = EventReview::sole();
    expect($review->user_id)->toBeNull()
        ->and($review->booking_id)->toBe($booking->id)
        ->and($review->reviewerName())->toBe('Susanne Schulleitung')
        ->and($review->is_approved)->toBeFalse();

    // kein zweites Feedback
    $this->withSession(['booking_access_' . $booking->id => true])
        ->post(route('bookings.feedback', $booking->booking_number), ['rating' => 1]);
    expect(EventReview::count())->toBe(1);
});

// ─── Warteliste mit Reservierung ────────────────────────────────────────────

test('Frei werdende Plätze werden für die nächste Person reserviert und sind für andere gesperrt', function () {
    Mail::fake();
    ['event' => $event, 'ticketType' => $ticketType] = pfSetup(['max_attendees' => 2]);
    $booking = pfBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid']);
    pfBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid', 'customer_email' => 'zwei@example.com']);

    $entry = EventWaitlist::create(['event_id' => $event->id, 'email' => 'warte@example.com', 'name' => 'Walter Warte', 'quantity' => 1, 'status' => 'waiting']);

    app(\App\Services\BookingWorkflowService::class)->cancel($booking, 'organizer');

    $entry->refresh();
    expect($entry->status)->toBe('notified')->and($entry->claim_token)->not->toBeNull();
    Mail::assertSent(WaitlistTicketAvailable::class, fn ($mail) => $mail->hasTo('warte@example.com'));

    // Für alle anderen ausgebucht …
    expect($event->fresh()->availableTickets())->toBe(0);

    // … aber über den persönlichen Link buchbar
    $this->get($entry->claimUrl())->assertOk()->assertSee('Für Sie reserviert');

    $data = pfBookingData($ticketType);
    $data['customer_email'] = 'warte@example.com';
    $data['waitlist_token'] = $entry->claim_token;
    $this->withoutMiddleware(\App\Http\Middleware\VerifyRecaptcha::class)
        ->post(route('bookings.store', $event), $data)
        ->assertRedirect();

    expect(Booking::where('customer_email', 'warte@example.com')->exists())->toBeTrue()
        ->and($entry->fresh()->status)->toBe('converted');
});

test('Abgelaufene Reservierungen gehen automatisch an die nächste Person', function () {
    Mail::fake();
    ['event' => $event] = pfSetup(['max_attendees' => 1]);
    $first = EventWaitlist::create(['event_id' => $event->id, 'email' => 'a@example.com', 'name' => 'A', 'quantity' => 1, 'status' => 'waiting', 'created_at' => now()->subDays(3)]);
    $second = EventWaitlist::create(['event_id' => $event->id, 'email' => 'b@example.com', 'name' => 'B', 'quantity' => 1, 'status' => 'waiting']);

    $first->markAsNotified();
    $first->update(['expires_at' => now()->subMinute()]);

    $this->artisan('waitlist:clean-expired')->assertSuccessful();

    expect($first->fresh()->status)->toBe('expired')
        ->and($second->fresh()->status)->toBe('notified');
    Mail::assertSent(WaitlistTicketAvailable::class, fn ($mail) => $mail->hasTo('b@example.com'));
});

// ─── Mehrfachtermine ────────────────────────────────────────────────────────

test('Check-in und Erinnerungen erfolgen je Termin', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] = pfSetup([
        'has_multiple_dates' => true,
        'start_date' => now()->addHours(20),
        'end_date' => now()->addHours(22),
    ]);
    $first = EventDate::create(['event_id' => $event->id, 'start_date' => now()->addHours(20), 'end_date' => now()->addHours(22)]);
    $second = EventDate::create(['event_id' => $event->id, 'start_date' => now()->addDays(8), 'end_date' => now()->addDays(8)->addHours(2)]);
    $booking = pfBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid', 'confirmed_at' => now()->subDays(3)]);
    $item = $booking->items()->first();

    // Check-in für Termin 1
    $this->actingAs($organizer)
        ->post(route('organizer.check-in.item.store', [$event, $item]), ['event_date_id' => $first->id])
        ->assertSessionHas('status');
    expect($item->fresh()->isCheckedInFor($first))->toBeTrue()
        ->and($item->fresh()->isCheckedInFor($second))->toBeFalse()
        ->and($item->fresh()->checked_in)->toBeTrue();

    // Termin 2 ist noch offen
    $this->actingAs($organizer)->get(route('organizer.check-in.index', [$event, 'date' => $second->id]))
        ->assertOk()->assertViewHas('stats', fn ($stats) => $stats['checked_in'] === 0);

    // Erinnerung für Termin 1 (Schlüssel je Termin)
    $this->artisan('events:send-reminders')->assertSuccessful();
    Mail::assertQueued(EventReminderMail::class, fn ($mail) => $mail->session?->id === $first->id);
    expect($booking->fresh()->reminderWasSent("d{$first->id}:24"))->toBeTrue()
        ->and($booking->fresh()->reminderWasSent("d{$second->id}:24"))->toBeFalse();
});

// ─── Nachrichten an Teilnehmende ────────────────────────────────────────────

test('Nachricht an eine Zielgruppe wird über die Queue versendet', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] = pfSetup();
    pfBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid', 'customer_email' => 'bezahlt@example.com'], [
        ['Bea Bezahlt', 'bezahlt@example.com'], ['Tom Team', 'tom@example.com'],
    ]);
    pfBooking($event, $ticketType, ['customer_email' => 'offen@example.com']);

    $this->actingAs($organizer)
        ->post(route('organizer.events.attendees.contact.send', $event), [
            'segment' => 'unpaid', 'include_attendees' => '0', 'subject' => 'Zahlungserinnerung', 'message' => 'Bitte überweisen.',
        ])->assertSessionHas('success');
    Mail::assertQueued(AttendeeMessageMail::class, 1);
    Mail::assertQueued(AttendeeMessageMail::class, fn ($mail) => $mail->hasTo('offen@example.com'));

    $this->actingAs($organizer)
        ->post(route('organizer.events.attendees.contact.send', $event), [
            'segment' => 'ready', 'include_attendees' => '1', 'subject' => 'Raumänderung', 'message' => 'Raum 12.',
        ]);
    Mail::assertQueued(AttendeeMessageMail::class, fn ($mail) => $mail->hasTo('tom@example.com'));
    Mail::assertQueued(AttendeeMessageMail::class, 3);
});

// ─── DSGVO ──────────────────────────────────────────────────────────────────

test('Personenbezogene Daten werden nach Fristablauf anonymisiert, Rechnungsdaten erst nach 10 Jahren', function () {
    ['event' => $event, 'ticketType' => $ticketType] = pfSetup([
        'start_date' => now()->subMonths(30),
        'end_date' => now()->subMonths(30)->addHours(3),
    ]);
    $paid = pfBooking($event, $ticketType, [
        'status' => 'confirmed', 'payment_status' => 'paid', 'invoice_number' => 'RE-1', 'invoice_date' => now()->subMonths(30),
        'customer_phone' => '0351 1', 'billing_city' => 'Dresden',
    ], [['Susanne Schulleitung', 'schulleitung@example.com'], ['Lars Lehrer', 'lars@example.com']]);
    $free = pfBooking($event, $ticketType, ['total' => 0, 'subtotal' => 0, 'status' => 'confirmed', 'payment_status' => 'paid', 'customer_email' => 'frei@example.com']);

    $this->artisan('privacy:anonymize-bookings')->assertSuccessful();

    $paid->refresh();
    expect($paid->anonymized_at)->not->toBeNull()
        ->and($paid->customer_email)->toEndWith('@invalid.local')
        ->and($paid->customer_phone)->toBeNull()
        ->and($paid->customer_name)->toBe('Susanne Schulleitung') // Rechnung: Aufbewahrungspflicht
        ->and($paid->billing_city)->toBe('Dresden')
        ->and($paid->items()->whereNotNull('attendee_name')->count())->toBe(0);

    $free->refresh();
    expect($free->customer_name)->toBe('Anonymisiert')->and($free->billing_city)->toBeNull();

    // Nach Ablauf der steuerlichen Frist
    $paid->forceFill(['invoice_date' => now()->subYears(11)])->saveQuietly();
    $this->artisan('privacy:anonymize-bookings')->assertSuccessful();
    expect($paid->fresh()->customer_name)->toBe('Anonymisiert')->and($paid->fresh()->billing_city)->toBeNull();
});

// ─── Dashboard ──────────────────────────────────────────────────────────────

test('Dashboard zeigt offene Aufgaben', function () {
    ['organizer' => $organizer, 'organization' => $organization, 'event' => $event, 'ticketType' => $ticketType] = pfSetup([], ['invoice_mode' => 'external']);
    pfBooking($event, $ticketType, ['status' => 'pending_approval', 'total' => 0, 'customer_email' => 'x@example.com']);
    pfBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'pending', 'release_tickets_before_payment' => true]);

    $this->actingAs($organizer)
        ->withSession(['current_organization_id' => $organization->id])
        ->get(route('organizer.dashboard'))
        ->assertOk()
        ->assertSee('warten auf Ihre Freigabe')
        ->assertSee('noch nicht fakturiert');
});

// ─── Alle E-Mails rendern fehlerfrei ────────────────────────────────────────

test('Neue und geänderte E-Mails rendern mit den erwarteten Inhalten', function () {
    ['event' => $event, 'ticketType' => $ticketType] = pfSetup([
        'event_type' => 'hybrid', 'online_url' => 'https://meet.example.com/x', 'has_multiple_dates' => true,
        'tickets_before_invoice' => true,
    ], ['invoice_mode' => 'external']);
    $date = EventDate::create(['event_id' => $event->id, 'start_date' => now()->addDays(3), 'end_date' => now()->addDays(3)->addHours(2)]);
    $booking = pfBooking($event, $ticketType, ['status' => 'confirmed'], [
        ['Susanne Schulleitung', 'schulleitung@example.com'], ['Lars Lehrer', 'lars@example.com'],
    ])->load('items', 'event.organization');
    $item = $booking->items->last();
    $item->update(['checked_in' => true]);
    $entry = EventWaitlist::create(['event_id' => $event->id, 'email' => 'w@example.com', 'name' => 'W', 'quantity' => 1, 'status' => 'waiting']);
    $entry->markAsNotified();

    expect((new AttendeeTicketMail($booking, $item))->render())->toContain('Lars Lehrer')->toContain('https://meet.example.com/x');
    expect((new PaymentConfirmed($booking))->render())->toContain('Rechnung folgt separat');
    expect((new BookingConfirmation($booking))->render())->toContain('auch erst nach der Veranstaltung');
    expect((new EventReminderMail($event, $booking, null, false, $date))->render())->toContain('Termin 1 von 1')->toContain('https://meet.example.com/x');
    $event->forceFill(['start_date' => now()->subDays(2), 'end_date' => now()->subDays(2)->addHours(3)])->saveQuietly();
    $booking = $booking->fresh(['items', 'event.organization']);
    $item = $booking->items->last();
    expect((new EventFollowUpMail($booking))->render())->toContain('Feedback geben');
    expect((new EventFollowUpMail($booking, $item))->render())->not->toContain('Feedback geben');
    expect((new AttendeeMessageMail($booking, 'Betreff', "Zeile 1\nZeile 2"))->render())->toContain('Zeile 1<br />');
    expect((new WaitlistTicketAvailable($entry->fresh()))->render())->toContain($entry->fresh()->claim_token);
});
