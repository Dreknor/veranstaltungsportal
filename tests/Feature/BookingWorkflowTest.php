<?php

/**
 * Prozessregeln rund um Buchungen: Wer bekommt wann welche E-Mail?
 * Siehe App\Services\BookingWorkflowService.
 */

use App\Mail\BookingCancellation;
use App\Mail\BookingConfirmation;
use App\Mail\EventCancelledMail;
use App\Mail\EventReminderMail;
use App\Mail\PaymentConfirmed;
use App\Models\Booking;
use App\Models\BookingEmailLog;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\TicketType;
use App\Notifications\BookingStatusChangedNotification;
use App\Notifications\EventReminderNotification;
use App\Notifications\EventUpdatedNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

function workflowSetup(array $eventAttributes = [], array $orgAttributes = []): array
{
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization([], $orgAttributes);

    $event = Event::factory()->create(array_merge([
        'organization_id' => $organization->id,
        'is_published' => true,
        'start_date' => now()->addWeeks(2),
        'end_date' => now()->addWeeks(2)->addHours(3),
        'max_attendees' => 50,
        'requires_ticket' => true,
    ], $eventAttributes));

    $ticketType = TicketType::factory()->create([
        'event_id' => $event->id,
        'price' => 40,
        'quantity' => 50,
        'quantity_sold' => 0,
        'sale_start' => null,
        'sale_end' => null,
        'min_per_order' => 1,
        'max_per_order' => 10,
    ]);

    return compact('organizer', 'organization', 'event', 'ticketType');
}

function workflowBooking(Event $event, TicketType $ticketType, array $attributes = [], int $items = 1): Booking
{
    $booking = Booking::factory()->create(array_merge([
        'event_id' => $event->id,
        'user_id' => null,
        'customer_email' => 'kunde@example.com',
        'customer_name' => 'Kim Kunde',
        'subtotal' => 40 * $items,
        'discount' => 0,
        'total' => 40 * $items,
        'status' => 'pending',
        'payment_status' => 'pending',
        'payment_method' => 'invoice',
        'confirmed_at' => null,
        'created_at' => now()->subDays(3),
    ], $attributes));

    for ($i = 0; $i < $items; $i++) {
        BookingItem::create([
            'booking_id' => $booking->id,
            'ticket_type_id' => $ticketType->id,
            'price' => $ticketType->price,
            'quantity' => 1,
            'attendee_name' => $items === 1 ? $booking->customer_name : null,
            'attendee_email' => $items === 1 ? $booking->customer_email : null,
        ]);
    }
    $ticketType->increment('quantity_sold', $items);
    if ($items === 1) {
        $booking->update(['tickets_personalized' => true, 'tickets_personalized_at' => now()]);
    }

    return $booking->fresh();
}

// ─── Zahlung ────────────────────────────────────────────────────────────────

test('Zahlungseingang bestätigt die Buchung und versendet genau einmal Tickets', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] = workflowSetup();
    $booking = workflowBooking($event, $ticketType);

    $this->actingAs($organizer)
        ->put(route('organizer.bookings.update-payment', $booking), ['payment_status' => 'paid'])
        ->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('confirmed')
        ->and($booking->payment_status)->toBe('paid')
        ->and($booking->confirmed_at)->not->toBeNull();

    Mail::assertSent(PaymentConfirmed::class, 1);

    // Erneutes Setzen löst keinen zweiten Versand aus
    $this->actingAs($organizer)
        ->put(route('organizer.bookings.update-payment', $booking), ['payment_status' => 'paid']);
    Mail::assertSent(PaymentConfirmed::class, 1);
});

test('Veranstalter kann "Extern fakturiert" setzen – Buchung wird bestätigt und Tickets versendet', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] =
        workflowSetup([], ['invoice_mode' => 'external']);
    $booking = workflowBooking($event, $ticketType);

    $this->actingAs($organizer)
        ->put(route('organizer.billing-data.mark-invoiced', $booking), ['external_invoice_number' => 'RE-1']);

    $booking->refresh();
    expect($booking->status)->toBe('confirmed')
        ->and($booking->payment_status)->toBe('extern')
        ->and($booking->externally_invoiced)->toBeTrue()
        ->and($booking->external_invoice_number)->toBe('RE-1');
    Mail::assertSent(PaymentConfirmed::class, 1);
});

