<?php

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use App\Models\Organization;

test('debug event creation', function () {
    $result = createOrganizerWithOrganization();
    $user = $result['organizer'];
    $organization = $result['organization'];
    session(['current_organization_id' => $organization->id]);
    $this->actingAs($user);

    $category = EventCategory::factory()->create();

    $response = $this->post(route('organizer.events.store'), [
        'title' => 'Test Event',
        'event_category_id' => $category->id,
        'event_type' => 'physical',
        'description' => 'This is a test event',
        'start_date' => now()->addDays(7)->format('Y-m-d\TH:i'),
        'end_date' => now()->addDays(7)->addHours(3)->format('Y-m-d\TH:i'),
        'venue_name' => 'Test Venue',
        'venue_address' => 'Test Street 1',
        'venue_city' => 'Test City',
        'venue_postal_code' => '12345',
        'venue_country' => 'Deutschland',
        'is_published' => false,
    ]);

    // Dump response for debugging
    if ($response->status() !== 302) {
        dump('Response Status: ' . $response->status());
        dump('Session Errors: ', session('errors'));
        dump('Validation Errors: ', $response->getSession()->get('errors'));
    }

    $response->assertStatus(302);
});

