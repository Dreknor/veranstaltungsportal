<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class SsoController extends Controller
{
    /**
     * Redirect to SSO provider for authentication
     */
    public function redirectToProvider(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback from SSO provider
     */
    public function handleProviderCallback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            $providerUser = Socialite::driver($provider)->user();

            // Find or create user
            $user = $this->findOrCreateUser($providerUser, $provider);

            // Log in the user
            Auth::login($user, true);

            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            Log::error("SSO Error ({$provider}): " . $e->getMessage());

            return redirect()->route('login')
                ->with('error', "Authentifizierung mit " . ucfirst($provider) . " fehlgeschlagen. Bitte versuchen Sie es erneut.");
        }
    }

    /**
     * Validate that the provider is supported
     */
    protected function validateProvider(string $provider): void
    {
        $allowedProviders = ['keycloak', 'google', 'github'];

        if (!in_array($provider, $allowedProviders)) {
            abort(404, 'SSO-Provider nicht unterstützt');
        }
    }

    /**
     * Find or create user from SSO provider data
     */
    protected function findOrCreateUser($providerUser, string $provider): User
    {
        // Determine the provider ID field
        $providerIdField = $provider . '_id';

        // Try to find user by provider ID first
        $user = User::where($providerIdField, $providerUser->getId())->first();

        // If not found, try to find by email
        if (!$user) {
            $user = User::where('email', $providerUser->getEmail())->first();

            if ($user) {
                // Link existing account to SSO provider
                $user->update([
                    $providerIdField => $providerUser->getId(),
                    'sso_provider' => $provider,
                ]);
            }
        }

        // Create new user if still not found
        if (!$user) {
            $user = $this->createUserFromProvider($providerUser, $provider);
        }

        return $user;
    }

    /**
     * Create a new user from SSO provider data
     */
    protected function createUserFromProvider($providerUser, string $provider): User
    {
        // Extract name parts
        $name = $providerUser->getName() ?? $providerUser->getNickname() ?? $providerUser->getEmail();
        $nameParts = explode(' ', $name, 2);

        // Determine the provider ID field
        $providerIdField = $provider . '_id';

        $user = User::create([
            $providerIdField => $providerUser->getId(),
            'sso_provider' => $provider,
            'name' => $name,
            'first_name' => $nameParts[0] ?? '',
            'last_name' => $nameParts[1] ?? '',
            'email' => $providerUser->getEmail(),
            'email_verified_at' => now(), // SSO users are pre-verified
            'password' => null, // No password for SSO users
        ]);

        // Assign participant role by default
        $this->assignDefaultRole($user);

        return $user;
    }

    /**
     * Assign default role to user (participant unless already organizer)
     */
    protected function assignDefaultRole(User $user): void
    {
        // Check if user already has organizer or admin role
        if ($user->hasRole(['organizer', 'admin'])) {
            return;
        }

        // Ensure the 'user' role exists
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign participant role
        if (!$user->hasRole('user')) {
            $user->assignRole($userRole);
        }
    }
}

