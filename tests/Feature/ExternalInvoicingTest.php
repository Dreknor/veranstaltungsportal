<?php

use App\Models\Booking;
use App\Models\Organization;
use Carbon\Carbon;

beforeEach(function () {
    setupRoles();
});

// ============================================================
// Organization Model – invoice_mode Helper-Methoden
// ============================================================

describe('Organization Model – invoice_mode', function () {

    it('behandelt null invoice_mode als automatic', function () {
        // Simuliert Legacy-Daten: invoice_mode als null im Model (ohne DB-Constraint)
        $org = Organization::factory()->make(['invoice_mode' => null]);
        expect($org->hasAutomaticInvoicing())->toBeTrue();
        expect($org->hasExternalInvoicing())->toBeFalse();
    });

    it('erkennt automatic invoice_mode korrekt', function () {
        $org = Organization::factory()->create(['invoice_mode' => 'automatic']);
        expect($org->hasAutomaticInvoicing())->toBeTrue();
        expect($org->hasExternalInvoicing())->toBeFalse();
    });

    it('erkennt external invoice_mode korrekt', function () {
        $org = Organization::factory()->create(['invoice_mode' => 'external']);
        expect($org->hasAutomaticInvoicing())->toBeFalse();
        expect($org->hasExternalInvoicing())->toBeTrue();
    });

    it('hat invoice_mode im fillable Array', function () {
        $org = new Organization();
        expect(in_array('invoice_mode', $org->getFillable()))->toBeTrue();
    });

    it('factory setzt default invoice_mode auf automatic', function () {
        $org = Organization::factory()->create();
        expect($org->invoice_mode)->toBe('automatic');
        expect($org->hasAutomaticInvoicing())->toBeTrue();
    });

    it('factory withExternalInvoicing setzt external', function () {
        $org = Organization::factory()->withExternalInvoicing()->create();
        expect($org->invoice_mode)->toBe('external');
        expect($org->hasExternalInvoicing())->toBeTrue();
    });

});

// ============================================================
// TODO 4: Booking Model – externally_invoiced Felder
// ============================================================

describe('Booking Model – externally_invoiced Felder', function () {

    it('hat externally_invoiced im fillable Array', function () {
        $booking = new Booking();
        $fillable = $booking->getFillable();
        expect(in_array('externally_invoiced', $fillable))->toBeTrue();
        expect(in_array('externally_invoiced_at', $fillable))->toBeTrue();
        expect(in_array('external_invoice_number', $fillable))->toBeTrue();
    });

    it('kann externally_invoiced Felder setzen', function () {
        $booking = createBooking([
            'externally_invoiced' => true,
            'externally_invoiced_at' => now(),
            'external_invoice_number' => 'EXT-2026-001',
        ]);

        expect($booking->externally_invoiced)->toBeTrue();
        expect($booking->externally_invoiced_at)->toBeInstanceOf(Carbon::class);
        expect($booking->external_invoice_number)->toBe('EXT-2026-001');
    });

    it('hat boolean cast für externally_invoiced', function () {
        $booking = createBooking(['externally_invoiced' => false]);
        expect($booking->externally_invoiced)->toBeFalse();
        expect(is_bool($booking->externally_invoiced))->toBeTrue();

        $booking2 = createBooking(['externally_invoiced' => true]);
        expect($booking2->externally_invoiced)->toBeTrue();
        expect(is_bool($booking2->externally_invoiced))->toBeTrue();
    });

    it('hat datetime cast für externally_invoiced_at', function () {
        $now = now();
        $booking = createBooking(['externally_invoiced_at' => $now]);
        expect($booking->externally_invoiced_at)->toBeInstanceOf(Carbon::class);
    });

    it('hat default false für externally_invoiced', function () {
        $booking = createBooking();
        expect($booking->externally_invoiced)->toBeFalse();
    });

    it('hat null default für externally_invoiced_at und external_invoice_number', function () {
        $booking = createBooking();
        expect($booking->externally_invoiced_at)->toBeNull();
        expect($booking->external_invoice_number)->toBeNull();
    });

});

// ============================================================
// TODO 1 & 2: Migrations – Spalten existieren in der DB
// ============================================================

describe('Datenbank-Schema', function () {

    it('organizations Tabelle hat invoice_mode Spalte', function () {
        expect(\Illuminate\Support\Facades\Schema::hasColumn('organizations', 'invoice_mode'))->toBeTrue();
    });

    it('bookings Tabelle hat externally_invoiced Spalte', function () {
        expect(\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'externally_invoiced'))->toBeTrue();
    });

    it('bookings Tabelle hat externally_invoiced_at Spalte', function () {
        expect(\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'externally_invoiced_at'))->toBeTrue();
    });

    it('bookings Tabelle hat external_invoice_number Spalte', function () {
        expect(\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'external_invoice_number'))->toBeTrue();
    });

});


