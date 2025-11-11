<?php

use App\Models\User;
use App\Models\UserConnection;
use App\Notifications\ConnectionAcceptedNotification;
use App\Notifications\ConnectionRequestNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

test('user can view connections index page', function () {
    $this->actingAs($this->user)
        ->get(route('connections.index'))
        ->assertOk()
        ->assertSee('Meine Verbindungen');
});

test('connections index shows following and followers', function () {
    $follower = User::factory()->create();
    $following = User::factory()->create();

    UserConnection::factory()->accepted()->create([
        'follower_id' => $this->user->id,
        'following_id' => $following->id,
    ]);

    UserConnection::factory()->accepted()->create([
        'follower_id' => $follower->id,
        'following_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('connections.index'))
        ->assertOk()
        ->assertSee($following->fullName())
        ->assertSee($follower->fullName());
});

test('user can send connection request', function () {
    Notification::fake();

    $this->actingAs($this->user)
        ->post(route('connections.send', $this->otherUser))
        ->assertRedirect()
        ->assertSessionHas('success', 'Verbindungsanfrage gesendet.');

    $this->assertDatabaseHas('user_connections', [
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
        'status' => 'pending',
    ]);

    Notification::assertSentTo($this->otherUser, ConnectionRequestNotification::class);
});

test('user cannot send connection request to self', function () {
    $this->actingAs($this->user)
        ->post(route('connections.send', $this->user))
        ->assertRedirect()
        ->assertSessionHas('error', 'Sie können sich nicht mit sich selbst verbinden.');

    $this->assertDatabaseMissing('user_connections', [
        'follower_id' => $this->user->id,
        'following_id' => $this->user->id,
    ]);
});

test('user cannot send duplicate connection request', function () {
    UserConnection::factory()->pending()->create([
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
    ]);

    $this->actingAs($this->user)
        ->post(route('connections.send', $this->otherUser))
        ->assertRedirect()
        ->assertSessionHas('error');
});

test('user can accept connection request', function () {
    Notification::fake();

    $connection = UserConnection::factory()->pending()->create([
        'follower_id' => $this->otherUser->id,
        'following_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->post(route('connections.accept', $this->otherUser))
        ->assertRedirect()
        ->assertSessionHas('success', 'Verbindungsanfrage akzeptiert.');

    expect($connection->fresh()->status)->toBe('accepted');
    expect($connection->fresh()->accepted_at)->not->toBeNull();

    Notification::assertSentTo($this->otherUser, ConnectionAcceptedNotification::class);
});

test('user can decline connection request', function () {
    UserConnection::factory()->pending()->create([
        'follower_id' => $this->otherUser->id,
        'following_id' => $this->user->id,
    ]);

    $this->actingAs($this->user)
        ->post(route('connections.decline', $this->otherUser))
        ->assertRedirect()
        ->assertSessionHas('success', 'Verbindungsanfrage abgelehnt.');

    $this->assertDatabaseMissing('user_connections', [
        'follower_id' => $this->otherUser->id,
        'following_id' => $this->user->id,
    ]);
});

test('user can cancel sent connection request', function () {
    UserConnection::factory()->pending()->create([
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
    ]);

    $this->actingAs($this->user)
        ->post(route('connections.cancel', $this->otherUser))
        ->assertRedirect()
        ->assertSessionHas('success', 'Verbindungsanfrage zurückgezogen.');

    $this->assertDatabaseMissing('user_connections', [
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
    ]);
});

test('user can remove connection', function () {
    UserConnection::factory()->accepted()->create([
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
    ]);

    $this->actingAs($this->user)
        ->delete(route('connections.remove', $this->otherUser))
        ->assertRedirect()
        ->assertSessionHas('success', 'Verbindung entfernt.');

    $this->assertDatabaseMissing('user_connections', [
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
    ]);
});

test('user can block another user', function () {
    $this->actingAs($this->user)
        ->post(route('connections.block', $this->otherUser))
        ->assertRedirect()
        ->assertSessionHas('success', 'Benutzer blockiert.');

    $this->assertDatabaseHas('user_connections', [
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
        'status' => 'blocked',
    ]);
});

test('user cannot block themselves', function () {
    $this->actingAs($this->user)
        ->post(route('connections.block', $this->user))
        ->assertRedirect()
        ->assertSessionHas('error', 'Sie können sich nicht selbst blockieren.');
});

test('blocking user removes existing connection', function () {
    UserConnection::factory()->accepted()->create([
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
    ]);

    $this->actingAs($this->user)
        ->post(route('connections.block', $this->otherUser));

    $this->assertDatabaseMissing('user_connections', [
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
        'status' => 'accepted',
    ]);

    $this->assertDatabaseHas('user_connections', [
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
        'status' => 'blocked',
    ]);
});

test('user can unblock another user', function () {
    UserConnection::factory()->blocked()->create([
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
    ]);

    $this->actingAs($this->user)
        ->delete(route('connections.unblock', $this->otherUser))
        ->assertRedirect()
        ->assertSessionHas('success', 'Benutzer entsperrt.');

    $this->assertDatabaseMissing('user_connections', [
        'follower_id' => $this->user->id,
        'following_id' => $this->otherUser->id,
        'status' => 'blocked',
    ]);
});

test('user can view connection requests page', function () {
    $this->actingAs($this->user)
        ->get(route('connections.requests'))
        ->assertOk()
        ->assertSee('Verbindungsanfragen');
});

test('connection requests page shows received and sent requests', function () {
    $requester = User::factory()->create();
    $requested = User::factory()->create();

    // Received request
    UserConnection::factory()->pending()->create([
        'follower_id' => $requester->id,
        'following_id' => $this->user->id,
    ]);

    // Sent request
    UserConnection::factory()->pending()->create([
        'follower_id' => $this->user->id,
        'following_id' => $requested->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('connections.requests'))
        ->assertOk()
        ->assertSee($requester->fullName())
        ->assertSee($requested->fullName());
});

test('user can view suggestions page', function () {
    $this->actingAs($this->user)
        ->get(route('connections.suggestions'))
        ->assertOk()
        ->assertSee('Verbindungsvorschläge');
});

test('user can search for users', function () {
    $searchUser = User::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $this->actingAs($this->user)
        ->get(route('connections.search', ['q' => 'John']))
        ->assertOk()
        ->assertSee('John Doe');
});

test('user can view blocked users page', function () {
    $blocked = User::factory()->create();

    UserConnection::factory()->blocked()->create([
        'follower_id' => $this->user->id,
        'following_id' => $blocked->id,
    ]);

    $this->actingAs($this->user)
        ->get(route('connections.blocked'))
        ->assertOk()
        ->assertSee('Blockierte Nutzer')
        ->assertSee($blocked->fullName());
});

test('guest cannot access connection pages', function () {
    $this->get(route('connections.index'))
        ->assertRedirect(route('login'));

    $this->get(route('connections.requests'))
        ->assertRedirect(route('login'));

    $this->get(route('connections.suggestions'))
        ->assertRedirect(route('login'));

    $this->get(route('connections.search'))
        ->assertRedirect(route('login'));
});

test('guest cannot perform connection actions', function () {
    $this->post(route('connections.send', $this->otherUser))
        ->assertRedirect(route('login'));

    $this->post(route('connections.accept', $this->otherUser))
        ->assertRedirect(route('login'));

    $this->post(route('connections.decline', $this->otherUser))
        ->assertRedirect(route('login'));

    $this->delete(route('connections.remove', $this->otherUser))
        ->assertRedirect(route('login'));

    $this->post(route('connections.block', $this->otherUser))
        ->assertRedirect(route('login'));
});