test('Sammel-Fakturierung versendet Tickets für jede Buchung', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] =
        workflowSetup([], ['invoice_mode' => 'external']);
    $a = workflowBooking($event, $ticketType, ['customer_email' => 'a@example.com']);
    $b = workflowBooking($event, $ticketType, ['customer_email' => 'b@example.com']);

    $this->actingAs($organizer)
        ->put(route('organizer.billing-data.bulk-mark-invoiced'), ['booking_ids' => [$a->id, $b->id]]);

    expect($a->fresh()->status)->toBe('confirmed')->and($b->fresh()->status)->toBe('confirmed');
    // Sammelaktion: Versand über die Queue
    Mail::assertQueued(PaymentConfirmed::class, 2);
});

// ─── Stornierung ────────────────────────────────────────────────────────────

test('Gast ohne verifizierten Zugriff kann fremde Buchung nicht stornieren', function () {
    Mail::fake();
    ['event' => $event, 'ticketType' => $ticketType] = workflowSetup();
    $booking = workflowBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid']);

    $this->post(route('bookings.cancel', $booking->booking_number))->assertForbidden();

    expect($booking->fresh()->status)->toBe('confirmed');
    Mail::assertNothingSent();
});

test('Kunde storniert: eine Bestätigung, Kontingent frei, keine Doppel-Stornierung', function () {
    Mail::fake();
    Notification::fake();
    ['event' => $event, 'ticketType' => $ticketType] = workflowSetup();
    $booking = workflowBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid']);
    expect($ticketType->fresh()->quantity_sold)->toBe(1);

    $this->withSession(['booking_access_' . $booking->id => true])
        ->post(route('bookings.cancel', $booking->booking_number))
        ->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('cancelled')
        ->and($booking->cancelled_by)->toBe('customer')
        ->and($ticketType->fresh()->quantity_sold)->toBe(0);
    Mail::assertSent(BookingCancellation::class, 1);

    // Zweiter Versuch ändert nichts
    $this->withSession(['booking_access_' . $booking->id => true])
        ->post(route('bookings.cancel', $booking->booking_number))
        ->assertSessionHas('error');
    expect($ticketType->fresh()->quantity_sold)->toBe(0);
    Mail::assertSent(BookingCancellation::class, 1);
});

test('Absage einer Veranstaltung informiert jede aktive Buchung genau einmal', function () {
    Mail::fake();
    Notification::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] = workflowSetup();
    $user = createUser(['email' => 'registriert@example.com']);

    $confirmed = workflowBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid', 'user_id' => $user->id, 'customer_email' => $user->email]);
    $unpaid = workflowBooking($event, $ticketType, ['customer_email' => 'offen@example.com']);
    $approval = workflowBooking($event, $ticketType, ['status' => 'pending_approval', 'total' => 0, 'customer_email' => 'frei@example.com']);
    $alreadyCancelled = workflowBooking($event, $ticketType, ['status' => 'cancelled', 'customer_email' => 'weg@example.com']);

    $this->actingAs($organizer)
        ->post(route('organizer.events.cancel', $event), ['cancellation_reason' => 'Referent erkrankt'])
        ->assertSessionHas('success');

    foreach ([$confirmed, $unpaid, $approval] as $booking) {
        expect($booking->fresh()->status)->toBe('cancelled')
            ->and($booking->fresh()->cancelled_by)->toBe('event');
    }
    expect($alreadyCancelled->fresh()->cancelled_by)->toBeNull();

    Mail::assertQueued(EventCancelledMail::class, 3);
    Mail::assertNotQueued(EventCancelledMail::class, fn ($mail) => $mail->hasTo('weg@example.com'));
    // keine zusätzliche generische Statusmeldung
    Notification::assertNotSentTo($user, BookingStatusChangedNotification::class);
    expect($ticketType->fresh()->quantity_sold)->toBe(1); // 3 von 4 Plätzen freigegeben (die 4. war schon vorher storniert)
});

test('Veranstalter storniert mit Grund – Kunde erhält Stornobestätigung mit Grund', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] = workflowSetup();
    $booking = workflowBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid']);

    $this->actingAs($organizer)
        ->post(route('organizer.bookings.cancel', $booking), ['cancellation_reason' => 'Doppelbuchung', 'notify_customer' => '1'])
        ->assertSessionHas('success');

    expect($booking->fresh()->cancelled_by)->toBe('organizer')
        ->and($booking->fresh()->cancellation_reason)->toBe('Doppelbuchung');
    Mail::assertSent(BookingCancellation::class, fn ($mail) => $mail->render() !== '' && str_contains($mail->render(), 'Doppelbuchung'));
});

// ─── Erinnerungen ───────────────────────────────────────────────────────────

