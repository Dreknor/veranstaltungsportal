<?php

namespace Tests\Unit\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RoleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoleService();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // canDelete
    // ─────────────────────────────────────────────────────────────────────────

    public function test_can_delete_returns_false_for_system_role(): void
    {
        /** @var Role $systemRole */
        $systemRole = Role::create([
            'name'       => 'system-role',
            'guard_name' => 'web',
            'is_system'  => true,
        ]);

        $this->assertFalse($this->service->canDelete($systemRole));
    }

    public function test_can_delete_returns_true_for_custom_role(): void
    {
        /** @var Role $customRole */
        $customRole = Role::create([
            'name'       => 'custom-role',
            'guard_name' => 'web',
            'is_system'  => false,
        ]);

        $this->assertTrue($this->service->canDelete($customRole));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // canRename
    // ─────────────────────────────────────────────────────────────────────────

    public function test_can_rename_returns_false_for_system_role(): void
    {
        /** @var Role $systemRole */
        $systemRole = Role::create([
            'name'       => 'immutable-role',
            'guard_name' => 'web',
            'is_system'  => true,
        ]);

        $this->assertFalse($this->service->canRename($systemRole));
    }

    public function test_can_rename_returns_true_for_custom_role(): void
    {
        /** @var Role $customRole */
        $customRole = Role::create([
            'name'       => 'renameable-role',
            'guard_name' => 'web',
            'is_system'  => false,
        ]);

        $this->assertTrue($this->service->canRename($customRole));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // deleteRole
    // ─────────────────────────────────────────────────────────────────────────

    public function test_delete_role_removes_it_from_database(): void
    {
        /** @var Role $role */
        $role = Role::create(['name' => 'deletable', 'guard_name' => 'web', 'is_system' => false]);

        $this->service->deleteRole($role);

        $this->assertDatabaseMissing('roles', ['name' => 'deletable']);
    }

    public function test_delete_role_assigns_fallback_role_to_affected_users(): void
    {
        /** @var Role $role */
        $role = Role::create(['name' => 'bye-role', 'guard_name' => 'web', 'is_system' => false]);
        $user = User::factory()->create();
        $user->syncRoles(['bye-role']); // Nur diese eine Rolle

        $affected = $this->service->deleteRole($role);

        $user->refresh();
        $this->assertSame(1, $affected);
        $this->assertTrue($user->hasRole('user'), 'Benutzer sollte zur Fallback-Rolle "user" zugewiesen worden sein');
    }

    public function test_delete_role_returns_zero_when_no_users_affected(): void
    {
        /** @var Role $role */
        $role = Role::create(['name' => 'empty-role', 'guard_name' => 'web', 'is_system' => false]);

        $affected = $this->service->deleteRole($role);

        $this->assertSame(0, $affected);
    }

    public function test_delete_role_does_not_reassign_if_user_has_other_roles(): void
    {
        /** @var Role $role */
        $role = Role::create(['name' => 'extra-role', 'guard_name' => 'web', 'is_system' => false]);
        $user = User::factory()->create();
        $user->assignRole('user');
        $user->assignRole('extra-role'); // Hat schon 'user'

        $this->service->deleteRole($role);

        $user->refresh();
        // Nur 'user' - keine doppelte Zuweisung
        $this->assertSame(1, $user->roles->count());
        $this->assertTrue($user->hasRole('user'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // syncAdminPermissions
    // ─────────────────────────────────────────────────────────────────────────

    public function test_sync_admin_permissions_gives_all_permissions_to_admin(): void
    {
        Permission::factory()->create(['name' => 'perm-a', 'group' => 'test']);
        Permission::factory()->create(['name' => 'perm-b', 'group' => 'test']);

        /** @var Role $adminRole */
        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->syncPermissions([]); // Reset

        $this->service->syncAdminPermissions();

        $adminRole->refresh()->load('permissions');
        $this->assertTrue($adminRole->hasPermissionTo('perm-a'));
        $this->assertTrue($adminRole->hasPermissionTo('perm-b'));
    }

    public function test_sync_admin_permissions_does_nothing_if_no_admin_role(): void
    {
        // Wenn keine Admin-Rolle existiert → kein Fehler
        Role::where('name', 'admin')->delete();

        $this->expectNotToPerformAssertions();
        $this->service->syncAdminPermissions();
    }
}

