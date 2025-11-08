<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManagementController extends Controller
{
    /**
     * Display roles overview
     */
    public function index()
    {
        $roles = Role::withCount('users')->with('permissions')->get();
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Show role edit form
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update role permissions
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Rollen-Berechtigungen erfolgreich aktualisiert.');
    }

    /**
     * Display permissions overview
     */
    public function permissions()
    {
        $permissions = Permission::with('roles')->get()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });

        return view('admin.roles.permissions', compact('permissions'));
    }
}
