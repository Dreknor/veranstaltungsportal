<?php

use App\Http\Controllers\Organizer\EventManagementController;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventReview;
use App\Models\Organization;
use App\Models\User;
use App\Support\ReviewSummary;

/*
 * Bewertungen sichtbar machen: Veranstalter-Profil, frühere Termine, Moderation und Antworten.
 */

function pastEvent(Organization $organization, array $attributes = []): Event
{
    return Event::factory()->create([
        'organization_id' => $organization->id,
        'is_published' => true,
        'start_date' => now()->subMonth(),
        'end_date' => now()->subMonth()->addHours(3),
        ...$attributes,
    ]);
}

function futureEvent(Organization $organization, array $attributes = []): Event
{
    return Event::factory()->create([
        'organization_id' => $organization->id,
        'is_published' => true,
        'start_date' => now()->addMonth(),
        'end_date' => now()->addMonth()->addHours(3),
        ...$attributes,
    ]);
}

function approvedReviews(Event $event, array $ratings, array $attributes = []): void
{
    foreach ($ratings as $rating) {
        EventReview::factory()->create([
            'event_id' => $event->id,
            'rating' => $rating,
            'is_approved' => true,
            ...$attributes,
        ]);
    }
}

// --- Kennzahlen -----------------------------------------------------------------------------

test('der Durchschnitt zählt nur freigegebene Bewertungen', function () {
    $event = pastEvent(Organization::factory()->create());
    approvedReviews($event, [5, 4, 3]);
    EventReview::factory()->create(['event_id' => $event->id, 'rating' => 1, 'is_approved' => false]);

    $summary = $event->reviewSummary();

    expect($summary->count)->toBe(3)
        ->and($summary->average)->toBe(4.0)
        ->and($summary->distribution[1])->toBe(0)
        ->and($summary->isMeaningful())->toBeTrue();
});

test('ein Durchschnitt gilt erst ab drei Bewertungen als aussagekräftig', function () {
    expect((new ReviewSummary(2, 5.0))->isMeaningful())->toBeFalse()
        ->and((new ReviewSummary(ReviewSummary::MIN_COUNT, 5.0))->isMeaningful())->toBeTrue();
});

test('die Gesamtbewertung des Veranstalters wird bei neuen Freigaben aktualisiert', function () {
    $organization = Organization::factory()->create();
    $event = pastEvent($organization);
    approvedReviews($event, [5, 5]);

    expect($organization->reviewSummary()->count)->toBe(2);

    $review = EventReview::factory()->create(['event_id' => $event->id, 'rating' => 2, 'is_approved' => false]);
    expect($organization->reviewSummary()->count)->toBe(2);

    $review->approve();
    expect($organization->reviewSummary()->count)->toBe(3)
        ->and($organization->reviewSummary()->average)->toBe(4.0);
});

test('die Gesamtbewertung des Veranstalters berücksichtigt nur die letzten 24 Monate', function () {
    $organization = Organization::factory()->create();
    $event = pastEvent($organization);
    approvedReviews($event, [5, 5, 5]);
    $old = EventReview::factory()->create(['event_id' => $event->id, 'rating' => 1, 'is_approved' => true]);
    $old->forceFill(['created_at' => now()->subMonths(30)])->save();

    expect($organization->reviewSummary()->count)->toBe(3)
        ->and($organization->reviewSummary()->average)->toBe(5.0);
});

// --- Namensanzeige ------------------------------------------------------------------------

test('öffentlich erscheint der Name gekürzt, außer er wurde freigegeben', function () {
    $user = User::factory()->create(['first_name' => 'Daniela', 'last_name' => 'Richter']);
    $review = EventReview::factory()->create(['user_id' => $user->id, 'show_full_name' => false]);

    expect($review->publicName())->toBe('Daniela R.');

    $review->update(['show_full_name' => true]);
    expect($review->publicName())->toBe($user->fullName());
});

