<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewOrganizerRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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

        // Filter by SSO provider (with error handling if column doesn't exist)
        if ($request->filled('sso_provider')) {
            try {
                if ($request->sso_provider === 'none') {
                    $query->whereNull('sso_provider');
                } else {
                    $query->where('sso_provider', $request->sso_provider);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to filter by SSO provider: ' . $e->getMessage());
            }
        }

        $users = $query->withCount(['events', 'bookings'])
                       ->latest()
                       ->paginate(20);

        $roles = Role::all();

        // Get unique SSO providers for filter
        try {
            $ssoProviders = User::whereNotNull('sso_provider')
                                ->distinct()
                                ->pluck('sso_provider');
        } catch (\Exception $e) {
            // If column doesn't exist or query fails, use empty collection
            $ssoProviders = collect();
            Log::warning('Failed to fetch SSO providers: ' . $e->getMessage());
        }

        return view('admin.users.index', compact('users', 'roles', 'ssoProviders'));
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
            'organization_name' => 'nullable|string|max:255',
            'organization_description' => 'nullable|string|max:1000',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'organization_name' => $validated['organization_name'] ?? null,
            'organization_description' => $validated['organization_description'] ?? null,
        ]);

        // Check if user is becoming an organizer
        $wasOrganizer = $user->hasRole('organizer');

        // Sync roles
        if (isset($validated['roles'])) {
            // Preserve admin role if user is editing themselves
            if (auth()->id() === $user->id && $user->hasRole('admin')) {
                $roles = $validated['roles'];
                if (!in_array('admin', $roles)) {
                    $roles[] = 'admin';
                }
                $user->syncRoles($roles);
            } else {
                $user->syncRoles($validated['roles']);
            }
        } else {
            // If no roles selected, preserve admin role for self-edit
            if (auth()->id() === $user->id && $user->hasRole('admin')) {
                $user->syncRoles(['admin']);
            } elseif ($user->roles()->count() === 0) {
                $user->assignRole('user');
            }
        }

        // Notify admins if user just became an organizer
        $isNowOrganizer = $user->hasRole('organizer');
        if (!$wasOrganizer && $isNowOrganizer) {
            $admins = User::role('admin')->where('id', '!=', auth()->id())->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewOrganizerRegisteredNotification($user));
            }
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

        $wasOrganizer = $user->hasRole('organizer');
        $user->assignRole($validated['role']);

        // Notify admins if user just became an organizer
        if (!$wasOrganizer && $validated['role'] === 'organizer') {
            $admins = User::role('admin')->where('id', '!=', auth()->id())->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewOrganizerRegisteredNotification($user));
            }
        }

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

    /**
     * Promote user to organizer (especially useful for SSO users)
     */
    public function promoteToOrganizer(User $user)
    {
        if ($user->hasRole('organizer')) {
            return back()->with('error', 'Benutzer ist bereits Organisator.');
        }

        $user->assignRole('organizer');

        // Notify all other admins about the promotion
        $admins = User::role('admin')->where('id', '!=', auth()->id())->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewOrganizerRegisteredNotification($user));
        }

        return back()->with('success', 'Benutzer erfolgreich zum Organisator befördert.');
    }

    /**
     * Demote organizer to participant
     */
    public function demoteToParticipant(User $user)
    {
        // Prevent demotion of admins
        if ($user->hasRole('admin')) {
            return back()->with('error', 'Administratoren können nicht degradiert werden.');
        }

        if (!$user->hasRole('organizer')) {
            return back()->with('error', 'Benutzer ist kein Organisator.');
        }

        $user->removeRole('organizer');

        // Ensure user has at least the participant role
        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }

        return back()->with('success', 'Benutzer wurde zum Teilnehmer degradiert.');
    }
}

