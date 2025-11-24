<?php

use App\Models\User;
use App\Models\UserConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

test('authenticated user can view their own profile photo', function () {
    $user = User::factory()->create([
        'profile_photo' => 'profile-photos/test.jpg',
    ]);

    // Create a fake image file
    Storage::disk('local')->put('profile-photos/test.jpg', 'fake-image-content');

    $response = $this->actingAs($user)->get(route('profile-photo.show', $user));

    $response->assertStatus(200);
});

test('authenticated user can view public profile photo', function () {
    $owner = User::factory()->create([
        'profile_photo' => 'profile-photos/test.jpg',
        'show_profile_public' => true,
    ]);

    $viewer = User::factory()->create();

    Storage::disk('local')->put('profile-photos/test.jpg', 'fake-image-content');

    $response = $this->actingAs($viewer)->get(route('profile-photo.show', $owner));

    $response->assertStatus(200);
});

test('authenticated user cannot view private profile photo of non-connected user', function () {
    $owner = User::factory()->create([
        'profile_photo' => 'profile-photos/test.jpg',
        'show_profile_public' => false,
        'allow_networking' => false,
    ]);

    $viewer = User::factory()->create();

    Storage::disk('local')->put('profile-photos/test.jpg', 'fake-image-content');

    $response = $this->actingAs($viewer)->get(route('profile-photo.show', $owner));

    $response->assertStatus(403);
});

test('guest cannot view profile photo', function () {
    $user = User::factory()->create([
        'profile_photo' => 'profile-photos/test.jpg',
    ]);

    Storage::disk('local')->put('profile-photos/test.jpg', 'fake-image-content');

    $response = $this->get(route('profile-photo.show', $user));

    $response->assertStatus(302); // Redirect to login
});

test('returns 404 when user has no profile photo', function () {
    $user = User::factory()->create([
        'profile_photo' => null,
    ]);

    $response = $this->actingAs($user)->get(route('profile-photo.show', $user));

    $response->assertStatus(404);
});

test('returns 404 when user does not exist', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/profile-photo/99999');

    $response->assertStatus(404);
});

test('returns 404 when profile photo file does not exist', function () {
    $user = User::factory()->create([
        'profile_photo' => 'profile-photos/nonexistent.jpg',
    ]);

    $response = $this->actingAs($user)->get(route('profile-photo.show', $user));

    $response->assertStatus(404);
});

test('connected user can view profile photo when networking allowed', function () {
    $owner = User::factory()->create([
        'profile_photo' => 'profile-photos/test-network.jpg',
        'show_profile_public' => false,
        'allow_networking' => true,
    ]);
    $viewer = User::factory()->create();

    // Verbindung anlegen (accepted)
    UserConnection::factory()->create([
        'follower_id' => $viewer->id,
        'following_id' => $owner->id,
        'status' => 'accepted',
    ]);

    Storage::disk('local')->put('profile-photos/test-network.jpg', 'fake-image-content');

    $response = $this->actingAs($viewer)->get(route('profile-photo.show', $owner));
    $response->assertStatus(200);
});

test('deleting profile photo results in gravatar fallback url', function () {
    $user = User::factory()->create([
        'profile_photo' => 'profile-photos/delete-me.jpg',
    ]);
    Storage::disk('local')->put('profile-photos/delete-me.jpg', 'fake-image-content');

    // Löschen über Settings Route
    $this->actingAs($user)->delete(route('settings.profile.photo.delete'))
        ->assertRedirect(route('settings.profile.edit'));

    $user->refresh();
    expect($user->profile_photo)->toBeNull();
    expect($user->profilePhotoUrl())->toContain('gravatar.com/avatar');
});
