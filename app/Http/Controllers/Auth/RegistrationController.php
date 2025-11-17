<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewOrganizerRegisteredNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'account_type' => ['required', 'in:participant,organizer'],
            'organization_name' => [
                'required_if:account_type,organizer',
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('account_type') === 'organizer' && $value) {
                        $slug = \Illuminate\Support\Str::slug($value);
                        if (\App\Models\Organization::where('slug', $slug)->exists()) {
                            $fail('Eine Organisation mit diesem Namen existiert bereits. Bitte wählen Sie einen anderen Namen.');
                        }
                    }
                },
            ],
            'organization_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $accountType = $validated['account_type'];
        $organizationName = $validated['organization_name'] ?? null;
        $organizationDescription = $validated['organization_description'] ?? null;

        // Remove organization and account_type fields from user data
        unset($validated['account_type'], $validated['organization_name'], $validated['organization_description']);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Assign appropriate role based on account_type
        if ($accountType === 'organizer') {
            $role = Role::query()->firstOrCreate(['name' => 'organizer']);
            $user->assignRole($role);

            // Create organization for the organizer
            if ($organizationName) {
                $organization = \App\Models\Organization::create([
                    'name' => $organizationName,
                    'description' => $organizationDescription,
                ]);

                // Attach user as owner of the organization
                $user->organizations()->attach($organization->id, [
                    'role' => 'owner',
                    'is_active' => true,
                    'joined_at' => now(),
                ]);
            }

            // Notify all admins about the new organizer registration
            $admins = User::role('admin')->get();
            Notification::send($admins, new NewOrganizerRegisteredNotification($user));
        } else {
            $user->assignRole('user');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
