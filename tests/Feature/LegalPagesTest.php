<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Organization;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests für Legal-Seiten (Datenschutz, Impressum) und DSGVO-Buchungspflichten.
 */
class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function datenschutz_page_is_accessible(): void
    {
        $response = $this->get(route('datenschutz'));
        $response->assertStatus(200);
        $response->assertSee('Datenschutzerklärung', false);
    }

    #[Test]
    public function impressum_page_is_accessible(): void
    {
        $response = $this->get(route('impressum'));
        $response->assertStatus(200);
        $response->assertSee('Impressum', false);
    }

    #[Test]
    public function datenschutz_page_has_noindex_meta(): void
    {
        $response = $this->get(route('datenschutz'));
        $response->assertSee('noindex', false);
    }

    #[Test]
    public function footer_links_to_internal_datenschutz(): void
    {
        $response = $this->get(route('home'));
        $response->assertSee(route('datenschutz'), false);
        $response->assertSee(route('impressum'), false);
    }

    #[Test]
    public function booking_without_privacy_accepted_fails_validation(): void
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);

        $event = Event::factory()->create([
            'organization_id' => $result['organization']->id,
            'is_published' => true,
        ]);

        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 0,
        ]);

        $response = $this->post(route('bookings.store', $event), [
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.de',
            'billing_address' => 'Musterstraße 1',
            'billing_postal_code' => '01234',
            'billing_city' => 'Musterstadt',
            'billing_country' => 'Deutschland',
            'tickets' => [
                ['ticket_type_id' => $ticketType->id, 'quantity' => 1],
            ],
            'payment_method' => 'invoice',
            // privacy_accepted absichtlich weggelassen
        ]);

        $response->assertSessionHasErrors('privacy_accepted');
    }

    #[Test]
    public function booking_with_privacy_accepted_passes_validation(): void
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);

        $event = Event::factory()->create([
            'organization_id' => $result['organization']->id,
            'is_published' => true,
            'max_attendees' => 100,
        ]);

        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 0,
            'quantity' => 10,
        ]);

        $response = $this->post(route('bookings.store', $event), [
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.de',
            'billing_address' => 'Musterstraße 1',
            'billing_postal_code' => '01234',
            'billing_city' => 'Musterstadt',
            'billing_country' => 'Deutschland',
            'tickets' => [
                ['ticket_type_id' => $ticketType->id, 'quantity' => 1],
            ],
            'payment_method' => 'invoice',
            'privacy_accepted' => '1',
        ]);

        // Mit korrekter privacy_accepted darf kein Validierungsfehler dafür kommen
        $response->assertSessionDoesntHaveErrors(['privacy_accepted']);
    }
}