// --- Bewerten und Ändern ------------------------------------------------------------------

test('beim Bewerten werden Buchung und Namensfreigabe gespeichert', function () {
    $user = User::factory()->create();
    $event = pastEvent(Organization::factory()->create());
    $booking = Booking::factory()->create(['user_id' => $user->id, 'event_id' => $event->id, 'status' => 'confirmed']);

    $this->actingAs($user)
        ->post(route('events.reviews.store', $event), ['rating' => 4, 'comment' => 'Gut', 'show_full_name' => '1'])
        ->assertSessionHas('success');

    $this->assertDatabaseHas('event_reviews', [
        'event_id' => $event->id,
        'user_id' => $user->id,
        'booking_id' => $booking->id,
        'show_full_name' => true,
        'is_approved' => false,
    ]);
});

test('eine geänderte Bewertung muss erneut freigegeben werden', function () {
    $user = User::factory()->create();
    $review = EventReview::factory()->create([
        'user_id' => $user->id,
        'rating' => 5,
        'comment' => 'Sehr gut',
        'is_approved' => true,
    ]);

    $this->actingAs($user)
        ->put(route('reviews.update', $review), ['rating' => 1, 'comment' => 'Doch nicht so gut'])
        ->assertSessionHas('success');

    $review->refresh();
    expect($review->is_approved)->toBeFalse()
        ->and($review->isPending())->toBeTrue()
        ->and($review->rating)->toBe(1);
});

test('nur die Namensfreigabe zu ändern hebt die Freigabe nicht auf', function () {
    $user = User::factory()->create();
    $review = EventReview::factory()->create([
        'user_id' => $user->id,
        'rating' => 5,
        'comment' => 'Sehr gut',
        'is_approved' => true,
    ]);

    $this->actingAs($user)
        ->put(route('reviews.update', $review), ['rating' => 5, 'comment' => 'Sehr gut', 'show_full_name' => '1']);

    expect($review->fresh()->is_approved)->toBeTrue()
        ->and($review->fresh()->show_full_name)->toBeTrue();
});

test('nur freigegebene Bewertungen erscheinen auf der Veranstaltungsseite', function () {
    $event = pastEvent(Organization::factory()->create());
    EventReview::factory()->create(['event_id' => $event->id, 'is_approved' => true, 'comment' => 'Freigegebener Kommentar']);
    EventReview::factory()->create(['event_id' => $event->id, 'is_approved' => false, 'comment' => 'Wartender Kommentar']);

    $this->get(route('events.show', $event->slug))
        ->assertOk()
        ->assertSee('Freigegebener Kommentar')
        ->assertDontSee('Wartender Kommentar');
});

// --- Veranstalter-Profil ------------------------------------------------------------------

test('das Veranstalter-Profil zeigt Gesamtbewertung und Bewertungen vergangener Veranstaltungen', function () {
    $organization = Organization::factory()->create(['name' => 'Akademie Elbtal']);
    $past = pastEvent($organization, ['title' => 'Klassenführung kompakt']);
    futureEvent($organization, ['title' => 'Elterngespräche führen']);
    approvedReviews($past, [5, 4, 5], ['comment' => 'Sehr praxisnah']);

    $this->get(route('organizations.show', $organization))
        ->assertOk()
        ->assertSee('Akademie Elbtal')
        ->assertSee('4,7 von 5')
        ->assertSee('3 Bewertungen')
        ->assertSee('Elterngespräche führen')
        ->assertSee('Sehr praxisnah');

    $this->get(route('organizations.show', [$organization, 'ansicht' => 'vergangen']))
        ->assertOk()
        ->assertSee('Klassenführung kompakt')
        ->assertSee(route('events.show', $past->slug).'#bewertungen', false);

    $this->get(route('organizations.show', [$organization, 'ansicht' => 'bewertungen']))
        ->assertOk()
        ->assertSee('Teilnahme an')
        ->assertSee('Klassenführung kompakt');
});

