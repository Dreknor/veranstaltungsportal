<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');
});

test('migration moves profile photo from public to private (local) storage', function () {
    $user = User::factory()->create(['profile_photo' => 'profile-photos/migrate.jpg']);

    Storage::disk('public')->put('profile-photos/migrate.jpg', 'public-image');

    $this->artisan('profile:migrate-to-private')
        ->expectsOutput('Starting migration of profile photos from public to private storage...')
        ->assertExitCode(0);

    Storage::disk('public')->assertMissing('profile-photos/migrate.jpg');
    Storage::disk('local')->assertExists('profile-photos/migrate.jpg');
});

test('migration skips already private photo', function () {
    $user = User::factory()->create(['profile_photo' => 'profile-photos/already.jpg']);
    Storage::disk('local')->put('profile-photos/already.jpg', 'already-private');

    $this->artisan('profile:migrate-to-private')
        ->assertExitCode(0);

    Storage::disk('local')->assertExists('profile-photos/already.jpg');
});

test('migration cleans missing file entry', function () {
    $user = User::factory()->create(['profile_photo' => 'profile-photos/missing.jpg']);

    $this->artisan('profile:migrate-to-private')
        ->expectsOutput('Starting migration of profile photos from public to private storage...')
        ->assertExitCode(0);

    $user->refresh();
    expect($user->profile_photo)->toBeNull();
});


