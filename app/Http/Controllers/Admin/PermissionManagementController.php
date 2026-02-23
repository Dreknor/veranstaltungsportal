<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

class PermissionManagementController extends Controller
{
    /**
     * Alle Permissions gruppiert anzeigen
     */
    public function index()
    {
        $permissions = Permission::with('roles')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $groups = Permission::distinct()->orderBy('group')->pluck('group');

        return view('admin.permissions.index', compact('permissions', 'groups'));
    }

    /**
     * Formular: Neue Permission anlegen
     */
    public function create()
    {
        $groups = Permission::distinct()->orderBy('group')->pluck('group');
        return view('admin.permissions.create', compact('groups'));
    }

    /**
     * Neue Permission speichern
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'regex:/^[a-z0-9 \-]+$/', 'unique:permissions,name', 'max:100'],
            'group'       => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $permission = Permission::create([
            'name'        => $validated['name'],
            'guard_name'  => 'web',
            'group'       => strtolower($validated['group']),
            'description' => $validated['description'] ?? null,
            'is_system'   => false,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Admin-Rolle bekommt automatisch alle neuen Permissions
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'permission_created',
            'description' => "Permission '{$permission->name}' wurde erstellt (Gruppe: {$permission->group})",
            'new_values'  => ['permission' => $permission->name, 'group' => $permission->group],
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permission->name}' wurde erstellt.");
    }

    /**
     * Permission bearbeiten (Formular)
     */
    public function edit(Permission $permission)
    {
        $groups = Permission::distinct()->orderBy('group')->pluck('group');
        return view('admin.permissions.edit', compact('permission', 'groups'));
    }

    /**
     * Permission aktualisieren
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'regex:/^[a-z0-9 \-]+$/', "unique:permissions,name,{$permission->id}", 'max:100'],
            'group'       => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $oldName = $permission->name;

        // System-Permissions: nur Beschreibung und Gruppe änderbar
        if ($permission->is_system) {
            $permission->update([
                'group'       => strtolower($validated['group']),
                'description' => $validated['description'] ?? null,
            ]);
        } else {
            $permission->update([
                'name'        => $validated['name'],
                'group'       => strtolower($validated['group']),
                'description' => $validated['description'] ?? null,
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'permission_updated',
            'description' => "Permission '{$oldName}' wurde bearbeitet",
            'old_values'  => ['name' => $oldName],
            'new_values'  => ['name' => $permission->name, 'group' => $permission->group],
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission aktualisiert.');
    }

    /**
     * Permission löschen
     */
    public function destroy(Permission $permission)
    {
        if ($permission->is_system) {
            return back()->with('error', 'System-Permissions können nicht gelöscht werden.');
        }

        $roleCount = $permission->roles()->count();
        $permissionName = $permission->name;
        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'permission_deleted',
            'description' => "Permission '{$permissionName}' wurde gelöscht (war {$roleCount} Rolle(n) zugewiesen)",
            'old_values'  => ['permission' => $permissionName, 'was_in_roles' => $roleCount],
        ]);

        return back()->with('success', "Permission '{$permissionName}' gelöscht. War {$roleCount} Rolle(n) zugewiesen.");
    }
}

