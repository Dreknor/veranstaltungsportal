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

it('sends notification to admins when a new organizer registers', function () {
    Notification::fake();

    // Create an admin user
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Create another admin
    $admin2 = User::factory()->create();
    $admin2->assignRole('admin');

    // Register a new organizer
    $response = $this->post('/register', [
        'name' => 'Test Organizer',
        'email' => 'organizer@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'account_type' => 'organizer',
        'organization_name' => 'Test Organization',
    ]);

    // Get the newly created organizer
    $organizer = User::where('email', 'organizer@test.com')->first();
    expect($organizer)->not->toBeNull();
    expect($organizer->hasRole('organizer'))->toBeTrue();

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
    Notification::fake();

    // Create an admin user
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Register a regular user
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'user@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'account_type' => 'participant',
    ]);

    // Assert notification was NOT sent
    Notification::assertNothingSent();
});

it('sends notification when admin promotes user to organizer', function () {
    Notification::fake();

    // Create an admin user
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Create a regular user
    $user = User::factory()->create();
    $user->assignRole('user');

    // Act as admin and promote user
    $this->actingAs($admin);
    $response = $this->post(route('admin.users.promote-organizer', $user));

    // Refresh user
    $user->refresh();
    expect($user->hasRole('organizer'))->toBeTrue();

    // Assert notification was sent to other admins
    Notification::assertSentTo(
        [$admin],
        NewOrganizerRegisteredNotification::class,
        function ($notification, $channels) use ($user) {
            return $notification->organizer->id === $user->id;
        }
    );
});

it('notification contains correct organizer data', function () {
    $admin = User::factory()->create(['name' => 'Admin User']);
    $admin->assignRole('admin');

    $organizer = User::factory()->create([
        'name' => 'John Doe',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'organization_name' => 'Doe Events',
    ]);
    $organizer->assignRole('organizer');

    $notification = new NewOrganizerRegisteredNotification($organizer);

    // Test toArray
    $array = $notification->toArray($admin);
    expect($array)->toHaveKeys([
        'title',
        'message',
        'organizer_id',
        'organizer_name',
        'organizer_email',
        'organization_name',
        'registered_at',
        'url',
    ]);
    expect($array['organizer_id'])->toBe($organizer->id);
    expect($array['organizer_name'])->toBe('John Doe');
    expect($array['organizer_email'])->toBe('john@example.com');
    expect($array['organization_name'])->toBe('Doe Events');

    // Test toMail
    $mail = $notification->toMail($admin);
    expect($mail->subject)->toContain('Neuer Organisator registriert');
    expect($mail->subject)->toContain('John Doe');
});


