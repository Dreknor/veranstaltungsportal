<?php

namespace Tests\Feature;

use App\Mail\NewUserAccountCreated;
use App\Mail\OrganizationInvitation;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserRegistrationToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrganizationTeamInvitationTest extends TestCase
{
    use RefreshDatabase;

    protected User $organizer;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        // Create organizer with organization
        $this->organizer = User::factory()->create([
            'is_organizer' => true,
        ]);

        $this->organization = Organization::factory()->create();
        $this->organization->users()->attach($this->organizer->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $this->organizer->setCurrentOrganization($this->organization);
    }

    /** @test */
    public function it_can_invite_existing_user_to_organization()
    {
        Mail::fake();

        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => 'existing@example.com',
                'role' => 'member',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Teammitglied erfolgreich hinzugefügt!');

        // Assert user is attached to organization
        $this->assertTrue($this->organization->users()->where('user_id', $existingUser->id)->exists());

        // Assert invitation email was sent
        Mail::assertSent(OrganizationInvitation::class, function ($mail) use ($existingUser) {
            return $mail->hasTo($existingUser->email);
        });
    }

    /** @test */
    public function it_shows_warning_when_inviting_non_existing_user_without_create_account()
    {
        Mail::fake();

        $response = $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => 'newuser@example.com',
                'role' => 'admin',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');
        $response->assertSessionHas('pending_email', 'newuser@example.com');
        $response->assertSessionHas('pending_role', 'admin');

        // Assert no user was created
        $this->assertDatabaseMissing('users', [
            'email' => 'newuser@example.com',
        ]);

        // Assert no email was sent
        Mail::assertNothingSent();
    }

    /** @test */
    public function it_creates_account_for_non_existing_user_when_create_account_is_true()
    {
        Mail::fake();

        $response = $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => 'newuser@example.com',
                'role' => 'member',
                'create_account' => true,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Konto wurde erstellt und der Benutzer per E-Mail mit Zugangsdaten informiert!');

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'is_organizer' => true,
        ]);

        $newUser = User::where('email', 'newuser@example.com')->first();

        // Assert user is attached to organization
        $this->assertTrue($this->organization->users()->where('user_id', $newUser->id)->exists());

        // Assert registration token was created
        $this->assertDatabaseHas('user_registration_tokens', [
            'user_id' => $newUser->id,
            'email' => 'newuser@example.com',
        ]);

        // Assert welcome email was sent
        Mail::assertSent(NewUserAccountCreated::class, function ($mail) use ($newUser) {
            return $mail->hasTo($newUser->email)
                && $mail->user->id === $newUser->id
                && $mail->organization->id === $this->organization->id
                && $mail->invitedBy->id === $this->organizer->id;
        });
    }

    /** @test */
    public function it_generates_name_from_email_when_creating_account()
    {
        Mail::fake();

        $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => 'john.doe@example.com',
                'role' => 'member',
                'create_account' => true,
            ]);

        $user = User::where('email', 'john.doe@example.com')->first();

        $this->assertEquals('John doe', $user->name);
    }

    /** @test */
    public function it_prevents_inviting_already_existing_member()
    {
        Mail::fake();

        $existingMember = User::factory()->create();
        $this->organization->users()->attach($existingMember->id, [
            'role' => 'member',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => $existingMember->email,
                'role' => 'admin',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Dieser Benutzer ist bereits Mitglied der Organisation.');

        Mail::assertNothingSent();
    }

    /** @test */
    public function it_requires_valid_email_for_invitation()
    {
        $response = $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => 'invalid-email',
                'role' => 'member',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_requires_valid_role_for_invitation()
    {
        $response = $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => 'test@example.com',
                'role' => 'invalid_role',
            ]);

        $response->assertSessionHasErrors('role');
    }

    /** @test */
    public function non_owner_cannot_invite_members()
    {
        $regularMember = User::factory()->create([
            'is_organizer' => true,
        ]);

        $this->organization->users()->attach($regularMember->id, [
            'role' => 'member',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($regularMember)
            ->post(route('organizer.team.invite'), [
                'email' => 'test@example.com',
                'role' => 'member',
            ]);

        $response->assertForbidden();
    }

    /** @test */
    public function it_creates_cancellation_token_with_7_day_expiry()
    {
        Mail::fake();

        $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => 'newuser@example.com',
                'role' => 'member',
                'create_account' => true,
            ]);

        $user = User::where('email', 'newuser@example.com')->first();
        $token = UserRegistrationToken::where('user_id', $user->id)->first();

        $this->assertNotNull($token);
        $this->assertEquals($user->email, $token->email);
        $this->assertEqualsWithDelta(
            now()->addDays(7)->timestamp,
            $token->expires_at->timestamp,
            60 // 1 minute tolerance
        );
    }

    /** @test */
    public function created_user_has_unverified_email()
    {
        Mail::fake();

        $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => 'newuser@example.com',
                'role' => 'member',
                'create_account' => true,
            ]);

        $user = User::where('email', 'newuser@example.com')->first();

        $this->assertNull($user->email_verified_at);
    }

    /** @test */
    public function welcome_email_contains_temporary_password()
    {
        Mail::fake();

        $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => 'newuser@example.com',
                'role' => 'member',
                'create_account' => true,
            ]);

        Mail::assertSent(NewUserAccountCreated::class, function ($mail) {
            return !empty($mail->temporaryPassword)
                && strlen($mail->temporaryPassword) === 16;
        });
    }

    /** @test */
    public function welcome_email_contains_cancellation_token()
    {
        Mail::fake();

        $this->actingAs($this->organizer)
            ->post(route('organizer.team.invite'), [
                'email' => 'newuser@example.com',
                'role' => 'member',
                'create_account' => true,
            ]);

        Mail::assertSent(NewUserAccountCreated::class, function ($mail) {
            return !empty($mail->cancellationToken)
                && strlen($mail->cancellationToken) === 64;
        });
    }
}
