<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleService
{
    /**
     * Prüft ob eine Rolle gelöscht werden darf
     */
    public function canDelete(Role $role): bool
    {
        return !$role->is_system;
    }

    /**
     * Prüft ob der Name einer Rolle geändert werden darf
     */
    public function canRename(Role $role): bool
    {
        return !$role->is_system;
    }

    /**
     * Löscht eine Rolle und setzt betroffene Benutzer zurück
     * Gibt Anzahl betroffener Benutzer zurück
     */
    public function deleteRole(Role $role): int
    {
        $fallback = Role::where('name', 'user')->first();
        $affected = 0;

        foreach ($role->users as $user) {
            $user->removeRole($role);
            if ($user->roles->isEmpty() && $fallback) {
                $user->assignRole($fallback);
                $affected++;
            }
        }

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $affected;
    }

    /**
     * Stellt sicher, dass Admin-Rolle immer alle Permissions hat
     */
    public function syncAdminPermissions(): void
    {
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->syncPermissions(Permission::all());
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
}

