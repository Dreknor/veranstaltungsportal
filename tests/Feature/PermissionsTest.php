<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_access_admin_panel()
    {
        $admin = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function non_admin_cannot_access_admin_panel()
    {
        $user = User::factory()->create(['user_type' => 'participant']);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_manage_all_events()
    {
        $admin = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $event = Event::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.events.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_manage_users()
    {
        $admin = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function organizer_can_only_manage_own_events()
    {
        $organizer1 = User::factory()->create(['user_type' => 'organizer']);
        $organizer2 = User::factory()->create(['user_type' => 'organizer']);

        $event1 = Event::factory()->create(['user_id' => $organizer1->id]);
        $event2 = Event::factory()->create(['user_id' => $organizer2->id]);

        $response = $this->actingAs($organizer1)->get(route('organizer.events.edit', $event1));
        $response->assertStatus(200);

        $response = $this->actingAs($organizer1)->get(route('organizer.events.edit', $event2));
        $response->assertStatus(403);
    }

    /** @test */
    public function participant_cannot_access_organizer_features()
    {
        $participant = User::factory()->create(['user_type' => 'participant']);

        $response = $this->actingAs($participant)->get(route('organizer.events.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_protected_routes()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}


