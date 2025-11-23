<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toBeWithinRange', function (int $min, int $max) {
    return $this->toBeGreaterThanOrEqual($min)
        ->toBeLessThanOrEqual($max);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createUser(array $attributes = []): \App\Models\User
{
    return \App\Models\User::factory()->create($attributes);
}

function createEvent(array $attributes = []): \App\Models\Event
{
    return \App\Models\Event::factory()->create($attributes);
}

function createBooking(array $attributes = []): \App\Models\Booking
{
    return \App\Models\Booking::factory()->create($attributes);
}

function createOrganizer(array $attributes = []): \App\Models\User
{
    // Ensure organizer role exists
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'organizer']);

    $user = \App\Models\User::factory()->create(array_merge(['user_type' => 'organizer'], $attributes));
    $user->assignRole('organizer');

    // Create an organization for the organizer
    $organization = \App\Models\Organization::factory()->create();
    $organization->users()->attach($user->id, [
        'role' => 'owner',
        'is_active' => true,
        'joined_at' => now(),
    ]);

    // Refresh the user to load the relationship
    $user->refresh();

    return $user;
}

function createAdmin(array $attributes = []): \App\Models\User
{
    $admin = \App\Models\User::factory()->create($attributes);
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
    $admin->assignRole($role);
    return $admin;
}

/**
 * Create an organizer user with organization
 */
function createOrganizerWithOrganization(array $userAttributes = [], array $orgAttributes = []): array
{
    // Ensure organizer role exists
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'organizer']);

    $user = \App\Models\User::factory()->create($userAttributes);
    $user->assignRole('organizer');

    // Create organization for the user
    $organization = \App\Models\Organization::factory()->create($orgAttributes);
    $organization->users()->attach($user->id, [
        'role' => 'owner',
        'is_active' => true,
        'joined_at' => now(),
    ]);

    return ['user' => $user, 'organization' => $organization];
}

/**
 * Setup default roles for testing
 */
function setupRoles(): void
{
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'organizer']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user']);
}

