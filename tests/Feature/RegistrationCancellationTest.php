<?php

namespace Tests\Feature;

use App\Mail\RegistrationCancelledConfirmation;
use App\Models\User;
use App\Models\UserRegistrationToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationCancellationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_cancellation_confirmation_page_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = UserRegistrationToken::createForUser($user);

        $response = $this->get(route('user.cancel-registration', $token->token));

        $response->assertOk();
        $response->assertViewIs('user.registration-cancel-confirm');
        $response->assertViewHas('email', 'test@example.com');
        $response->assertViewHas('token', $token->token);
    }

    /** @test */
    public function it_shows_error_page_with_invalid_token()
    {
        $response = $this->get(route('user.cancel-registration', 'invalid-token'));

        $response->assertOk();
        $response->assertViewIs('user.registration-cancel-error');
        $response->assertSee('Ungültiger oder abgelaufener Link');
    }

    /** @test */
    public function it_shows_error_page_with_expired_token()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::create([
            'user_id' => $user->id,
            'token' => bin2hex(random_bytes(32)),
            'email' => $user->email,
            'expires_at' => now()->subDay(), // Expired yesterday
        ]);

        $response = $this->get(route('user.cancel-registration', $token->token));

        $response->assertOk();
        $response->assertViewIs('user.registration-cancel-error');
        $response->assertSee('abgelaufen');

        // Assert expired token was deleted
        $this->assertDatabaseMissing('user_registration_tokens', [
            'id' => $token->id,
        ]);
    }

    /** @test */
    public function it_deletes_user_account_when_cancellation_is_confirmed()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = UserRegistrationToken::createForUser($user);

        $response = $this->post(route('user.cancel-registration.process', $token->token));

        $response->assertOk();
        $response->assertViewIs('user.registration-cancelled-success');

        // Assert user was deleted
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        // Assert token was deleted (cascade)
        $this->assertDatabaseMissing('user_registration_tokens', [
            'id' => $token->id,
        ]);

        // Assert confirmation email was sent
        Mail::assertSent(RegistrationCancelledConfirmation::class, function ($mail) {
            return $mail->hasTo('test@example.com')
                && $mail->email === 'test@example.com';
        });
    }

    /** @test */
    public function it_redirects_to_home_when_cancelling_with_invalid_token()
    {
        $response = $this->post(route('user.cancel-registration.process', 'invalid-token'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Ungültiger oder abgelaufener Link.');
    }

    /** @test */
    public function it_redirects_to_home_when_cancelling_with_expired_token()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::create([
            'user_id' => $user->id,
            'token' => bin2hex(random_bytes(32)),
            'email' => $user->email,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->post(route('user.cancel-registration.process', $token->token));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');

        // Assert user was NOT deleted
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function cancellation_token_is_64_characters_long()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user);

        $this->assertEquals(64, strlen($token->token));
    }

    /** @test */
    public function token_expires_in_7_days_by_default()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user);

        $this->assertEqualsWithDelta(
            now()->addDays(7)->timestamp,
            $token->expires_at->timestamp,
            60 // 1 minute tolerance
        );
    }

    /** @test */
    public function token_can_have_custom_expiry_days()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user, 14);

        $this->assertEqualsWithDelta(
            now()->addDays(14)->timestamp,
            $token->expires_at->timestamp,
            60
        );
    }

    /** @test */
    public function is_expired_method_returns_true_for_expired_tokens()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::create([
            'user_id' => $user->id,
            'token' => bin2hex(random_bytes(32)),
            'email' => $user->email,
            'expires_at' => now()->subHour(),
        ]);

        $this->assertTrue($token->isExpired());
    }

    /** @test */
    public function is_expired_method_returns_false_for_valid_tokens()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user);

        $this->assertFalse($token->isExpired());
    }

    /** @test */
    public function token_belongs_to_user()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user);

        $this->assertEquals($user->id, $token->user->id);
    }

    /** @test */
    public function deleting_user_also_deletes_registration_tokens()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user);

        $tokenId = $token->id;

        $user->delete();

        $this->assertDatabaseMissing('user_registration_tokens', [
            'id' => $tokenId,
        ]);
    }

    /** @test */
    public function multiple_tokens_can_be_created_for_same_user()
    {
        $user = User::factory()->create();

        $token1 = UserRegistrationToken::createForUser($user);
        $token2 = UserRegistrationToken::createForUser($user);

        $this->assertNotEquals($token1->token, $token2->token);
        $this->assertDatabaseHas('user_registration_tokens', ['id' => $token1->id]);
        $this->assertDatabaseHas('user_registration_tokens', ['id' => $token2->id]);
    }

    /** @test */
    public function cancellation_confirmation_email_contains_correct_email()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = UserRegistrationToken::createForUser($user);

        $this->post(route('user.cancel-registration.process', $token->token));

        Mail::assertSent(RegistrationCancelledConfirmation::class, function ($mail) {
            return $mail->email === 'test@example.com';
        });
    }

    /** @test */
    public function user_with_organization_membership_can_cancel_registration()
    {
        Mail::fake();

        $user = User::factory()->create();
        $organization = \App\Models\Organization::factory()->create();

        $organization->users()->attach($user->id, [
            'role' => 'member',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $token = UserRegistrationToken::createForUser($user);

        $response = $this->post(route('user.cancel-registration.process', $token->token));

        $response->assertOk();

        // Assert user was deleted (cascade should handle organization membership)
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        // Assert organization membership was removed
        $this->assertDatabaseMissing('organization_user', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function cancellation_page_shows_user_email()
    {
        $user = User::factory()->create([
            'email' => 'john.doe@example.com',
        ]);

        $token = UserRegistrationToken::createForUser($user);

        $response = $this->get(route('user.cancel-registration', $token->token));

        $response->assertSee('john.doe@example.com');
    }

    /** @test */
    public function success_page_shows_confirmation_message()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = UserRegistrationToken::createForUser($user);

        $response = $this->post(route('user.cancel-registration.process', $token->token));

        $response->assertSee('erfolgreich storniert');
        $response->assertSee('test@example.com');
    }
}
