<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query()->with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->withCount(['events', 'bookings'])
                       ->latest()
                       ->paginate(20);

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'user_type' => 'required|in:participant,organizer',
            'organization_name' => 'nullable|string|max:255',
            'organization_description' => 'nullable|string|max:1000',
            'is_organizer' => 'boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_type' => $validated['user_type'],
            'organization_name' => $validated['organization_name'] ?? null,
            'organization_description' => $validated['organization_description'] ?? null,
            'is_organizer' => $validated['is_organizer'] ?? false,
        ]);

        // Sync roles based on user_type
        if ($validated['user_type'] === 'organizer') {
            if (!$user->hasRole('organizer')) {
                $user->assignRole('organizer');
            }
        } else {
            if ($user->hasRole('organizer') && !$user->hasRole('admin')) {
                $user->removeRole('organizer');
                if (!$user->hasRole('user')) {
                    $user->assignRole('user');
                }
            }
        }

        // Sync additional roles
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Benutzer erfolgreich aktualisiert.');
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Sie können sich nicht selbst löschen.');
        }

        // Check if user has events
        if ($user->events()->count() > 0) {
            return back()->with('error', 'Benutzer kann nicht gelöscht werden, da er noch Events hat.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Benutzer erfolgreich gelöscht.');
    }

    public function toggleOrganizer(User $user)
    {
        $isOrganizer = !$user->is_organizer;

        $user->update([
            'is_organizer' => $isOrganizer,
            'user_type' => $isOrganizer ? 'organizer' : 'participant',
        ]);

        // Sync organizer role
        if ($isOrganizer) {
            $user->assignRole('organizer');
        } else {
            $user->removeRole('organizer');
            if (!$user->hasRole('user')) {
                $user->assignRole('user');
            }
        }

        return back()->with('success', 'Account-Typ erfolgreich geändert.');
    }

    public function toggleAdmin(User $user)
    {
        // Prevent self-demotion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Sie können Ihren eigenen Admin-Status nicht ändern.');
        }

        if ($user->hasRole('admin')) {
            $user->removeRole('admin');
            $message = 'Admin-Rechte entfernt.';
        } else {
            $user->assignRole('admin');
            $message = 'Admin-Rechte erteilt.';
        }

        return back()->with('success', $message);
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->assignRole($validated['role']);

        return back()->with('success', 'Rolle erfolgreich zugewiesen.');
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        // Prevent removing own admin role
        if ($user->id === auth()->id() && $validated['role'] === 'admin') {
            return back()->with('error', 'Sie können Ihre eigene Admin-Rolle nicht entfernen.');
        }

        $user->removeRole($validated['role']);

        return back()->with('success', 'Rolle erfolgreich entfernt.');
    }
}

