<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

// Test: Upload über Settings Controller

test('user can upload profile photo via settings controller to local storage', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

    $response = $this->actingAs($user)->put(route('settings.profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'profile_photo' => $file,
    ]);

    $response->assertRedirect(route('settings.profile.edit'));

    $user->refresh();
    expect($user->profile_photo)->not()->toBeNull();
    expect($user->profile_photo)->toStartWith('profile-photos/');
    Storage::disk('local')->assertExists($user->profile_photo);
});

// Test: Upload über Organizer Controller

test('organizer can upload profile photo to local storage', function () {
    $data = $this->createOrganizerWithOrganization();
    $organizer = $data['organizer'];
    $file = UploadedFile::fake()->image('organizer.jpg', 120, 120);

    $response = $this->actingAs($organizer)->put(route('organizer.profile.update'), [
        'first_name' => $organizer->first_name,
        'last_name' => $organizer->last_name,
        'email' => $organizer->email,
        'profile_photo' => $file,
    ]);

    $response->assertRedirect(route('organizer.profile.edit'));

    $organizer->refresh();
    expect($organizer->profile_photo)->not()->toBeNull();
    Storage::disk('local')->assertExists($organizer->profile_photo);
});

// Test: Ersetzen vorhandenen Profilbildes (Settings Controller)

test('uploading a new profile photo deletes the old one', function () {
    $user = User::factory()->create();

    // Erstes Bild hochladen
    $file1 = UploadedFile::fake()->image('first.jpg', 80, 80);
    $this->actingAs($user)->put(route('settings.profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'profile_photo' => $file1,
    ])->assertRedirect(route('settings.profile.edit'));

    $user->refresh();
    $oldPath = $user->profile_photo;
    Storage::disk('local')->assertExists($oldPath);

    // Zweites Bild hochladen (ersetzt altes)
    $file2 = UploadedFile::fake()->image('second.jpg', 90, 90);
    $this->actingAs($user)->put(route('settings.profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'profile_photo' => $file2,
    ])->assertRedirect(route('settings.profile.edit'));

    $user->refresh();
    $newPath = $user->profile_photo;
    expect($newPath)->not()->toBe($oldPath);
    Storage::disk('local')->assertExists($newPath);
    Storage::disk('local')->assertMissing($oldPath); // Altes Bild wurde gelöscht
});
