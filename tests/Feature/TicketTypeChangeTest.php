<?php

/**
 * Veranstalter ändert die Ticketart einzelner Positionen (z. B. Fehlbuchung einer eingeschränkten Ticketart).
 * Siehe App\Services\BookingWorkflowService::changeTicketType().
 */

use App\Mail\AttendeeTicketMail;
use App\Mail\BookingConfirmation;
use App\Mail\TicketTypeChanged;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function ttcSetup(array $orgAttributes = []): array
{
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization([], $orgAttributes);

    $event = Event::factory()->create([
        'organization_id' => $organization->id,
        'is_published' => true,
        'start_date' => now()->addWeeks(2),
        'end_date' => now()->addWeeks(2)->addHours(3),
        'max_attendees' => 100,
        'requires_ticket' => true,
    ]);

    $make = fn (string $name, float $price, array $attributes = []) => TicketType::factory()->create(array_merge([
        'event_id' => $event->id,
        'name' => $name,
        'price' => $price,
        'quantity' => 50,
        'quantity_sold' => 0,
        'sale_start' => null,
        'sale_end' => null,
        'is_available' => true,
        'min_per_order' => 1,
        'max_per_order' => 10,
    ], $attributes));

    $member = $make('Mitglieder', 20);
    $regular = $make('Regulär', 50);

    return compact('organizer', 'organization', 'event', 'member', 'regular', 'make');
}

function ttcBooking(Event $event, TicketType $ticketType, array $attributes = [], int $items = 1): Booking
{
    $total = $ticketType->price * $items;
    $booking = Booking::factory()->create(array_merge([
        'event_id' => $event->id,
        'user_id' => null,
        'customer_email' => 'kunde@example.com',
        'customer_name' => 'Kim Kunde',
        'subtotal' => $total,
        'discount' => 0,
        'total' => $total,
        'status' => 'pending',
        'payment_status' => 'pending',
        'payment_method' => 'invoice',
        'invoice_number' => null,
        'confirmed_at' => null,
        'tickets_personalized' => true,
    ], $attributes));

    for ($i = 0; $i < $items; $i++) {
        BookingItem::create([
            'booking_id' => $booking->id,
            'ticket_type_id' => $ticketType->id,
            'price' => $ticketType->price,
            'quantity' => 1,
            'attendee_name' => "Person {$i}",
        ]);
    }
    $ticketType->increment('quantity_sold', $items);

    return $booking->fresh('items');
}

function ttcChange(User $organizer, Booking $booking, TicketType $target, array $data = [])
{
    return test()->actingAs($organizer)->put(route('organizer.bookings.change-ticket-type', $booking), array_merge([
        'items' => $booking->items->pluck('id')->all(),
        'ticket_type_id' => $target->id,
        'reason' => 'Mitgliedertarif gilt nur für Vereinsmitglieder.',
    ], $data));
}

test('unbezahlte Buchung erhält neue Ticketart, neuen Preis und umgebuchte Kontingente', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'regular' => $regular] = ttcSetup();
    $booking = ttcBooking($event, $member, [], 2);
    $ticketNumbers = $booking->items->pluck('ticket_number')->all();

    ttcChange($organizer, $booking, $regular)->assertSessionHas('success');

    $booking->refresh()->load('items');
    expect($booking->items->pluck('ticket_type_id')->unique()->all())->toBe([$regular->id])
        ->and($booking->items->pluck('ticket_number')->all())->toBe($ticketNumbers)
        ->and((float) $booking->items->first()->price)->toBe(50.0)
        ->and((float) $booking->subtotal)->toBe(100.0)
        ->and((float) $booking->total)->toBe(100.0)
        ->and($member->fresh()->quantity_sold)->toBe(0)
        ->and($regular->fresh()->quantity_sold)->toBe(2);

    expect(AuditLog::where('action', 'booking.ticket_type_changed')->where('auditable_id', $booking->id)->count())->toBe(2);
    Mail::assertSent(TicketTypeChanged::class, fn ($mail) => $mail->hasTo('kunde@example.com') && count($mail->changes) === 2);
});

