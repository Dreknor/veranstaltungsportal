<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organizer_can_create_organization()
    {
        $user = User::factory()->create();
        $user->assignRole('organizer');

        $response = $this->actingAs($user)->post(route('organizer.organizations.store'), [
            'name' => 'Test Organization',
            'description' => 'Test Description',
            'email' => 'test@example.com',
            'website' => 'https://example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('organizations', [
            'name' => 'Test Organization',
            'email' => 'test@example.com',
        ]);

        $organization = Organization::where('name', 'Test Organization')->first();
        $this->assertTrue($user->isOwnerOf($organization));
    }

    #[Test]
    public function user_can_switch_between_organizations()
    {
        $user = User::factory()->create();
        $user->assignRole('organizer');

        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();

        $org1->users()->attach($user->id, ['role' => 'owner', 'is_active' => true, 'joined_at' => now()]);
        $org2->users()->attach($user->id, ['role' => 'admin', 'is_active' => true, 'joined_at' => now()]);

        $response = $this->actingAs($user)->post(route('organizer.organizations.switch', $org2));

        $response->assertRedirect();
        $this->assertEquals($org2->id, session('current_organization_id'));
    }

    #[Test]
    public function owner_can_invite_team_member()
    {
        $owner = User::factory()->create();
        $owner->assignRole('organizer');
        $member = User::factory()->create();

        $organization = Organization::factory()->create();
        $organization->users()->attach($owner->id, ['role' => 'owner', 'is_active' => true, 'joined_at' => now()]);

        $response = $this->actingAs($owner)
            ->withSession(['current_organization_id' => $organization->id])
            ->post(route('organizer.team.invite'), [
                'email' => $member->email,
                'role' => 'member',
            ]);

        $response->assertRedirect();
        $this->assertTrue($organization->hasMember($member));
    }

    #[Test]
    public function owner_can_change_member_role()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $owner->assignRole('organizer');

        $organization = Organization::factory()->create();
        $organization->users()->attach($owner->id, ['role' => 'owner', 'is_active' => true, 'joined_at' => now()]);
        $organization->users()->attach($member->id, ['role' => 'member', 'is_active' => true, 'joined_at' => now()]);

        $response = $this->actingAs($owner)
            ->withSession(['current_organization_id' => $organization->id])
            ->put(route('organizer.team.update-role', $member), [
                'role' => 'admin',
            ]);

        $response->assertRedirect();
        $this->assertEquals('admin', $organization->getUserRole($member));
    }

    #[Test]
    public function member_cannot_manage_organization()
    {
        $member = User::factory()->create();
        $member->assignRole('organizer');

        $organization = Organization::factory()->create();
        $organization->users()->attach($member->id, ['role' => 'member', 'is_active' => true, 'joined_at' => now()]);

        $response = $this->actingAs($member)
            ->withSession(['current_organization_id' => $organization->id])
            ->put(route('organizer.organization.update'), [
                'name' => 'New Name',
            ]);

        $response->assertForbidden();
    }

    #[Test]
    public function cannot_remove_last_owner()
    {
        $owner = User::factory()->create();
        $owner->assignRole('organizer');

        $organization = Organization::factory()->create();
        $organization->users()->attach($owner->id, ['role' => 'owner', 'is_active' => true, 'joined_at' => now()]);

        $response = $this->actingAs($owner)
            ->withSession(['current_organization_id' => $organization->id])
            ->delete(route('organizer.team.remove', $owner));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertTrue($organization->hasMember($owner));
    }

    #[Test]
    public function user_without_organization_is_redirected_to_create()
    {
        $user = User::factory()->create();
        $user->assignRole('organizer');

        $response = $this->actingAs($user)->get(route('organizer.dashboard'));

        $response->assertRedirect(route('organizer.organizations.create'));
    }
}



