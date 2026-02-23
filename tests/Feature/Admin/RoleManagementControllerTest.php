<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->user = User::factory()->create();
        $this->user->assignRole('user');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Zugriffsschutz
    // ─────────────────────────────────────────────────────────────────────────

    public function test_guest_cannot_access_roles_index(): void
    {
        $this->get(route('admin.roles.index'))->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_roles_index(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.roles.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_access_roles_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.roles.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.roles.index')
            ->assertViewHas('roles')
            ->assertViewHas('permissions');
    }

    public function test_guest_cannot_access_roles_create(): void
    {
        $this->get(route('admin.roles.create'))->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_roles_create(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.roles.create'))
            ->assertStatus(403);
    }

    public function test_admin_can_access_roles_create(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.roles.create'))
            ->assertStatus(200)
            ->assertViewIs('admin.roles.create');
    }

    public function test_admin_can_access_roles_matrix(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.roles.matrix'))
            ->assertStatus(200)
            ->assertViewIs('admin.roles.matrix')
            ->assertViewHas('roles')
            ->assertViewHas('permissions');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Rolle anlegen
    // ─────────────────────────────────────────────────────────────────────────

    public function test_admin_can_create_role(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.roles.store'), [
                'name'        => 'content-editor',
                'description' => 'Darf Inhalte bearbeiten',
                'color'       => '#3b82f6',
                'permissions' => [],
            ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('roles', ['name' => 'content-editor', 'is_system' => false]);
    }

    public function test_create_role_fails_with_invalid_name(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.roles.store'), [
                'name'  => 'Invalid Name!', // Leerzeichen + Sonderzeichen → ungültig
                'color' => '#3b82f6',
            ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('roles', ['name' => 'Invalid Name!']);
    }

    public function test_create_role_fails_with_duplicate_name(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.roles.store'), [
                'name'  => 'test-role',
                'color' => '#6b7280',
            ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.roles.store'), [
                'name'  => 'test-role',
                'color' => '#6b7280',
            ]);

        $response->assertSessionHasErrors('name');
        $this->assertSame(1, Role::where('name', 'test-role')->count());
    }

    public function test_create_role_assigns_permissions(): void
    {
        $permission = Permission::factory()->create([
            'name'  => 'view events',
            'group' => 'events',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.roles.store'), [
                'name'        => 'viewer-plus',
                'color'       => '#6b7280',
                'permissions' => ['view events'],
            ]);

        $role = Role::where('name', 'viewer-plus')->first();
        $this->assertNotNull($role);
        $this->assertTrue($role->hasPermissionTo('view events'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Rolle bearbeiten
    // ─────────────────────────────────────────────────────────────────────────

    public function test_admin_can_access_role_edit(): void
    {
        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web', 'is_system' => false]);

        $this->actingAs($this->admin)
            ->get(route('admin.roles.edit', $role))
            ->assertStatus(200)
            ->assertViewIs('admin.roles.edit')
            ->assertViewHas('role')
            ->assertViewHas('permissions')
            ->assertViewHas('rolePermissions');
    }

    public function test_admin_can_update_role_permissions(): void
    {
        $role = Role::create(['name' => 'custom-role', 'guard_name' => 'web', 'is_system' => false]);
        $permission = Permission::factory()->create(['name' => 'view events', 'group' => 'events']);

        $this->actingAs($this->admin)
            ->put(route('admin.roles.update', $role), [
                'name'        => 'custom-role',
                'description' => 'Aktualisierte Beschreibung',
                'color'       => '#ef4444',
                'permissions' => ['view events'],
            ]);

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('view events'));
        $this->assertEquals('Aktualisierte Beschreibung', $role->description);
        $this->assertEquals('#ef4444', $role->color);
    }

    public function test_system_role_name_cannot_be_changed(): void
    {
        $systemRole = Role::where('name', 'user')->first();
        $this->assertNotNull($systemRole, 'user-Rolle muss existieren');

        $this->actingAs($this->admin)
            ->put(route('admin.roles.update', $systemRole), [
                'name'        => 'renamed-user',
                'description' => 'Neue Beschreibung',
                'color'       => '#6b7280',
                'permissions' => [],
            ]);

        $systemRole->refresh();
        $this->assertEquals('user', $systemRole->name); // Name unverändert
        $this->assertEquals('Neue Beschreibung', $systemRole->description); // Beschreibung geändert
    }

    public function test_admin_role_always_gets_all_permissions(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        Permission::factory()->create(['name' => 'new-special-perm', 'group' => 'test']);

        $this->actingAs($this->admin)
            ->put(route('admin.roles.update', $adminRole), [
                'name'        => 'admin',
                'description' => 'Super Admin',
                'color'       => '#ef4444',
                'permissions' => [], // Keine Permissions angegeben
            ]);

        $adminRole->refresh();
        $allPermissions = Permission::pluck('name')->toArray();
        foreach ($allPermissions as $perm) {
            $this->assertTrue(
                $adminRole->hasPermissionTo($perm),
                "Admin-Rolle sollte Permission '{$perm}' haben"
            );
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Rolle löschen
    // ─────────────────────────────────────────────────────────────────────────

    public function test_admin_can_delete_non_system_role(): void
    {
        $role = Role::create(['name' => 'deletable-role', 'guard_name' => 'web', 'is_system' => false]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('roles', ['name' => 'deletable-role']);
    }

    public function test_admin_cannot_delete_system_role(): void
    {
        $systemRole = Role::where('name', 'user')->first();
        $this->assertNotNull($systemRole);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.roles.destroy', $systemRole));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'user']);
    }

    public function test_users_get_fallback_role_when_role_is_deleted(): void
    {
        $role = Role::create(['name' => 'temp-role', 'guard_name' => 'web', 'is_system' => false]);
        $affectedUser = User::factory()->create();
        $affectedUser->syncRoles([$role->name]); // Nur diese Rolle

        $this->actingAs($this->admin)
            ->delete(route('admin.roles.destroy', $role));

        $affectedUser->refresh();
        $this->assertTrue($affectedUser->hasRole('user'), 'Benutzer sollte Fallback-Rolle "user" erhalten haben');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Matrix-View
    // ─────────────────────────────────────────────────────────────────────────

    public function test_matrix_shows_correct_permission_assignments(): void
    {
        $permission = Permission::factory()->create(['name' => 'view events', 'group' => 'events']);
        $role = Role::create(['name' => 'view-only', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.roles.matrix'));

        $response->assertStatus(200);
        $response->assertSee('view events');
        $response->assertSee('view-only');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Audit-Log
    // ─────────────────────────────────────────────────────────────────────────

    public function test_creating_role_writes_audit_log(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.roles.store'), [
                'name'  => 'auditable-role',
                'color' => '#6b7280',
            ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action'  => 'role_created',
        ]);
    }

    public function test_deleting_role_writes_audit_log(): void
    {
        $role = Role::create(['name' => 'log-me', 'guard_name' => 'web', 'is_system' => false]);

        $this->actingAs($this->admin)
            ->delete(route('admin.roles.destroy', $role));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action'  => 'role_deleted',
        ]);
    }
}