test('nur ausgewählte Positionen werden geändert', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'regular' => $regular] = ttcSetup();
    $booking = ttcBooking($event, $member, [], 2);
    $first = $booking->items->first();

    ttcChange($organizer, $booking, $regular, ['items' => [$first->id]])->assertSessionHas('success');

    $booking->refresh()->load('items');
    expect($booking->items->firstWhere('id', $first->id)->ticket_type_id)->toBe($regular->id)
        ->and($booking->items->where('ticket_type_id', $member->id))->toHaveCount(1)
        ->and((float) $booking->total)->toBe(70.0);
});

test('bezahlte Buchung behält ihren Preis', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'regular' => $regular] = ttcSetup();
    $booking = ttcBooking($event, $member, ['status' => 'confirmed', 'payment_status' => 'paid', 'confirmed_at' => now()]);

    ttcChange($organizer, $booking, $regular)->assertSessionHas('success');

    $booking->refresh()->load('items');
    expect($booking->items->first()->ticket_type_id)->toBe($regular->id)
        ->and((float) $booking->items->first()->price)->toBe(20.0)
        ->and((float) $booking->total)->toBe(20.0);
    Mail::assertSent(TicketTypeChanged::class, fn ($mail) => $mail->priceLocked && count($mail->attachments()) === 1);
});

test('offene Buchung mit Rechnungsnummer erhält den neuen Preis und die korrigierte Rechnung', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'regular' => $regular] = ttcSetup();
    $booking = ttcBooking($event, $member);
    $booking->forceFill(['invoice_number' => 'RE-2026-1', 'invoice_date' => now()])->saveQuietly();

    ttcChange($organizer, $booking, $regular)->assertSessionHas('success');

    $booking->refresh();
    expect((float) $booking->total)->toBe(50.0)
        ->and($booking->invoice_number)->toBe('RE-2026-1');
    Mail::assertSent(TicketTypeChanged::class, function ($mail) {
        $names = collect($mail->attachments())->map(fn ($attachment) => $attachment->as)->all();

        return $names === ['Rechnung_RE-2026-1.pdf'];
    });
});

test('extern abgerechnete Buchung erhält den neuen Preis', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'regular' => $regular] =
        ttcSetup(['invoice_mode' => 'external']);
    $booking = ttcBooking($event, $member, [
        'status' => 'confirmed', 'payment_status' => 'extern', 'externally_invoiced' => true,
        'external_invoice_number' => 'EXT-7', 'confirmed_at' => now(),
    ]);

    ttcChange($organizer, $booking, $regular)->assertSessionHas('success');

    $booking->refresh();
    expect((float) $booking->total)->toBe(50.0)
        ->and($booking->payment_status)->toBe('extern')
        ->and($booking->external_invoice_number)->toBe('EXT-7');
});

test('Rabatt bleibt als fester Betrag und wird auf die neue Zwischensumme gedeckelt', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'regular' => $regular, 'make' => $make] = ttcSetup();
    $cheap = $make('Schüler', 10);
    $booking = ttcBooking($event, $regular, ['discount' => 15, 'total' => 35]);

    ttcChange($organizer, $booking, $cheap)->assertSessionHas('success');

    $booking->refresh();
    expect((float) $booking->subtotal)->toBe(10.0)
        ->and((float) $booking->discount)->toBe(10.0)
        ->and((float) $booking->total)->toBe(0.0);
});

test('aus kostenfreier wird kostenpflichtige Buchung: Zahlung per Rechnung, Tickets bleiben gültig', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'regular' => $regular, 'make' => $make] = ttcSetup();
    $free = $make('Ehrenamt', 0);
    $booking = ttcBooking($event, $free, ['status' => 'confirmed', 'payment_status' => 'paid', 'payment_method' => null, 'confirmed_at' => now()]);

    ttcChange($organizer, $booking, $regular)->assertSessionHas('success');

    $booking->refresh();
    expect((float) $booking->total)->toBe(50.0)
        ->and($booking->status)->toBe('confirmed')
        ->and($booking->payment_status)->toBe('pending')
        ->and($booking->payment_method)->toBe('invoice')
        ->and($booking->isReadyForParticipation())->toBeTrue();
    Mail::assertSent(TicketTypeChanged::class);
    Mail::assertSent(BookingConfirmation::class);
});