test('Erinnerung erreicht Gäste mit Online-Zugangsdaten und wird nur einmal versendet', function () {
    Mail::fake();
    ['event' => $event, 'ticketType' => $ticketType] = workflowSetup([
        'event_type' => 'online',
        'online_url' => 'https://meet.example.com/raum-42',
        'online_access_code' => 'GEHEIM',
        'requires_ticket' => false,
        'start_date' => now()->addHours(20),
        'end_date' => now()->addHours(22),
    ]);
    $booking = workflowBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid', 'confirmed_at' => now()->subDays(3)]);

    $this->artisan('events:send-reminders')->assertSuccessful();

    Mail::assertQueued(EventReminderMail::class, function ($mail) use ($booking) {
        $html = $mail->render();
        return $mail->hasTo($booking->customer_email)
            && str_contains($html, 'https://meet.example.com/raum-42')
            && str_contains($html, 'GEHEIM')
            && str_contains($html, $booking->booking_number);
    });
    expect($booking->fresh()->reminderWasSent('24'))->toBeTrue();

    $this->artisan('events:send-reminders')->assertSuccessful();
    Mail::assertQueued(EventReminderMail::class, 1);
});

test('Erinnerung wird nicht für unbezahlte oder gerade erst gebuchte Plätze versendet', function () {
    Mail::fake();
    ['event' => $event, 'ticketType' => $ticketType] = workflowSetup([
        'start_date' => now()->addHours(20),
        'end_date' => now()->addHours(22),
    ]);
    workflowBooking($event, $ticketType, ['customer_email' => 'offen@example.com']);
    workflowBooking($event, $ticketType, [
        'customer_email' => 'frisch@example.com',
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'created_at' => now()->subHours(2),
        'confirmed_at' => now()->subHours(2),
    ]);

    $this->artisan('events:send-reminders')->assertSuccessful();

    Mail::assertNothingQueued();
});

test('Registrierte Nutzer erhalten die Erinnerung als Benachrichtigung, personalisierte Teilnehmende per Mail', function () {
    Mail::fake();
    Notification::fake();
    ['event' => $event, 'ticketType' => $ticketType] = workflowSetup([
        'start_date' => now()->addHours(2),
        'end_date' => now()->addHours(4),
    ]);
    $user = createUser();
    $booking = workflowBooking($event, $ticketType, [
        'status' => 'confirmed',
        'payment_status' => 'paid',
        'user_id' => $user->id,
        'customer_email' => $user->email,
        'confirmed_at' => now()->subDays(3),
    ], 2);
    $booking->items()->first()->update(['attendee_name' => 'Tea Teilnehmerin', 'attendee_email' => 'tea@example.com']);

    $this->artisan('events:send-reminders')->assertSuccessful();

    Notification::assertSentTo($user, EventReminderNotification::class);
    Mail::assertQueued(EventReminderMail::class, fn ($mail) => $mail->hasTo('tea@example.com') && $mail->isAttendee);
    // 3-h-Erinnerung verschickt → 24-h-Erinnerung gilt als erledigt
    expect($booking->fresh()->reminders_sent)->toHaveKeys(['3', '24']);
});

// ─── Änderungen an der Veranstaltung ────────────────────────────────────────

test('Neuer Zugangslink wird nur an Buchungen mit freigeschaltetem Zugang versendet – auch an Gäste', function () {
    Notification::fake();
    ['event' => $event, 'ticketType' => $ticketType] = workflowSetup([
        'event_type' => 'online',
        'online_url' => 'https://alt.example.com',
        'requires_ticket' => false,
    ]);
    workflowBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid', 'customer_email' => 'bezahlt@example.com']);
    workflowBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'pending', 'customer_email' => 'offen@example.com']);

    $event->update(['online_url' => 'https://neu.example.com']);

    Notification::assertSentOnDemand(EventUpdatedNotification::class, function ($notification, $channels, $notifiable) {
        return $notifiable->routes['mail'] === 'bezahlt@example.com'
            && str_contains(implode("\n", $notification->toMail($notifiable)->introLines), 'https://neu.example.com');
    });
    Notification::assertSentOnDemandTimes(EventUpdatedNotification::class, 1);
});

// ─── Personalisierung ───────────────────────────────────────────────────────

test('Nach der Personalisierung einer bezahlten Online-Buchung werden die Zugangsdaten versendet', function () {
    Mail::fake();
    ['event' => $event, 'ticketType' => $ticketType] = workflowSetup([
        'event_type' => 'online',
        'online_url' => 'https://meet.example.com',
        'requires_ticket' => false,
    ]);
    $booking = workflowBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid'], 2);
    $items = $booking->items;

    $this->withSession(['booking_access_' . $booking->id => true])
        ->post(route('bookings.save-personalization', $booking->booking_number), [
            'attendees' => [
                $items[0]->id => ['attendee_name' => 'A', 'attendee_email' => 'a@example.com'],
                $items[1]->id => ['attendee_name' => 'B', 'attendee_email' => 'b@example.com'],
            ],
        ])->assertRedirect(route('bookings.show', $booking->booking_number));

    Mail::assertSent(PaymentConfirmed::class, 1);
});

