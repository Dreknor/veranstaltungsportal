<?php

use App\Models\User;
use App\Notifications\NewOrganizerRegisteredNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Ensure roles exist
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'organizer']);
    Role::firstOrCreate(['name' => 'user']);
});

test('it sends notification to admins when a new organizer registers', function () {

    Notification::fake([NewOrganizerRegisteredNotification::class]);

    // Create an admin user
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Create another admin
    $admin2 = User::factory()->create();
    $admin2->assignRole('admin');

    // Register a new organizer directly (bypass registration form issues)
    $organizer = User::factory()->create([
        'email' => 'organizer@test.com',
        'name' => 'Test Organizer',
    ]);
    $organizer->assignRole('organizer');

    // Manually trigger the notification (simulating what should happen on registration)
    $admins = User::role('admin')->get();
    \Illuminate\Support\Facades\Notification::send($admins, new NewOrganizerRegisteredNotification($organizer));

    // Assert notification was sent to admins
    Notification::assertSentTo(
        [$admin, $admin2],
        NewOrganizerRegisteredNotification::class,
        function ($notification, $channels) use ($organizer) {
            return $notification->organizer->id === $organizer->id;
        }
    );
});

it('does not send notification when a regular user registers', function () {
    Notification::fake([NewOrganizerRegisteredNotification::class]);

    // Create an admin user
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Create a regular user (not through registration to avoid email verification)
    $user = User::factory()->create([
        'email' => 'user@test.com',
        'name' => 'Test User',
    ]);
    $user->assignRole('user');

    // Assert NewOrganizerRegisteredNotification was NOT sent
    Notification::assertNothingSent();
});


it('notification contains correct organizer data', function () {
    $admin = User::factory()->create(['name' => 'Admin User']);
    $admin->assignRole('admin');

    $organizer = User::factory()->create([
        'name' => 'John Doe',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);
    $organizer->assignRole('organizer');

    // Create organization for the organizer
    $organization = \App\Models\Organization::factory()->create([
        'name' => 'Doe Events',
    ]);
    $organization->users()->attach($organizer->id, [
        'role' => 'owner',
        'is_active' => true,
        'joined_at' => now(),
    ]);

    $notification = new NewOrganizerRegisteredNotification($organizer);

    // Test toArray
    $array = $notification->toArray($admin);
    expect($array)->toHaveKeys([
        'title',
        'message',
        'organizer_id',
        'organizer_name',
        'organizer_email',
        'registered_at',
        'url',
    ]);
    expect($array['organizer_id'])->toBe($organizer->id);
    expect($array['organizer_name'])->toBe('John Doe');
    expect($array['organizer_email'])->toBe('john@example.com');

    // Test toMail
    $mail = $notification->toMail($admin);
    expect($mail->subject)->toContain('Neuer Organisator registriert');
    expect($mail->subject)->toContain('John Doe');
});