test('aus kostenpflichtiger wird kostenfreie Buchung: keine Zahlung mehr nötig', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'make' => $make] = ttcSetup();
    $free = $make('Ehrenamt', 0);
    $booking = ttcBooking($event, $member);

    ttcChange($organizer, $booking, $free)->assertSessionHas('success');

    $booking->refresh();
    expect($booking->isFree())->toBeTrue()
        ->and($booking->payment_status)->toBe('paid')
        ->and($booking->status)->toBe('confirmed')
        ->and($booking->isReadyForParticipation())->toBeTrue();
    Mail::assertSent(TicketTypeChanged::class, fn ($mail) => count($mail->attachments()) === 1);
});

test('ausverkaufte oder gesperrte Ticketart nur nach Bestätigung, dann wird überbucht', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'make' => $make] = ttcSetup();
    $soldOut = $make('Sonderkontingent', 20, ['quantity' => 1, 'quantity_sold' => 1, 'is_available' => false]);
    $booking = ttcBooking($event, $member);

    ttcChange($organizer, $booking, $soldOut)->assertSessionHasErrors('confirm_override');
    expect($booking->items()->first()->ticket_type_id)->toBe($member->id);

    ttcChange($organizer, $booking, $soldOut, ['confirm_override' => '1'])->assertSessionHas('success');
    expect($booking->items()->first()->ticket_type_id)->toBe($soldOut->id)
        ->and($soldOut->fresh()->quantity_sold)->toBe(2);
});

test('Teilnehmende mit eigener E-Mail-Adresse erhalten ihr neues Ticket', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'regular' => $regular] = ttcSetup();
    $booking = ttcBooking($event, $member, ['status' => 'confirmed', 'payment_status' => 'paid', 'confirmed_at' => now()], 2);
    $booking->items->first()->update(['attendee_email' => 'person@example.com']);

    ttcChange($organizer, $booking, $regular)->assertSessionHas('success');

    Mail::assertSent(AttendeeTicketMail::class, fn ($mail) => $mail->hasTo('person@example.com'));
    Mail::assertSent(AttendeeTicketMail::class, 1);
});

test('Begründung ist Pflicht', function () {
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'regular' => $regular] = ttcSetup();
    $booking = ttcBooking($event, $member);

    ttcChange($organizer, $booking, $regular, ['reason' => ''])->assertSessionHasErrors('reason');
});

test('stornierte und abgeschlossene Buchungen können nicht geändert werden', function (string $status) {
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'regular' => $regular] = ttcSetup();
    $booking = ttcBooking($event, $member, ['status' => $status]);

    ttcChange($organizer, $booking, $regular)->assertSessionHas('error');
    expect($booking->items()->first()->ticket_type_id)->toBe($member->id);
})->with(['cancelled', 'completed']);

test('Ticketart einer anderen Veranstaltung wird abgelehnt', function () {
    ['organizer' => $organizer, 'event' => $event, 'member' => $member] = ttcSetup();
    $foreign = TicketType::factory()->create(['event_id' => Event::factory()->create()->id]);
    $booking = ttcBooking($event, $member);

    ttcChange($organizer, $booking, $foreign)->assertSessionHasErrors('ticket_type_id');
});

test('Mitglieder ohne Owner-/Admin-Rolle dürfen die Ticketart nicht ändern', function () {
    ['organization' => $organization, 'event' => $event, 'member' => $member, 'regular' => $regular] = ttcSetup();
    $teamMember = User::factory()->create();
    $organization->users()->attach($teamMember->id, ['role' => 'member', 'is_active' => true, 'joined_at' => now()]);
    $booking = ttcBooking($event, $member);

    ttcChange($teamMember, $booking, $regular)->assertForbidden();
});

test('Buchungsdetails zeigen den Dialog und die protokollierten Änderungen', function () {
    Mail::fake();
    ['organizer' => $organizer, 'event' => $event, 'member' => $member, 'regular' => $regular] = ttcSetup();
    $booking = ttcBooking($event, $member);

    $this->actingAs($organizer)->get(route('organizer.bookings.show', $booking))
        ->assertOk()
        ->assertSee('Ticketart ändern');

    ttcChange($organizer, $booking, $regular);

    $this->actingAs($organizer)->get(route('organizer.bookings.show', $booking))
        ->assertOk()
        ->assertSee('Änderungen an der Buchung')
        ->assertSee('Mitgliedertarif gilt nur für Vereinsmitglieder.');
});
