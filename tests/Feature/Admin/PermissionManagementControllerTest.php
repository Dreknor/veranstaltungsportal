<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionManagementControllerTest extends TestCase
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

    public function test_guest_cannot_access_permissions_index(): void
    {
        $this->get(route('admin.permissions.index'))->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_permissions_index(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.permissions.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_access_permissions_index(): void
    {
        Permission::factory()->count(3)->create(['group' => 'events']);

        $this->actingAs($this->admin)
            ->get(route('admin.permissions.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.permissions.index')
            ->assertViewHas('permissions')
            ->assertViewHas('groups');
    }

    public function test_admin_can_access_permissions_create(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.permissions.create'))
            ->assertStatus(200)
            ->assertViewIs('admin.permissions.create');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Permission anlegen
    // ─────────────────────────────────────────────────────────────────────────

    public function test_admin_can_create_permission(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.permissions.store'), [
                'name'        => 'export invoices',
                'group'       => 'invoices',
                'description' => 'Rechnungen exportieren',
            ]);

        $response->assertRedirect(route('admin.permissions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('permissions', [
            'name'  => 'export invoices',
            'group' => 'invoices',
        ]);
    }

    public function test_create_permission_fails_with_invalid_name(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.permissions.store'), [
                'name'  => 'INVALID!!! Name', // Großbuchstaben + Sonderzeichen → ungültig
                'group' => 'general',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_create_permission_fails_with_duplicate_name(): void
    {
        Permission::factory()->create(['name' => 'view reports', 'group' => 'reports']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.permissions.store'), [
                'name'  => 'view reports',
                'group' => 'reports',
            ]);

        $response->assertSessionHasErrors('name');
        $this->assertSame(1, Permission::where('name', 'view reports')->count());
    }

    public function test_create_permission_fails_without_group(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.permissions.store'), [
                'name' => 'some permission',
                // 'group' fehlt
            ]);

        $response->assertSessionHasErrors('group');
    }

    public function test_new_permission_is_automatically_assigned_to_admin_role(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        $this->actingAs($this->admin)
            ->post(route('admin.permissions.store'), [
                'name'  => 'manage payouts',
                'group' => 'payouts',
            ]);

        $adminRole->refresh()->load('permissions');
        $this->assertTrue($adminRole->hasPermissionTo('manage payouts'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Permission bearbeiten
    // ─────────────────────────────────────────────────────────────────────────

    public function test_admin_can_access_permission_edit(): void
    {
        $permission = Permission::factory()->create(['name' => 'view events', 'group' => 'events']);

        $this->actingAs($this->admin)
            ->get(route('admin.permissions.edit', $permission))
            ->assertStatus(200)
            ->assertViewIs('admin.permissions.edit')
            ->assertViewHas('permission')
            ->assertViewHas('groups');
    }

    public function test_admin_can_update_permission_group_and_description(): void
    {
        $permission = Permission::factory()->create([
            'name'        => 'edit events',
            'group'       => 'events',
            'is_system'   => false,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.permissions.update', $permission), [
                'name'        => 'edit events',
                'group'       => 'content',
                'description' => 'Events bearbeiten',
            ]);

        $permission->refresh();
        $this->assertEquals('content', $permission->group);
        $this->assertEquals('Events bearbeiten', $permission->description);
    }

    public function test_system_permission_name_cannot_be_changed(): void
    {
        $permission = Permission::factory()->create([
            'name'      => 'access admin panel',
            'group'     => 'admin',
            'is_system' => true,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.permissions.update', $permission), [
                'name'        => 'renamed-permission',
                'group'       => 'admin',
                'description' => 'Aktualisiert',
            ]);

        $permission->refresh();
        $this->assertEquals('access admin panel', $permission->name); // Name unverändert
        $this->assertEquals('Aktualisiert', $permission->description); // Beschreibung geändert
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Permission löschen
    // ─────────────────────────────────────────────────────────────────────────

    public function test_admin_can_delete_non_system_permission(): void
    {
        $permission = Permission::factory()->create([
            'name'      => 'deletable-perm',
            'group'     => 'test',
            'is_system' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.permissions.destroy', $permission));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('permissions', ['name' => 'deletable-perm']);
    }

    public function test_admin_cannot_delete_system_permission(): void
    {
        $permission = Permission::factory()->create([
            'name'      => 'system-perm',
            'group'     => 'system',
            'is_system' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.permissions.destroy', $permission));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('permissions', ['name' => 'system-perm']);
    }

    public function test_deleting_permission_removes_it_from_roles(): void
    {
        $permission = Permission::factory()->create([
            'name'      => 'temp-perm',
            'group'     => 'test',
            'is_system' => false,
        ]);

        $role = Role::where('name', 'user')->first();
        $role->givePermissionTo($permission);
        $this->assertTrue($role->hasPermissionTo('temp-perm'));

        $this->actingAs($this->admin)
            ->delete(route('admin.permissions.destroy', $permission));

        $role->refresh()->load('permissions');
        $this->assertFalse($role->hasPermissionTo('temp-perm'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Audit-Log
    // ─────────────────────────────────────────────────────────────────────────

    public function test_creating_permission_writes_audit_log(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.permissions.store'), [
                'name'  => 'log-this-perm',
                'group' => 'test',
            ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action'  => 'permission_created',
        ]);
    }

    public function test_deleting_permission_writes_audit_log(): void
    {
        $permission = Permission::factory()->create([
            'name'      => 'log-delete-perm',
            'group'     => 'test',
            'is_system' => false,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.permissions.destroy', $permission));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action'  => 'permission_deleted',
        ]);
    }
}

