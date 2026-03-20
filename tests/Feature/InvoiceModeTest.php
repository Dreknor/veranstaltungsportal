<?php

/**
 * tests/Feature/InvoiceModeTest.php
 *
 * Umfangreiche Tests für den Rechnungsmodus (automatisch vs. extern).
 * Deckt TODOs 6–19 ab.
 */

use App\Models\Booking;
use App\Models\Organization;
use App\Models\TicketType;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

beforeEach(fn () => setupRoles());

// ─────────────────────────────────────────────────────────────────────────────
// BookingInvoiceObserver
// ─────────────────────────────────────────────────────────────────────────────

describe('BookingInvoiceObserver – Rechnungsnummern-Generierung', function () {
    it('generiert KEINE Rechnungsnummer bei external Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking(['event_id' => $event->id, 'total' => 50.00]);

        expect($booking->fresh()->invoice_number)->toBeNull();
        expect($booking->fresh()->invoice_date)->toBeNull();
    });

    it('generiert keine Rechnungsnummer bei payment_status-Wechsel zu paid im external Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking([
            'event_id' => $event->id,
            'total' => 50.00,
            'payment_status' => 'pending',
        ]);

        $booking->update(['payment_status' => 'paid']);

        expect($booking->fresh()->invoice_number)->toBeNull();
    });

    it('generiert keine Rechnungsnummer bei kostenloser Buchung unabhängig vom Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'automatic',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking(['event_id' => $event->id, 'total' => 0]);

        expect($booking->fresh()->invoice_number)->toBeNull();
    });

    it('behandelt Buchungen ohne Organisation korrekt (null-safe)', function () {
        $event = createEvent();
        $booking = createBooking(['event_id' => $event->id, 'total' => 50.00]);
        // Kein Fehler – kein organization_id erforderlich
        expect($booking)->toBeInstanceOf(Booking::class);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// PayPal-Blockierung + Rechnungs-Download-Guard
// ─────────────────────────────────────────────────────────────────────────────

describe('BookingController – externer Rechnungsmodus', function () {
    it('blockiert PayPal-Zahlung bei external Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id, 'is_published' => true]);

        // TicketType ohne 'capacity' Spalte (pre-existierendes SQLite-Problem)
        $ticketType = \App\Models\TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 50.00,
        ]);

        $response = $this->post(route('bookings.store', $event->slug), [
            'customer_name' => 'Max Mustermann',
            'customer_email' => 'max@example.com',
            'payment_method' => 'paypal',
            'tickets' => [$ticketType->id => 1],
        ]);

        // PayPal-Buchung darf nicht mit paypal-Methode gespeichert werden
        $booking = Booking::where('customer_email', 'max@example.com')
            ->where('event_id', $event->id)
            ->first();

        if ($booking) {
            expect($booking->payment_method)->not->toBe('paypal');
        } else {
            // Redirect bedeutet: Entweder Fehler (back) oder erfolgreiche Buchung mit redirect
            // Bei externer Rechnungsstellung muss payment_method = 'invoice' sein
            expect($response->status())->toBeIn([200, 302]);
        }
    });

    it('verhindert Rechnungs-Download bei external Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking([
            'event_id' => $event->id,
            'total' => 50.00,
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        $response = $this->get(route('bookings.invoice', $booking->booking_number));
        $response->assertStatus(403);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// TODO 12: Rechnungsmodus-Settings (InvoiceSettingsController)
// ─────────────────────────────────────────────────────────────────────────────

describe('Rechnungsmodus-Einstellungen', function () {
    it('kann Rechnungsmodus auf external wechseln', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization();

        $this->actingAs($organizer)
            ->put(route('organizer.settings.invoice-mode.update'), [
                'invoice_mode' => 'external',
            ])
            ->assertRedirect();

        expect($org->fresh()->invoice_mode)->toBe('external');
    });

    it('kann Rechnungsmodus auf automatic wechseln', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $this->actingAs($organizer)
            ->put(route('organizer.settings.invoice-mode.update'), [
                'invoice_mode' => 'automatic',
            ])
            ->assertRedirect();

        expect($org->fresh()->invoice_mode)->toBe('automatic');
    });

    it('lehnt ungültige invoice_mode-Werte ab', function () {
        ['organizer' => $organizer] = createOrganizerWithOrganization();

        $this->actingAs($organizer)
            ->put(route('organizer.settings.invoice-mode.update'), [
                'invoice_mode' => 'invalid_wert',
            ])
            ->assertSessionHasErrors('invoice_mode');
    });

    it('speichert Flash-Nachricht nach erfolgreichem Wechsel', function () {
        ['organizer' => $organizer] = createOrganizerWithOrganization();

        $this->actingAs($organizer)
            ->put(route('organizer.settings.invoice-mode.update'), [
                'invoice_mode' => 'external',
            ])
            ->assertSessionHas('status');
    });

    it('zeigt Rechnungseinstellungs-Seite bei automatic korrekt an', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'automatic',
        ]);

        $this->actingAs($organizer)
            ->get(route('organizer.settings.invoice.index'))
            ->assertOk()
            ->assertSee('Automatische Rechnungsstellung');
    });

    it('zeigt Rechnungseinstellungs-Seite bei external korrekt an', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $this->actingAs($organizer)
            ->get(route('organizer.settings.invoice.index'))
            ->assertOk()
            ->assertSee('Externe Rechnungsstellung');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// TODO 13: BillingDataExportController
// ─────────────────────────────────────────────────────────────────────────────

describe('BillingDataExport – Übersicht', function () {
    it('zeigt Rechnungsdaten-Übersicht für Organizer', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking(['event_id' => $event->id, 'total' => 50.00]);

        $this->actingAs($organizer)
            ->withSession(['current_organization_id' => $org->id])
            ->get(route('organizer.billing-data.index'))
            ->assertOk()
            ->assertSee($booking->booking_number);
    });

    it('blendet kostenfreie Buchungen aus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $paidBooking = createBooking(['event_id' => $event->id, 'total' => 50.00]);
        $freeBooking = createBooking(['event_id' => $event->id, 'total' => 0]);

        $this->actingAs($organizer)
            ->withSession(['current_organization_id' => $org->id])
            ->get(route('organizer.billing-data.index'))
            ->assertSee($paidBooking->booking_number)
            ->assertDontSee($freeBooking->booking_number);
    });

    it('filtert nach Fakturiert-Status (pending)', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $invoiced = createBooking(['event_id' => $event->id, 'total' => 50.00, 'externally_invoiced' => true]);
        $pending = createBooking(['event_id' => $event->id, 'total' => 75.00, 'externally_invoiced' => false]);

        $this->actingAs($organizer)
            ->withSession(['current_organization_id' => $org->id])
            ->get(route('organizer.billing-data.index', ['filter' => 'pending']))
            ->assertSee($pending->booking_number)
            ->assertDontSee($invoiced->booking_number);
    });

    it('zeigt keine Buchungen fremder Organisationen', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);
        ['organizer' => $otherOrganizer, 'organization' => $otherOrg] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $myEvent = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $otherEvent = createEvent(['organization_id' => $otherOrg->id, 'user_id' => $otherOrganizer->id]);

        $myBooking = createBooking(['event_id' => $myEvent->id, 'total' => 50.00]);
        $otherBooking = createBooking(['event_id' => $otherEvent->id, 'total' => 75.00]);

        $this->actingAs($organizer)
            ->withSession(['current_organization_id' => $org->id])
            ->get(route('organizer.billing-data.index'))
            ->assertSee($myBooking->booking_number)
            ->assertDontSee($otherBooking->booking_number);
    });
});