// ─── Buchungsvorgang ────────────────────────────────────────────────────────

test('Gast sieht nach der Buchung sofort seine Buchung (ohne erneute Verifizierung)', function () {
    Mail::fake();
    ['event' => $event, 'ticketType' => $ticketType] = workflowSetup();

    $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyRecaptcha::class)
        ->post(route('bookings.store', $event), [
            'customer_name' => 'Gerda Gast',
            'customer_email' => 'gerda@example.com',
            'billing_address' => 'Weg 1',
            'billing_postal_code' => '01067',
            'billing_city' => 'Dresden',
            'billing_country' => 'Deutschland',
            'privacy_accepted' => '1',
            'tickets' => [['ticket_type_id' => $ticketType->id, 'quantity' => 1]],
        ]);

    $booking = Booking::where('customer_email', 'gerda@example.com')->firstOrFail();
    $response->assertRedirect(route('bookings.show', $booking->booking_number));

    $this->get(route('bookings.show', $booking->booking_number))
        ->assertOk()
        ->assertSee('Nächster Schritt')
        ->assertSee('Bitte überweisen Sie');

    Mail::assertSent(BookingConfirmation::class, 1);
});

test('Persönlicher Link aus der E-Mail öffnet die Buchung auch mehrfach', function () {
    ['event' => $event, 'ticketType' => $ticketType] = workflowSetup();
    $booking = workflowBooking($event, $ticketType, ['email_verification_token' => 'tok-123']);

    $this->get($booking->manageUrl())->assertRedirect(route('bookings.show', $booking->booking_number));
    $this->get(route('bookings.show', $booking->booking_number))->assertOk();

    // Neue Sitzung, gleicher Link
    $this->flushSession();
    $this->get($booking->manageUrl())->assertRedirect(route('bookings.show', $booking->booking_number));
    expect($booking->fresh()->email_verification_token)->toBe('tok-123');
});

test('Nicht abgeschlossene PayPal-Buchungen werden nach Ablauf freigegeben', function () {
    Mail::fake();
    ['event' => $event, 'ticketType' => $ticketType] = workflowSetup();
    $old = workflowBooking($event, $ticketType, ['payment_method' => 'paypal', 'created_at' => now()->subHours(50)]);
    $fresh = workflowBooking($event, $ticketType, ['payment_method' => 'paypal', 'created_at' => now()->subHour(), 'customer_email' => 'neu@example.com']);

    $this->artisan('bookings:release-abandoned')->assertSuccessful();

    expect($old->fresh()->status)->toBe('cancelled')
        ->and($old->fresh()->cancelled_by)->toBe('system')
        ->and($fresh->fresh()->status)->toBe('pending')
        ->and($ticketType->fresh()->quantity_sold)->toBe(1);
    Mail::assertSent(BookingCancellation::class, 1);
});

// ─── E-Mail-Verlauf ─────────────────────────────────────────────────────────

test('Versendete Buchungs-E-Mails werden im Verlauf protokolliert und dem Veranstalter angezeigt', function () {
    config(['mail.default' => 'array']);
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] = workflowSetup();
    $booking = workflowBooking($event, $ticketType);

    Mail::to($booking->customer_email)->send(new BookingConfirmation($booking->load('items.ticketType', 'event.organization')));

    $log = BookingEmailLog::where('booking_id', $booking->id)->sole();
    expect($log->type)->toBe('BookingConfirmation')
        ->and($log->recipient)->toBe('kunde@example.com');

    $this->actingAs($organizer)
        ->get(route('organizer.bookings.show', $booking))
        ->assertOk()
        ->assertSee('E-Mail-Verlauf')
        ->assertSee('Buchungsbestätigung');
});

test('Veranstalter kann die aktuelle Bestätigung erneut senden', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'ticketType' => $ticketType] = workflowSetup();
    $booking = workflowBooking($event, $ticketType, ['status' => 'confirmed', 'payment_status' => 'paid']);

    $this->actingAs($organizer)
        ->post(route('organizer.bookings.resend', $booking))
        ->assertSessionHas('success');

    Mail::assertSent(PaymentConfirmed::class, 1);
});
