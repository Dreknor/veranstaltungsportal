<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SsoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_sso_provider_validation_blocks_invalid_providers(): void
    {
        $response = $this->get('/sso/invalid-provider');

        $response->assertStatus(404);
    }

    public function test_keycloak_provider_is_allowed(): void
    {
        $response = $this->get('/sso/keycloak');

        // Should redirect to KeyCloak (or fail if not configured, but not 404)
        $this->assertTrue(in_array($response->status(), [302, 500]));
    }

    public function test_google_provider_is_allowed(): void
    {
        $response = $this->get('/sso/google');

        // Should redirect to Google (or fail if not configured, but not 404)
        $this->assertTrue(in_array($response->status(), [302, 500]));
    }

    public function test_github_provider_is_allowed(): void
    {
        $response = $this->get('/sso/github');

        // Should redirect to GitHub (or fail if not configured, but not 404)
        $this->assertTrue(in_array($response->status(), [302, 500]));
    }

    public function test_sso_user_cannot_change_password(): void
    {
        $user = User::factory()->create([
            'email' => 'sso@example.com',
            'sso_provider' => 'google',
            'google_id' => '123456',
            'password' => null,
        ]);

        $this->assertFalse($user->canChangePassword());
        $this->assertTrue($user->isSsoUser());
    }

    public function test_normal_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'email' => 'normal@example.com',
            'sso_provider' => null,
        ]);

        $this->assertTrue($user->canChangePassword());
        $this->assertFalse($user->isSsoUser());
    }

    public function test_sso_provider_name_is_formatted_correctly(): void
    {
        $user = User::factory()->create([
            'sso_provider' => 'keycloak',
        ]);

        $this->assertEquals('Keycloak', $user->ssoProviderName());
    }

    public function test_sso_provider_name_is_null_for_normal_users(): void
    {
        $user = User::factory()->create([
            'sso_provider' => null,
        ]);

        $this->assertNull($user->ssoProviderName());
    }
}

