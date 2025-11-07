<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            if ($request->role === 'organizer') {
                $query->where('is_organizer', true);
            } elseif ($request->role === 'admin') {
                $query->where('is_admin', true);
            }
        }

        $users = $query->withCount(['events', 'bookings'])
                       ->latest()
                       ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_organizer' => 'boolean',
            'is_admin' => 'boolean',
        ]);

        $user->update($validated);

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
        $user->update([
            'is_organizer' => !$user->is_organizer
        ]);

        return back()->with('success', 'Organizer-Status erfolgreich geändert.');
    }

    public function toggleAdmin(User $user)
    {
        // Prevent self-demotion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Sie können Ihren eigenen Admin-Status nicht ändern.');
        }

        $user->update([
            'is_admin' => !$user->is_admin
        ]);

        return back()->with('success', 'Admin-Status erfolgreich geändert.');
    }
}

