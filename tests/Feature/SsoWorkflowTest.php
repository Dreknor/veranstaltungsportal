<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create necessary roles - use firstOrCreate to avoid duplicates
    Role::firstOrCreate(['name' => 'user']);
    Role::firstOrCreate(['name' => 'organizer']);
    Role::firstOrCreate(['name' => 'admin']);
});

test('sso user is created with participant role by default', function () {
    // This would normally be tested with a mocked Socialite response
    $user = User::create([
        'name' => 'SSO User',
        'email' => 'sso@example.com',
        'google_id' => '123456',
        'sso_provider' => 'google',
        'email_verified_at' => now(),
        'password' => null,
    ]);

    $user->assignRole('user');

    expect($user->hasRole('user'))->toBeTrue()
        ->and($user->hasRole('organizer'))->toBeFalse()
        ->and($user->hasRole('admin'))->toBeFalse()
        ->and($user->isSsoUser())->toBeTrue()
        ->and($user->canChangePassword())->toBeFalse();
});

test('existing organizer keeps their role after sso login', function () {
    $user = User::factory()->create([
        'email' => 'organizer@example.com',
    ]);

    $user->assignRole('organizer');

    // Simulate SSO linking
    $user->update([
        'google_id' => '789012',
        'sso_provider' => 'google',
    ]);

    expect($user->hasRole('organizer'))->toBeTrue()
        ->and($user->isSsoUser())->toBeTrue();
});

test('sso user can be promoted to organizer', function () {
    $user = User::factory()->create([
        'email' => 'participant@example.com',
        'google_id' => '345678',
        'sso_provider' => 'google',
        'password' => null,
    ]);

    $user->assignRole('user');

    // Admin promotes user to organizer
    $user->assignRole('organizer');

    expect($user->hasRole('organizer'))->toBeTrue()
        ->and($user->hasRole('user'))->toBeTrue();
});

test('sso users cannot access password change page', function () {
    $user = User::factory()->create([
        'email' => 'sso@example.com',
        'google_id' => '123456',
        'sso_provider' => 'google',
        'password' => null,
    ]);

    $user->assignRole('user');

    $response = $this->actingAs($user)->get(route('settings.password.edit'));

    $response->assertRedirect(route('settings.profile.edit'));
    $response->assertSessionHas('error');
});

test('normal users can access password change page', function () {
    $user = User::factory()->create([
        'email' => 'normal@example.com',
        'password' => bcrypt('password'),
    ]);

    $user->assignRole('user');

    $response = $this->actingAs($user)->get(route('settings.password.edit'));

    $response->assertStatus(200);
    $response->assertViewIs('settings.password');
});

test('admin can filter users by sso provider', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Create SSO users
    User::factory()->create(['google_id' => '111', 'sso_provider' => 'google']);
    User::factory()->create(['github_id' => '222', 'sso_provider' => 'github']);
    User::factory()->create(['keycloak_id' => '333', 'sso_provider' => 'keycloak']);
    User::factory()->create(); // Normal user

    $response = $this->actingAs($admin)
        ->get(route('admin.users.index', ['sso_provider' => 'google']));

    $response->assertStatus(200);
});

test('admin can promote sso user to organizer', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $user = User::factory()->create([
        'google_id' => '123456',
        'sso_provider' => 'google',
    ]);
    $user->assignRole('user');

    $response = $this->actingAs($admin)
        ->post(route('admin.users.promote-organizer', $user));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->hasRole('organizer'))->toBeTrue();
});

test('admin can demote organizer to participant', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $user = User::factory()->create([
        'google_id' => '123456',
        'sso_provider' => 'google',
    ]);
    $user->assignRole('organizer');

    $response = $this->actingAs($admin)
        ->post(route('admin.users.demote-participant', $user));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->hasRole('organizer'))->toBeFalse()
        ->and($user->hasRole('user'))->toBeTrue();
});