test('unter drei Bewertungen zeigt das Profil keinen Durchschnitt', function () {
    $organization = Organization::factory()->create();
    approvedReviews(pastEvent($organization), [5, 5]);

    $this->get(route('organizations.show', $organization))
        ->assertOk()
        ->assertDontSee('5,0 von 5')
        ->assertSee('Noch zu wenige Bewertungen');
});

test('ein Veranstalter ohne veröffentlichte Veranstaltung hat kein öffentliches Profil', function () {
    $organization = Organization::factory()->create();
    Event::factory()->create(['organization_id' => $organization->id, 'is_published' => false]);

    $this->get(route('organizations.show', $organization))->assertNotFound();
});

test('die Veranstaltungsseite verlinkt das Veranstalter-Profil mit Bewertung', function () {
    $organization = Organization::factory()->create(['name' => 'Akademie Elbtal']);
    approvedReviews(pastEvent($organization), [4, 4, 5]);
    $event = futureEvent($organization);

    $this->get(route('events.show', $event->slug))
        ->assertOk()
        ->assertSee(route('organizations.show', $organization), false)
        ->assertSee('4,3 von 5');
});

test('die Veranstaltungsliste zeigt die Bewertung des Veranstalters', function () {
    $organization = Organization::factory()->create(['name' => 'Akademie Elbtal']);
    approvedReviews(pastEvent($organization), [5, 5, 5]);
    futureEvent($organization);

    $this->get(route('events.index'))
        ->assertOk()
        ->assertSee('5,0 von 5');
});

// --- Frühere Termine ----------------------------------------------------------------------

test('ein neuer Termin zeigt die Bewertungen früherer Termine', function () {
    $organization = Organization::factory()->create();
    $first = pastEvent($organization, ['start_date' => now()->subYear(), 'end_date' => now()->subYear()->addHours(3)]);
    $second = pastEvent($organization, ['predecessor_id' => $first->id]);
    $next = futureEvent($organization, ['predecessor_id' => $second->id]);
    approvedReviews($first, [5], ['comment' => 'Erster Durchgang war stark']);
    approvedReviews($second, [4, 4], ['comment' => 'Zweiter Durchgang auch']);

    expect($next->predecessorIds())->toBe([$second->id, $first->id]);

    $this->get(route('events.show', $next->slug))
        ->assertOk()
        ->assertSee('Bewertungen früherer Termine')
        ->assertSee('Erster Durchgang war stark')
        ->assertSee('Zweiter Durchgang auch')
        ->assertSee('4,3 von 5');
});

test('Bewertungen fremder Veranstalter werden nicht übernommen', function () {
    $foreign = pastEvent(Organization::factory()->create());
    approvedReviews($foreign, [5, 5, 5], ['comment' => 'Fremde Bewertung']);
    $own = futureEvent(Organization::factory()->create(), ['predecessor_id' => $foreign->id]);

    expect($own->predecessorIds())->toBe([]);

    $this->get(route('events.show', $own->slug))
        ->assertOk()
        ->assertDontSee('Fremde Bewertung');
});

test('eine vergangene Veranstaltung verweist auf den nächsten Termin', function () {
    $organization = Organization::factory()->create();
    $past = pastEvent($organization);
    $next = futureEvent($organization, ['predecessor_id' => $past->id, 'title' => 'Der nächste Durchgang']);

    expect($past->upcomingSuccessor()?->id)->toBe($next->id);

    $this->get(route('events.show', $past->slug))
        ->assertOk()
        ->assertSee('Nächster Termin')
        ->assertSee('Der nächste Durchgang');
});

test('beim Duplizieren wird das Original als früherer Termin verknüpft', function () {
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization();
    $event = pastEvent($organization);

    $this->actingAs($organizer)->post(route('organizer.events.duplicate', $event))->assertRedirect();

    $copy = Event::where('id', '!=', $event->id)->latest('id')->first();
    expect($copy->predecessor_id)->toBe($event->id);
});