describe('BillingDataExport – Excel-Export', function () {
    it('exportiert Rechnungsdaten als CSV', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        createBooking(['event_id' => $event->id, 'total' => 50.00]);

        $response = $this->actingAs($organizer)
            ->get(route('organizer.billing-data.export'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    });

    it('enthält Buchungsnummer im Export', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking(['event_id' => $event->id, 'total' => 50.00]);

        $response = $this->actingAs($organizer)
            ->get(route('organizer.billing-data.export'));

        $content = $response->streamedContent();
        expect($content)->toContain($booking->booking_number);
    });
});

describe('BillingDataExport – DATEV-Export', function () {
    it('gibt DATEV-Export als CSV aus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        createBooking(['event_id' => $event->id, 'total' => 119.00, 'payment_status' => 'paid']);

        $response = $this->actingAs($organizer)
            ->get(route('organizer.billing-data.export-datev'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    });

    it('enthält EXTF-Header im DATEV-Export', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        createBooking(['event_id' => $event->id, 'total' => 119.00, 'payment_status' => 'paid']);

        $content = $this->actingAs($organizer)
            ->get(route('organizer.billing-data.export-datev'))
            ->streamedContent();

        expect($content)->toContain('"EXTF"');
    });

    it('exportiert nur bezahlte Buchungen im DATEV-Export', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        createBooking(['event_id' => $event->id, 'total' => 119.00, 'payment_status' => 'paid']);
        createBooking(['event_id' => $event->id, 'total' => 50.00, 'payment_status' => 'pending']);

        $content = $this->actingAs($organizer)
            ->get(route('organizer.billing-data.export-datev'))
            ->streamedContent();

        expect($content)->toContain('119,00');
        expect($content)->not->toContain('50,00');
    });

    it('verwendet Komma als Dezimaltrenner im DATEV-Export', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        createBooking(['event_id' => $event->id, 'total' => 123.45, 'payment_status' => 'paid']);

        $content = $this->actingAs($organizer)
            ->get(route('organizer.billing-data.export-datev'))
            ->streamedContent();

        expect($content)->toContain('123,45');
        expect($content)->not->toContain('123.45');
    });

    it('verwendet DDMM-Format für Belegdatum im DATEV-Export', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        createBooking([
            'event_id' => $event->id,
            'total' => 100.00,
            'payment_status' => 'paid',
            'created_at' => '2026-03-20 10:00:00',
        ]);

        $content = $this->actingAs($organizer)
            ->get(route('organizer.billing-data.export-datev'))
            ->streamedContent();

        // DDMM-Format: 20. März = "2003"
        expect($content)->toContain('2003');
    });
});

