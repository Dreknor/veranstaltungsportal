<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    /**
     * Display organization selection screen
     */
    public function select()
    {
        $organizations = auth()->user()->activeOrganizations;

        if ($organizations->count() === 1) {
            $organization = $organizations->first();
            auth()->user()->setCurrentOrganization($organization);
            return redirect()->route('organizer.dashboard');
        }

        return view('organizer.organizations.select', compact('organizations'));
    }

    /**
     * Switch to a different organization
     */
    public function switch(Organization $organization)
    {
        $user = auth()->user();

        if (!$user->isMemberOf($organization)) {
            abort(403, 'Sie sind kein Mitglied dieser Organisation.');
        }

        $user->setCurrentOrganization($organization);

        return redirect()->route('organizer.dashboard')
            ->with('success', 'Organisation gewechselt zu: ' . $organization->name);
    }

    /**
     * Show the form for creating a new organization
     */
    public function create()
    {
        return view('organizer.organizations.create');
    }

    /**
     * Store a newly created organization
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Organization::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $organization = Organization::create($validated);

        // Attach current user as owner
        $organization->users()->attach(auth()->id(), [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        // Set as current organization
        auth()->user()->setCurrentOrganization($organization);

        return redirect()->route('organizer.dashboard')
            ->with('success', 'Organisation erfolgreich erstellt!');
    }

    /**
     * Display organization settings
     */
    public function edit()
    {
        $organization = auth()->user()->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $this->authorize('update', $organization);

        return view('organizer.organizations.edit', compact('organization'));
    }

    /**
     * Update organization settings
     */
    public function update(Request $request)
    {
        $organization = auth()->user()->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($organization->logo) {
                \Storage::disk('public')->delete($organization->logo);
            }

            $validated['logo'] = $request->file('logo')->store('organizations/logos', 'public');
        }

        $organization->update($validated);

        return back()->with('success', 'Organisation erfolgreich aktualisiert!');
    }

    /**
     * Delete organization logo
     */
    public function deleteLogo()
    {
        $organization = auth()->user()->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $this->authorize('update', $organization);

        if ($organization->logo) {
            Storage::disk('public')->delete($organization->logo);
            $organization->update(['logo' => null]);
        }

        return back()->with('success', 'Logo erfolgreich gelöscht!');
    }

    /**
     * Show team members
     */
    public function team()
    {
        $organization = auth()->user()->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $this->authorize('update', $organization);

        $members = $organization->users()->withPivot(['role', 'is_active', 'joined_at'])->get();

        return view('organizer.organizations.team', compact('organization', 'members'));
    }

    /**
     * Invite team member
     */
    public function inviteMember(Request $request)
    {
        $organization = auth()->user()->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $this->authorize('update', $organization);

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', Rule::in(['admin', 'member'])],
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->first();

        if ($organization->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Dieser Benutzer ist bereits Mitglied der Organisation.');
        }

        $organization->users()->attach($user->id, [
            'role' => $validated['role'],
            'is_active' => true,
            'invited_at' => now(),
            'joined_at' => now(),
        ]);

        return back()->with('success', 'Teammitglied erfolgreich hinzugefügt!');
    }

    /**
     * Update team member role
     */
    public function updateMemberRole(Request $request, $userId)
    {
        $organization = auth()->user()->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $this->authorize('update', $organization);

        $validated = $request->validate([
            'role' => ['required', Rule::in(['owner', 'admin', 'member'])],
        ]);

        $user = \App\Models\User::findOrFail($userId);
        $oldRole = $organization->getUserRole($user);

        $organization->users()->updateExistingPivot($userId, [
            'role' => $validated['role'],
        ]);

        // Send role change notification
        if ($oldRole !== $validated['role']) {
            \Mail::to($user->email)->send(new \App\Mail\OrganizationRoleChanged(
                $organization,
                auth()->user(),
                $oldRole,
                $validated['role']
            ));
        }

        return back()->with('success', 'Rolle erfolgreich aktualisiert!');
    }

    /**
     * Remove team member
     */
    public function removeMember($userId)
    {
        $organization = auth()->user()->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $this->authorize('update', $organization);

        // Prevent removing the last owner
        if ($organization->owners()->count() === 1) {
            $lastOwner = $organization->owners()->first();
            if ($lastOwner->id == $userId) {
                return back()->with('error', 'Die Organisation muss mindestens einen Besitzer haben.');
            }
        }

        $organization->users()->detach($userId);

        return back()->with('success', 'Teammitglied erfolgreich entfernt!');
    }

    /**
     * Show form for batch import
     */
    public function importForm()
    {
        $organization = auth()->user()->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $this->authorize('update', $organization);

        return view('organizer.organizations.team-import', compact('organization'));
    }

    /**
     * Process batch import from CSV
     */
    public function importMembers(Request $request)
    {
        $organization = auth()->user()->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $this->authorize('update', $organization);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
            'default_role' => ['required', Rule::in(['admin', 'member'])],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $imported = 0;
        $skipped = 0;
        $errors = [];

        // Skip header row
        fgetcsv($handle);

        while (($data = fgetcsv($handle)) !== false) {
            $email = trim($data[0] ?? '');
            $role = trim($data[1] ?? '') ?: $request->default_role;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ungültige E-Mail: {$email}";
                $skipped++;
                continue;
            }

            $user = \App\Models\User::where('email', $email)->first();

            if (!$user) {
                $errors[] = "Benutzer nicht gefunden: {$email}";
                $skipped++;
                continue;
            }

            if ($organization->users()->where('user_id', $user->id)->exists()) {
                $skipped++;
                continue;
            }

            $organization->users()->attach($user->id, [
                'role' => $role,
                'is_active' => true,
                'invited_at' => now(),
                'joined_at' => now(),
            ]);

            // Send invitation email
            \Mail::to($user->email)->send(new \App\Mail\OrganizationInvitation(
                $organization,
                auth()->user(),
                $role
            ));

            $imported++;
        }

        fclose($handle);

        $message = "{$imported} Mitglieder erfolgreich importiert.";
        if ($skipped > 0) {
            $message .= " {$skipped} übersprungen.";
        }

        return back()->with('success', $message)->with('import_errors', $errors);
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $csv = "email,role\n";
        $csv .= "beispiel@email.com,member\n";
        $csv .= "admin@email.com,admin\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="team-import-vorlage.csv"',
        ]);
    }
}