test('ein früherer Termin eines anderen Veranstalters kann nicht gewählt werden', function () {
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization();
    $event = futureEvent($organization);
    $foreign = pastEvent(Organization::factory()->create(), ['title' => $event->title]);

    $this->actingAs($organizer)
        ->put(route('organizer.events.update', $event), eventUpdatePayload($event, ['predecessor_id' => $foreign->id]))
        ->assertSessionHasErrors('predecessor_id');

    expect($event->fresh()->predecessor_id)->toBeNull();
});

test('bei stark abweichendem Titel muss die Verknüpfung bestätigt werden', function () {
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization();
    $event = futureEvent($organization, ['title' => 'Klassenführung, die trägt']);
    $other = pastEvent($organization, ['title' => 'Steuerrecht für Fördervereine']);

    $this->actingAs($organizer)
        ->put(route('organizer.events.update', $event), eventUpdatePayload($event, ['predecessor_id' => $other->id]))
        ->assertSessionHasErrors('predecessor_id');
    expect($event->fresh()->predecessor_id)->toBeNull();

    $this->actingAs($organizer)
        ->put(route('organizer.events.update', $event), eventUpdatePayload($event, [
            'predecessor_id' => $other->id,
            'confirm_predecessor' => '1',
        ]))
        ->assertSessionHasNoErrors();
    expect($event->fresh()->predecessor_id)->toBe($other->id);
});

test('ähnliche Titel gelten als dasselbe Angebot', function () {
    expect(EventManagementController::titlesMatch('Klassenführung 2026', 'Klassenführung 2027 (Kopie)'))->toBeTrue()
        ->and(EventManagementController::titlesMatch('Klassenführung', 'Steuerrecht für Fördervereine'))->toBeFalse();
});

test('ein Kreis aus früheren Terminen ist nicht möglich', function () {
    $organization = Organization::factory()->create();
    $a = pastEvent($organization);
    $b = futureEvent($organization, ['predecessor_id' => $a->id]);

    expect($a->canHavePredecessor($b))->toBeFalse()
        ->and($b->canHavePredecessor($a))->toBeTrue();
});

// --- Moderation und Antworten ---------------------------------------------------------------

test('der Veranstalter kann öffentlich antworten', function () {
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization();
    $event = pastEvent($organization);
    $review = EventReview::factory()->create(['event_id' => $event->id, 'is_approved' => true, 'comment' => 'Zu kurz']);

    $this->actingAs($organizer)
        ->patch(route('organizer.reviews.reply', $review), ['organizer_reply' => 'Danke, wir planen mehr Zeit ein.'])
        ->assertSessionHas('success');

    expect($review->fresh()->organizer_reply)->toBe('Danke, wir planen mehr Zeit ein.')
        ->and($review->fresh()->organizer_replied_at)->not->toBeNull();

    $this->get(route('events.show', $event->slug))
        ->assertSee('Danke, wir planen mehr Zeit ein.')
        ->assertSee('Antwort von');
});

test('ein fremder Veranstalter kann nicht antworten', function () {
    ['organizer' => $organizer] = createOrganizerWithOrganization();
    $review = EventReview::factory()->create(['is_approved' => true]);

    $this->actingAs($organizer)
        ->patch(route('organizer.reviews.reply', $review), ['organizer_reply' => 'Hallo'])
        ->assertForbidden();
});

test('abgelehnte Bewertungen werden getrennt von wartenden geführt', function () {
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization();
    $review = EventReview::factory()->create(['event_id' => pastEvent($organization)->id, 'is_approved' => false]);

    $this->actingAs($organizer)->patch(route('organizer.reviews.reject', $review));

    expect($review->fresh()->isRejected())->toBeTrue()
        ->and(EventReview::pending()->count())->toBe(0)
        ->and(EventReview::rejected()->count())->toBe(1);

    $this->actingAs($organizer)->patch(route('organizer.reviews.approve', $review));
    expect($review->fresh()->is_approved)->toBeTrue()
        ->and($review->fresh()->rejected_at)->toBeNull();
});