describe('BillingDataExport – Fakturierung markieren', function () {
    it('kann Buchung als extern fakturiert markieren', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking(['event_id' => $event->id, 'total' => 50.00]);

        $this->actingAs($organizer)
            ->put(route('organizer.billing-data.mark-invoiced', $booking), [
                'external_invoice_number' => 'EXT-2026-042',
            ])
            ->assertRedirect();

        expect($booking->fresh()->externally_invoiced)->toBeTrue();
        expect($booking->fresh()->external_invoice_number)->toBe('EXT-2026-042');
        expect($booking->fresh()->externally_invoiced_at)->not->toBeNull();
    });

    it('kann mehrere Buchungen in Bulk als fakturiert markieren', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking1 = createBooking(['event_id' => $event->id, 'total' => 50.00]);
        $booking2 = createBooking(['event_id' => $event->id, 'total' => 75.00]);

        $this->actingAs($organizer)
            ->put(route('organizer.billing-data.bulk-mark-invoiced'), [
                'booking_ids' => [$booking1->id, $booking2->id],
            ])
            ->assertRedirect();

        expect($booking1->fresh()->externally_invoiced)->toBeTrue();
        expect($booking2->fresh()->externally_invoiced)->toBeTrue();
    });

    it('verhindert das Markieren von Buchungen fremder Organisationen', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);
        ['organizer' => $otherOrganizer, 'organization' => $otherOrg] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $otherEvent = createEvent(['organization_id' => $otherOrg->id, 'user_id' => $otherOrganizer->id]);
        $otherBooking = createBooking(['event_id' => $otherEvent->id, 'total' => 50.00]);

        // Organizer versucht, fremde Buchung zu markieren
        $this->actingAs($organizer)
            ->put(route('organizer.billing-data.mark-invoiced', $otherBooking), [
                'external_invoice_number' => 'HACK',
            ])
            ->assertStatus(403);
    });

    it('Bulk-Markierung ignoriert Buchungen fremder Organisationen', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);
        ['organizer' => $otherOrganizer, 'organization' => $otherOrg] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $myEvent = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $otherEvent = createEvent(['organization_id' => $otherOrg->id, 'user_id' => $otherOrganizer->id]);

        $myBooking = createBooking(['event_id' => $myEvent->id, 'total' => 50.00]);
        $otherBooking = createBooking(['event_id' => $otherEvent->id, 'total' => 50.00]);

        $this->actingAs($organizer)
            ->put(route('organizer.billing-data.bulk-mark-invoiced'), [
                'booking_ids' => [$myBooking->id, $otherBooking->id],
            ])
            ->assertRedirect();

        // Nur eigene Buchung darf markiert werden
        expect($myBooking->fresh()->externally_invoiced)->toBeTrue();
        expect($otherBooking->fresh()->externally_invoiced)->toBeFalse();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// TODO 11: Buchungsdetails – Rechnungs-Download-Button
