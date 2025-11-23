<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_access_admin_panel()
    {
        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    #[Test]
    public function non_admin_cannot_access_admin_panel()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_manage_all_events()
    {
        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $event = Event::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.events.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_manage_users()
    {
        $admin = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function organizer_can_only_manage_own_events()
    {
        $organizer1 = User::factory()->create();
        $organizer1->assignRole('organizer');
        $result1 = $this->createOrganizerWithOrganization($organizer1);

        $organizer2 = User::factory()->create();
        $organizer2->assignRole('organizer');
        $result2 = $this->createOrganizerWithOrganization($organizer2);

        $event1 = Event::factory()->create(['organization_id' => $result1['organization']->id]);
        $event2 = Event::factory()->create(['organization_id' => $result2['organization']->id]);

        // Set current organization for organizer1
        session(['current_organization_id' => $result1['organization']->id]);

        $response = $this->actingAs($organizer1)->get(route('organizer.events.edit', $event1));
        $response->assertStatus(200);

        $response = $this->actingAs($organizer1)->get(route('organizer.events.edit', $event2));
        $response->assertStatus(403);
    }

    #[Test]
    public function participant_cannot_access_organizer_features()
    {
        $participant = User::factory()->create();
        $participant->assignRole('user');

        $response = $this->actingAs($participant)->get(route('organizer.events.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function guest_cannot_access_protected_routes()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}