test('die Administration sieht die Ablehnungsquote je Veranstalter', function () {
    $organization = Organization::factory()->create(['name' => 'Akademie Schönfärber']);
    $event = pastEvent($organization);
    approvedReviews($event, [5]);
    EventReview::factory()->create(['event_id' => $event->id, 'is_approved' => false, 'rejected_at' => now()]);

    $this->actingAs(createAdmin())
        ->get(route('admin.reviews.index'))
        ->assertOk()
        ->assertSee('Ablehnungen je Veranstalter')
        ->assertSee('Akademie Schönfärber')
        ->assertSee('50 %')
        ->assertSee('Auffällig viele Ablehnungen');
});

test('die Administration kann mehrere Bewertungen auf einmal freigeben', function () {
    $reviews = EventReview::factory()->count(2)->create(['is_approved' => false]);

    $this->actingAs(createAdmin())
        ->post(route('admin.reviews.bulk-approve'), ['review_ids' => $reviews->pluck('id')->all()])
        ->assertSessionHas('success');

    expect(EventReview::approved()->count())->toBe(2);
});

test('die Veranstalter-Seiten zu Bewertungen und Bearbeitung werden angezeigt', function () {
    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization();
    $past = pastEvent($organization);
    $event = futureEvent($organization, ['predecessor_id' => $past->id]);
    $review = EventReview::factory()->create([
        'event_id' => $past->id,
        'is_approved' => true,
        'organizer_reply' => 'Vielen Dank',
        'organizer_replied_at' => now(),
    ]);

    $this->actingAs($organizer)->get(route('organizer.reviews.index'))
        ->assertOk()
        ->assertSee('Öffentliche Bewertung');

    $this->actingAs($organizer)->get(route('organizer.reviews.moderate', $review))
        ->assertOk()
        ->assertSee('Öffentliche Antwort')
        ->assertSee('Vielen Dank');

    $this->actingAs($organizer)->get(route('organizer.events.edit', $event))
        ->assertOk()
        ->assertSee('Früherer Termin dieses Angebots')
        ->assertSee('1 freigegebene Bewertung');
});

test('Teilnehmende sehen ihre Bewertung und können sie ändern', function () {
    $user = User::factory()->create();
    $event = pastEvent(Organization::factory()->create());
    Booking::factory()->create(['user_id' => $user->id, 'event_id' => $event->id, 'status' => 'confirmed']);
    EventReview::factory()->create(['user_id' => $user->id, 'event_id' => $event->id, 'rejected_at' => now()]);

    $this->actingAs($user)->get(route('events.show', $event->slug))
        ->assertOk()
        ->assertSee('Ihre Bewertung wurde nicht veröffentlicht.')
        ->assertSee('Bewertung ändern');
});

/**
 * Vollständige Formulardaten für organizer.events.update.
 */
function eventUpdatePayload(Event $event, array $overrides = []): array
{
    return [
        'title' => $event->title,
        'description' => $event->description,
        'event_type' => 'physical',
        'event_category_id' => $event->event_category_id,
        'start_date' => $event->start_date->format('Y-m-d H:i:s'),
        'end_date' => $event->end_date->format('Y-m-d H:i:s'),
        'venue_name' => $event->venue_name ?? 'Schulzentrum',
        'venue_address' => $event->venue_address ?? 'Teststraße 1',
        'venue_city' => $event->venue_city ?? 'Dresden',
        'venue_postal_code' => $event->venue_postal_code ?? '01067',
        'venue_country' => $event->venue_country ?? 'Deutschland',
        'is_published' => true,
        'is_featured' => false,
        'is_private' => false,
        ...$overrides,
    ];
}