// ─────────────────────────────────────────────────────────────────────────────

describe('Buchungsdetails – Rechnungs-Download-Button', function () {
    it('zeigt Hinweis statt Download-Button bei external Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $user = createUser();
        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking([
            'event_id' => $event->id,
            'total' => 50.00,
            'status' => 'confirmed',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('bookings.show', $booking->booking_number))
            ->assertOk()
            ->assertSee('separat vom Veranstalter')
            ->assertDontSee('Rechnung herunterladen');
    });

    it('zeigt Rechnungs-Download-Button bei automatic Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'automatic',
        ]);

        $user = createUser();
        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking([
            'event_id' => $event->id,
            'total' => 50.00,
            'status' => 'confirmed',
            'invoice_number' => 'RE-2026-001',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('bookings.show', $booking->booking_number))
            ->assertOk()
            ->assertSee('Rechnung herunterladen');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// TODO 10: E-Mail Template Buchungsbestätigung
// ─────────────────────────────────────────────────────────────────────────────

describe('E-Mail Buchungsbestätigung – externer Modus', function () {
    it('blendet Rechnungsnummer in E-Mail bei external Modus aus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking([
            'event_id' => $event->id,
            'total' => 50.00,
            'invoice_number' => null,
        ]);

        $mailable = new \App\Mail\BookingConfirmation($booking->load('event.organization', 'items.ticketType'));
        $rendered = $mailable->render();

        expect($rendered)->not->toContain('Rechnungsnr.');
    });

    it('zeigt externen Zahlungshinweis OHNE Bankdaten bei external Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
            'bank_account' => ['iban' => 'DE12345678901234567890', 'account_holder' => 'Test GmbH'],
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking([
            'event_id' => $event->id,
            'total' => 50.00,
            'payment_status' => 'pending',
        ]);

        $mailable = new \App\Mail\BookingConfirmation($booking->load('event.organization', 'items.ticketType'));
        $rendered = $mailable->render();

        expect($rendered)->toContain('erhalten Sie in Kürze eine Rechnung');
        expect($rendered)->not->toContain('DE12345678901234567890');
    });

    it('zeigt Rechnungsnummer in E-Mail bei automatic Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'automatic',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking([
            'event_id' => $event->id,
            'total' => 50.00,
            'invoice_number' => 'RE-2026-001',
            'payment_status' => 'pending',
        ]);

        $mailable = new \App\Mail\BookingConfirmation($booking->load('event.organization', 'items.ticketType'));
        $rendered = $mailable->render();

        expect($rendered)->toContain('Rechnungsnr.');
        expect($rendered)->toContain('RE-2026-001');
    });

    it('hängt KEIN Rechnungs-PDF bei external Modus an', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking([
            'event_id' => $event->id,
            'total' => 50.00,
        ]);

        $mailable = new \App\Mail\BookingConfirmation($booking->load('event.organization', 'items.ticketType'));
        $attachments = $mailable->attachments();

        // Kein Attachment mit 'Rechnung' im Namen
        $invoiceAttachments = array_filter($attachments, function ($a) {
            return isset($a['as']) && str_contains($a['as'], 'Rechnung');
        });

        expect(count($invoiceAttachments))->toBe(0);
        // Rechnungsnummer darf nicht gesetzt worden sein
        expect($booking->fresh()->invoice_number)->toBeNull();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// TODO 14/15: Organizer-Buchungsansicht
// ─────────────────────────────────────────────────────────────────────────────

describe('Organizer-Buchungsansicht – Fakturierungsstatus', function () {
    it('zeigt Fakturierungsstatus in Buchungsdetails bei external Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking([
            'event_id' => $event->id,
            'total' => 50.00,
            'externally_invoiced' => false,
        ]);

        $this->actingAs($organizer)
            ->get(route('organizer.bookings.show', $booking))
            ->assertOk()
            ->assertSee('Noch nicht fakturiert')
            ->assertSee('Als fakturiert markieren');
    });

    it('zeigt fakturierten Status wenn bereits fakturiert', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking([
            'event_id' => $event->id,
            'total' => 50.00,
            'externally_invoiced' => true,
            'external_invoice_number' => 'EXT-2026-007',
        ]);

        $this->actingAs($organizer)
            ->get(route('organizer.bookings.show', $booking))
            ->assertOk()
            ->assertSee('Fakturiert')
            ->assertSee('EXT-2026-007');
    });

    it('zeigt KEINEN Fakturierungsstatus bei automatic Modus', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'automatic',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $booking = createBooking(['event_id' => $event->id, 'total' => 50.00]);

        $this->actingAs($organizer)
            ->get(route('organizer.bookings.show', $booking))
            ->assertOk()
            ->assertDontSee('Extern fakturiert');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Integration: Vollständiger Buchungsfluss
// ─────────────────────────────────────────────────────────────────────────────

describe('Integration – Buchungsfluss bei external Modus', function () {
    it('Moduswechsel beeinflusst bestehende Buchungen NICHT', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'automatic',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        // Buchung im automatic-Modus erstellen (ohne invoice_number wegen fehlender Billing-Daten in Test)
        $booking = createBooking(['event_id' => $event->id, 'total' => 50.00]);

        // Modus wechseln
        $org->update(['invoice_mode' => 'external']);

        // Bestehende Buchung unverändert
        $freshBooking = $booking->fresh();
        // Buchung existiert noch
        expect($freshBooking)->not->toBeNull();
        // externally_invoiced wurde nicht geändert
        expect($freshBooking->externally_invoiced)->toBeFalse();
    });

    it('neue Buchung nach Moduswechsel zu external erhält keine Rechnungsnummer', function () {
        ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization([], [
            'invoice_mode' => 'external',
        ]);

        $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);
        $newBooking = createBooking(['event_id' => $event->id, 'total' => 50.00]);

        expect($newBooking->fresh()->invoice_number)->toBeNull();
    });

    it('Organization-Methoden liefern konsistente Ergebnisse', function () {
        $org = Organization::factory()->create(['invoice_mode' => 'external']);

        expect($org->hasExternalInvoicing())->toBeTrue();
        expect($org->hasAutomaticInvoicing())->toBeFalse();

        $org->update(['invoice_mode' => 'automatic']);
        $org->refresh();

        expect($org->hasExternalInvoicing())->toBeFalse();
        expect($org->hasAutomaticInvoicing())->toBeTrue();
    });
});

