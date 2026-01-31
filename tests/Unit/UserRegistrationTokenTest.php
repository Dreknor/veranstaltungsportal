<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserRegistrationToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_token_for_user()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = UserRegistrationToken::createForUser($user);

        $this->assertInstanceOf(UserRegistrationToken::class, $token);
        $this->assertEquals($user->id, $token->user_id);
        $this->assertEquals('test@example.com', $token->email);
        $this->assertNotNull($token->token);
        $this->assertNotNull($token->expires_at);
    }

    /** @test */
    public function token_is_unique_and_random()
    {
        $user = User::factory()->create();

        $token1 = UserRegistrationToken::createForUser($user);
        $token2 = UserRegistrationToken::createForUser($user);

        $this->assertNotEquals($token1->token, $token2->token);
    }

    /** @test */
    public function token_length_is_64_characters()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user);

        $this->assertEquals(64, strlen($token->token));
    }

    /** @test */
    public function token_expires_at_correct_time()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user, 5);

        $expectedExpiry = now()->addDays(5);

        $this->assertEqualsWithDelta(
            $expectedExpiry->timestamp,
            $token->expires_at->timestamp,
            60
        );
    }

    /** @test */
    public function is_expired_returns_true_when_token_is_expired()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::create([
            'user_id' => $user->id,
            'token' => 'test-token',
            'email' => $user->email,
            'expires_at' => now()->subDay(),
        ]);

        $this->assertTrue($token->isExpired());
    }

    /** @test */
    public function is_expired_returns_false_when_token_is_valid()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user);

        $this->assertFalse($token->isExpired());
    }

    /** @test */
    public function token_belongs_to_user_relationship()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
        ]);

        $token = UserRegistrationToken::createForUser($user);

        $this->assertInstanceOf(User::class, $token->user);
        $this->assertEquals('John Doe', $token->user->name);
    }

    /** @test */
    public function expires_at_is_casted_to_datetime()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $token->expires_at);
    }

    /** @test */
    public function it_stores_email_copy()
    {
        $user = User::factory()->create([
            'email' => 'original@example.com',
        ]);

        $token = UserRegistrationToken::createForUser($user);

        // Change user email
        $user->update(['email' => 'changed@example.com']);

        // Token should still have original email
        $this->assertEquals('original@example.com', $token->fresh()->email);
    }

    /** @test */
    public function default_expiry_is_7_days()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user);

        $expectedExpiry = now()->addDays(7);

        $this->assertEqualsWithDelta(
            $expectedExpiry->timestamp,
            $token->expires_at->timestamp,
            60
        );
    }

    /** @test */
    public function can_find_token_by_token_string()
    {
        $user = User::factory()->create();

        $token = UserRegistrationToken::createForUser($user);

        $found = UserRegistrationToken::where('token', $token->token)->first();

        $this->assertNotNull($found);
        $this->assertEquals($token->id, $found->id);
    }

    /** @test */
    public function token_is_unique_in_database()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $token1 = UserRegistrationToken::createForUser($user1);
        $token2 = UserRegistrationToken::createForUser($user2);

        $this->assertNotEquals($token1->token, $token2->token);

        $this->assertDatabaseHas('user_registration_tokens', [
            'token' => $token1->token,
        ]);

        $this->assertDatabaseHas('user_registration_tokens', [
            'token' => $token2->token,
        ]);
    }
}
