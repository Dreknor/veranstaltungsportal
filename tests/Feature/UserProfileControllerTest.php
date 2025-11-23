<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Models\Badge;
use App\Models\UserConnection;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->profileUser = User::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'bio' => 'Passionate educator',
        'show_profile_public' => true, // Make profile public for tests
    ]);
});

test('user can view another users profile', function () {
    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertSee('Jane Smith')
        ->assertSee('Passionate educator');
});

test('guest can view user profile', function () {
    $this->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertSee('Jane Smith');
});

test('profile shows connection button for non-connected users', function () {
    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertSee('Verbinden');
});

test('profile shows connected status for connected users', function () {
    UserConnection::factory()->accepted()->create([
        'follower_id' => $this->user->id,
        'following_id' => $this->profileUser->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertSee('Verbunden');
});

test('profile shows pending status for pending connections', function () {
    UserConnection::factory()->pending()->create([
        'follower_id' => $this->user->id,
        'following_id' => $this->profileUser->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertSee('Anfrage gesendet');
});

test('profile shows user statistics', function () {
    // Create attended events
    $event = Event::factory()->create();
    Booking::factory()->create([
        'user_id' => $this->profileUser->id,
        'event_id' => $event->id,
        'checked_in' => true,
    ]);

    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertSee('Events besucht');
});

test('profile shows user badges', function () {
    $badge = Badge::factory()->create();
    $this->profileUser->awardBadge($badge);

    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertSee('Auszeichnungen')
        ->assertSee($badge->name);
});

test('cannot view profile of user who blocked you', function () {
    UserConnection::factory()->blocked()->create([
        'follower_id' => $this->profileUser->id,
        'following_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertForbidden();
});

test('cannot view profile of user you blocked', function () {
    UserConnection::factory()->blocked()->create([
        'follower_id' => $this->user->id,
        'following_id' => $this->profileUser->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertForbidden();
});

test('user can view followers list', function () {
    $follower1 = User::factory()->create();
    $follower2 = User::factory()->create();

    UserConnection::factory()->accepted()->create([
        'follower_id' => $follower1->id,
        'following_id' => $this->profileUser->id,
    ]);

    UserConnection::factory()->accepted()->create([
        'follower_id' => $follower2->id,
        'following_id' => $this->profileUser->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('users.followers', $this->profileUser))
        ->assertOk()
        ->assertSee($follower1->fullName())
        ->assertSee($follower2->fullName());
});

test('user can view following list', function () {
    $following1 = User::factory()->create();
    $following2 = User::factory()->create();

    UserConnection::factory()->accepted()->create([
        'follower_id' => $this->profileUser->id,
        'following_id' => $following1->id,
    ]);

    UserConnection::factory()->accepted()->create([
        'follower_id' => $this->profileUser->id,
        'following_id' => $following2->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('users.following', $this->profileUser))
        ->assertOk()
        ->assertSee($following1->fullName())
        ->assertSee($following2->fullName());
});

test('profile shows contact info only to connected users', function () {
    $this->profileUser->update([
        'email' => 'jane@example.com',
        'phone' => '123-456-7890',
        'show_email_to_connections' => true,
        'show_phone_to_connections' => true,
    ]);

    // Not connected - should not see contact info
    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertDontSee('jane@example.com')
        ->assertSee('Verbinden Sie sich, um Kontaktdaten zu sehen');

    // Create connection
    UserConnection::factory()->accepted()->create([
        'follower_id' => $this->user->id,
        'following_id' => $this->profileUser->id,
    ]);

    // Connected - should see contact info
    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertSee('jane@example.com')
        ->assertSee('123-456-7890');
});

test('organizer profile shows organized events', function () {
    $organizer = User::factory()->create([
        'show_profile_public' => true,
    ]);
    $organizer->assignRole('organizer');
    $organization = test()->createOrganization($organizer);

    $event = Event::factory()->create([
        'organization_id' => $organization->id,
        'start_date' => now()->subDays(5),
    ]);

    test()->actingAs(test()->user)
        ->get(route('users.show', $organizer))
        ->assertOk()
        ->assertSee('Organisierte Veranstaltungen')
        ->assertSee($event->title);
});

test('participant profile shows attended events', function () {
    $event = Event::factory()->create(['start_date' => now()->subDays(5)]);

    Booking::factory()->create([
        'user_id' => $this->profileUser->id,
        'event_id' => $event->id,
        'checked_in' => true,
    ]);

    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertSee('Besuchte Veranstaltungen')
        ->assertSee($event->title);
});

test('profile shows activity feed', function () {
    $event = Event::factory()->create();
    Booking::factory()->create([
        'user_id' => $this->profileUser->id,
        'event_id' => $event->id,
        'checked_in' => true,
    ]);

    $badge = Badge::factory()->create();
    $this->profileUser->awardBadge($badge);

    $this->actingAs($this->user)
        ->get(route('users.show', $this->profileUser))
        ->assertOk()
        ->assertSee('Aktivität')
        ->assertSee('Veranstaltung(en) besucht')
        ->assertSee('Badge(s) verdient');
});

