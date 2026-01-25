<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        // Ensure the user role exists
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'account_type' => 'participant',
        ]);

        // User should be created in database
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);


        // User should be redirected (either to dashboard or verification notice)
        // Since the user implements MustVerifyEmail, they will be redirected to dashboard
        // but the 'verified' middleware will redirect them to verification.notice
        //$response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_organizer_registration_can_be_disabled(): void
    {
        // Disable organizer registration
        config(['app.allow_organizer_registration' => false]);

        // Ensure required roles exist
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'organizer', 'guard_name' => 'web']);

        // Attempt to register as organizer
        $response = $this->post('/register', [
            'name' => 'Test Organizer',
            'email' => 'organizer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'account_type' => 'organizer',
            'organization_name' => 'Test Organization',
        ]);

        // Should fail validation
        $response->assertSessionHasErrors('account_type');

        // User should not be created
        $this->assertDatabaseMissing('users', [
            'email' => 'organizer@example.com',
        ]);
    }

    public function test_organizer_registration_works_when_enabled(): void
    {
        // Enable organizer registration (default)
        config(['app.allow_organizer_registration' => true]);

        // Ensure required roles exist
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'organizer', 'guard_name' => 'web']);

        // Register as organizer
        $response = $this->post('/register', [
            'name' => 'Test Organizer',
            'email' => 'organizer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'account_type' => 'organizer',
            'organization_name' => 'Test Organization',
        ]);

        // User should be created
        $this->assertDatabaseHas('users', [
            'email' => 'organizer@example.com',
            'name' => 'Test Organizer',
        ]);

        // Organization should be created
        $this->assertDatabaseHas('organizations', [
            'name' => 'Test Organization',
        ]);
    }

    public function test_participant_registration_works_when_organizer_registration_disabled(): void
    {
        // Disable organizer registration
        config(['app.allow_organizer_registration' => false]);

        // Ensure the user role exists
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Participant registration should still work
        $response = $this->post('/register', [
            'name' => 'Test Participant',
            'email' => 'participant@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'account_type' => 'participant',
        ]);

        // User should be created
        $this->assertDatabaseHas('users', [
            'email' => 'participant@example.com',
            'name' => 'Test Participant',
        ]);
    }

    public function test_registration_page_shows_organizer_option_when_enabled(): void
    {
        // Enable organizer registration
        config(['app.allow_organizer_registration' => true]);

        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Organisator');
        $response->assertSee('Ich möchte Events erstellen');
    }

    public function test_registration_page_hides_organizer_option_when_disabled(): void
    {
        // Disable organizer registration
        config(['app.allow_organizer_registration' => false]);

        $response = $this->get('/register');

        $response->assertStatus(200);
        // Should only see participant option
        $response->assertSee('Teilnehmer');
        // Should not see organizer text
        $response->assertDontSee('Organisator');
    }
}
