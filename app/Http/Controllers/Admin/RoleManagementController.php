<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

class RoleManagementController extends Controller
{
    /**
     * Rollen-Übersicht
     */
    public function index()
    {
        $roles = Role::withCount('users')->with('permissions')->get();
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Formular: Neue Rolle anlegen
     */
    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Neue Rolle speichern
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'regex:/^[a-z0-9\-]+$/', 'unique:roles,name', 'max:50'],
            'description'   => 'nullable|string|max:255',
            'color'         => 'nullable|string|max:20',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name'        => $validated['name'],
            'guard_name'  => 'web',
            'description' => $validated['description'] ?? null,
            'color'       => $validated['color'] ?? '#6b7280',
            'is_system'   => false,
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'role_created',
            'description' => "Rolle '{$role->name}' wurde erstellt",
            'new_values'  => ['role' => $role->name, 'permissions' => $validated['permissions'] ?? []],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Rolle '{$role->name}' wurde erfolgreich erstellt.");
    }

    /**
     * Rolle bearbeiten (Formular)
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Rolle aktualisieren (Permissions + Metadaten)
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'regex:/^[a-z0-9\-]+$/', "unique:roles,name,{$role->id}", 'max:50'],
            'description'   => 'nullable|string|max:255',
            'color'         => 'nullable|string|max:20',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $oldPermissions = $role->permissions->pluck('name')->toArray();

        // System-Rollen: Name nicht änderbar
        if ($role->is_system) {
            $role->update([
                'description' => $validated['description'] ?? null,
                'color'       => $validated['color'] ?? $role->color,
            ]);
        } else {
            $role->update([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'color'       => $validated['color'] ?? $role->color,
            ]);
        }

        // Admin-Rolle bekommt immer ALLE Permissions
        if ($role->name === 'admin') {
            $role->syncPermissions(Permission::all());
        } else {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'role_updated',
            'description' => "Rolle '{$role->name}' wurde bearbeitet",
            'old_values'  => ['permissions' => $oldPermissions],
            'new_values'  => ['permissions' => $validated['permissions'] ?? []],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rollen-Berechtigungen erfolgreich aktualisiert.');
    }

    /**
     * Rolle löschen
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System-Rollen können nicht gelöscht werden.');
        }

        $userCount = $role->users()->count();
        $fallbackRole = Role::where('name', 'user')->first();

        foreach ($role->users as $user) {
            $user->removeRole($role);
            if ($user->roles->isEmpty() && $fallbackRole) {
                $user->assignRole($fallbackRole);
            }
        }

        $roleName = $role->name;
        $role->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'role_deleted',
            'description' => "Rolle '{$roleName}' wurde gelöscht ({$userCount} Benutzer auf 'user' zurückgesetzt)",
            'old_values'  => ['role' => $roleName, 'affected_users' => $userCount],
        ]);

        $msg = "Rolle '{$roleName}' wurde gelöscht.";
        if ($userCount > 0) {
            $msg .= " {$userCount} Benutzer wurden auf 'user' zurückgesetzt.";
        }

        return redirect()->route('admin.roles.index')->with('success', $msg);
    }

    /**
     * Matrix: Alle Rollen × Permissions
     */
    public function matrix()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('admin.roles.matrix', compact('roles', 'permissions'));
    }
}
