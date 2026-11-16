<?php

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;

test('Abzeichen werden täglich über den Scheduler geprüft', function () {
    $events = collect(app(Schedule::class)->events())
        ->filter(fn ($event) => str_contains((string) $event->command, 'badges:check-and-award --all'));

    expect($events)->toHaveCount(1)
        ->and($events->first()->expression)->toBe('30 3 * * *');
});

test('Startseite führt angemeldete Teilnehmende zu den Kontotypen', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertSee('Als Veranstalter freischalten lassen')
        ->assertSee(route('account-types'), false)
        ->assertDontSee('Kontakt aufnehmen');
});

test('Hervorhebung bezahlen lehnt Stripe ab und bietet es nicht an', function () {
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization();
    $event = \App\Models\Event::factory()->create(['organization_id' => $organization->id]);
    $fee = \App\Models\FeaturedEventFee::create([
        'event_id' => $event->id,
        'user_id' => $organizer->id,
        'duration_type' => 'weekly',
        'duration_days' => 7,
        'featured_start_date' => now()->addDay(),
        'featured_end_date' => now()->addDays(8),
        'fee_amount' => 49.99,
        'payment_status' => 'pending',
    ]);

    $this->actingAs($organizer)
        ->withSession(['current_organization_id' => $organization->id])
        ->get(route('organizer.featured-events.payment', $fee))
        ->assertOk()
        ->assertDontSee('Stripe')
        ->assertSee('49,99 €');

    $this->actingAs($organizer)
        ->withSession(['current_organization_id' => $organization->id])
        ->post(route('organizer.featured-events.process-payment', $fee), ['payment_method' => 'stripe'])
        ->assertSessionHasErrors('payment_method');
});

test('Hervorhebungs-Historie zeigt den tatsächlichen Betrag', function () {
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization();
    $event = \App\Models\Event::factory()->create(['organization_id' => $organization->id]);
    \App\Models\FeaturedEventFee::create([
        'event_id' => $event->id,
        'user_id' => $organizer->id,
        'duration_type' => 'weekly',
        'duration_days' => 7,
        'featured_start_date' => now()->addDay(),
        'featured_end_date' => now()->addDays(8),
        'fee_amount' => 49.99,
        'payment_status' => 'paid',
    ]);

    $this->actingAs($organizer)
        ->withSession(['current_organization_id' => $organization->id])
        ->get(route('organizer.featured-events.history'))
        ->assertOk()
        ->assertSee('49,99 €')
        ->assertDontSee('0,00 €');
});
