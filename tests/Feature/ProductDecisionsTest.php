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

test('Registrierung speichert Vor- und Nachname getrennt und bildet daraus den Anzeigenamen', function () {
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

    $this->get('/register')->assertOk()->assertSee('Vorname')->assertSee('Nachname')->assertDontSee('Vollständiger Name');

    $this->post('/register', [
        'first_name' => ' Erika ',
        'last_name' => 'Mustermann',
        'email' => 'erika@example.com',
        'password' => 'Passwort-123!',
        'password_confirmation' => 'Passwort-123!',
        'account_type' => 'participant',
    ]);

    $user = User::where('email', 'erika@example.com')->firstOrFail();
    expect($user->first_name)->toBe('Erika')
        ->and($user->last_name)->toBe('Mustermann')
        ->and($user->name)->toBe('Erika Mustermann')
        ->and($user->fullName())->toBe('Erika Mustermann');
});

test('Registrierung verlangt Vor- und Nachname', function () {
    $this->post('/register', [
        'email' => 'ohne@example.com',
        'password' => 'Passwort-123!',
        'password_confirmation' => 'Passwort-123!',
        'account_type' => 'participant',
    ])->assertSessionHasErrors(['first_name', 'last_name']);
});

test('Kategorie-Bericht zählt Buchungen im Zeitraum auch bei älteren Veranstaltungen', function () {
    $admin = User::factory()->create();
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin->assignRole('admin');

    $category = \App\Models\EventCategory::factory()->create(['name' => 'Alte Kategorie']);
    $event = \App\Models\Event::factory()->create(['event_category_id' => $category->id, 'created_at' => now()->subYears(2)]);
    \App\Models\Booking::factory()->count(2)->create(['event_id' => $event->id, 'status' => 'confirmed', 'payment_status' => 'paid', 'total' => 50]);

    $response = $this->actingAs($admin)->get(route('admin.reporting.events', ['period' => '30days']))->assertOk();
    $row = collect($response->viewData('data')['events_by_category'])->firstWhere('id', $category->id);

    expect($row->period_bookings_count)->toBe(2)
        ->and($row->bookings_sum_total)->toEqual(100.0);
});

test('Support-Adresse ist info@esdigmbh.de', function () {
    $this->actingAs(User::factory()->create())->get(route('help.index'))->assertOk()->assertSee('info@esdigmbh.de')->assertDontSee('support@bildungsportal.de');
});
