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

    $qrCodeService = app(QrCodeService::class);

    // Generate QR data
    $qrData = json_encode([
        'booking_number' => $booking->booking_number,
        'event_id' => $booking->event_id,
        'verification_code' => $booking->verification_code,
        'attendee_name' => $booking->attendee_name,
        'attendee_email' => $booking->attendee_email,
    ]);

    $verifiedBooking = $qrCodeService->verifyQrCode($qrData);

    expect($verifiedBooking)->not->toBeNull();
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

test('can download ticket PDF', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $booking = Booking::factory()->create([
        'event_id' => $event->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->get(route('bookings.ticket', $booking->booking_number));

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

