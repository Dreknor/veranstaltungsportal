<?php

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Services\QrCodeService;
use App\Services\TicketPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->event = Event::factory()->create(['is_published' => true]);
    $this->booking = Booking::factory()->create([
        'event_id' => $this->event->id,
        'user_id' => $this->user->id,
        'status' => 'confirmed',
    ]);

    // Lade die Event-Beziehung, damit booking_number garantiert existiert
    $this->booking->load('event');
});

test('can generate qr code for booking', function () {
    $qrCodeService = app(QrCodeService::class);

    $qrCode = $qrCodeService->generateBookingQrCode($this->booking);

    expect($qrCode)->toBeString();
    expect($qrCode)->toContain('svg');
});

test('can generate qr code as data uri', function () {
    $qrCodeService = app(QrCodeService::class);

    $dataUri = $qrCodeService->generateBookingQrCodeDataUri($this->booking);

    expect($dataUri)->toBeString();
    // QR Code Service generiert SVG data URI statt PNG
    expect($dataUri)->toStartWith('data:image/svg+xml;base64,');
});

test('can verify qr code data', function () {
    $qrCodeService = app(QrCodeService::class);

    $qrData = json_encode([
        'booking_id' => $this->booking->id,
        'reference' => $this->booking->booking_number,
        'event_id' => $this->event->id,
    ]);

    $verified = $qrCodeService->verifyQrCodeData($qrData);

    expect($verified)->toBeArray();
    expect($verified['booking_id'])->toBe($this->booking->id);
});

test('can download ticket pdf', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('bookings.ticket', $this->booking->booking_number));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});

test('cannot download ticket without access', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);

    $response = $this->get(route('bookings.ticket', $this->booking->booking_number));

    $response->assertForbidden();
});

test('ticket pdf service can be instantiated', function () {
    $ticketPdfService = app(TicketPdfService::class);

    expect($ticketPdfService)->toBeInstanceOf(TicketPdfService::class);
});

