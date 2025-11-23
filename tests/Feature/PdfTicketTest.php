<?php

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Services\QrCodeService;
use App\Services\TicketPdfService;

test('can generate QR code for booking', function () {
    $booking = Booking::factory()
        ->for(Event::factory())
        ->create();

    $qrCodeService = app(QrCodeService::class);
    $qrCode = $qrCodeService->generateForBooking($booking);

    expect($qrCode)->toBeString();
    expect($qrCode)->toContain('svg');
});

test('can verify QR code data', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $booking = Booking::factory()->create(['event_id' => $event->id]);

    // Verify booking exists in database
    expect($booking->id)->toBeInt();
    expect(Booking::find($booking->id))->not->toBeNull();

    $qrCodeService = app(QrCodeService::class);

    // Generate the QR data
    $qrData = json_encode([
        'booking_id' => $booking->id,
        'reference' => $booking->booking_number,
        'event_id' => $booking->event_id,
    ]);

    // Verify the QR code
    $verifiedBooking = $qrCodeService->verifyQrCode($qrData);

    expect($verifiedBooking)->not->toBeNull('Booking should be found by ID ' . $booking->id);
    expect($verifiedBooking->id)->toBe($booking->id);
});

test('qr code verification fails with invalid data', function () {
    $qrCodeService = app(QrCodeService::class);

    $invalidData = json_encode([
        'booking_number' => 'INVALID',
        'verification_code' => 'WRONG',
    ]);

    $result = $qrCodeService->verifyQrCode($invalidData);

    expect($result)->toBeNull();
});

test('can generate PDF ticket for booking', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $booking = Booking::factory()->create(['event_id' => $event->id]);

    $pdfService = app(TicketPdfService::class);
    $pdf = $pdfService->generateTicket($booking);

    expect($pdf)->toBeInstanceOf(\Barryvdh\DomPDF\PDF::class);
});

test('can download ticket PDF for confirmed booking', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $booking = Booking::factory()
        ->confirmed()
        ->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);

    $this->actingAs($user);

    $response = $this->get(route('bookings.ticket', $booking->booking_number));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('can download invoice PDF', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $booking = Booking::factory()->create([
        'event_id' => $event->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->get(route('bookings.invoice', $booking->booking_number));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('cannot download ticket without proper access', function () {
    $organizer = User::factory()->create();
    $otherUser = User::factory()->create();

    $event = Event::factory()->create();
    $booking = Booking::factory()->create([
        'event_id' => $event->id,
        'user_id' => $organizer->id,
    ]);

    $response = $this->actingAs($otherUser)
        ->get(route('bookings.ticket', $booking->booking_number));

    $response->assertStatus(403);
});

test('can generate invoice PDF', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $booking = Booking::factory()->create(['event_id' => $event->id]);

    $pdfService = app(TicketPdfService::class);
    $pdf = $pdfService->generateInvoice($booking);

    expect($pdf)->toBeInstanceOf(\Barryvdh\DomPDF\PDF::class);
});

