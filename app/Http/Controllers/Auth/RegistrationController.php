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
        return view('auth.register', [
            'allowOrganizerRegistration' => config('app.allow_organizer_registration', false),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => 'required|recaptchav3:register,0.7',
            'account_type' => [
                'required',
                'in:participant,organizer',
                function ($attribute, $value, $fail) {
                    if ($value === 'organizer' && !config('app.allow_organizer_registration', true)) {
                        $fail('Die Registrierung als Veranstalter ist derzeit nicht möglich.');
                    }
                },
            ],
            'organization_name' => [
                'required_if:account_type,organizer',
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('account_type') === 'organizer' && $value) {
                        // Verhindere, dass eine E-Mail als Organisationsname verwendet wird
                        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $fail('Bitte geben Sie einen Organisationsnamen an, nicht eine E-Mail-Adresse.');
                            return;
                        }
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

        // Robuste Ermittlung des Organisationsnamens direkt aus der Anfrage (getrimmt)
        $organizationName = null;
        if ($accountType === 'organizer') {
            $rawOrgName = (string) $request->input('organization_name', '');
            $organizationName = trim(preg_replace('/\s+/', ' ', $rawOrgName));
            if ($organizationName === '') {
                $organizationName = null;
            }
        }
        $organizationDescription = $request->input('organization_description');

        // Remove organization and account_type fields from user data
        unset($validated['account_type'], $validated['organization_name'], $validated['organization_description']);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Assign appropriate role based on account_type
        if ($accountType === 'organizer') {
            $role = Role::query()->firstOrCreate(['name' => 'organizer']);
            $user->assignRole($role);

            // Create organization for the organizer (nur wenn Name vorhanden)
            if ($organizationName) {
                $organization = \App\Models\Organization::create([
                    'name' => $organizationName,
                    'description' => $organizationDescription,
                    'email' => $user->email,
                    'is_active' => true,
                ]);

                // Attach user as owner of the organization
                $user->organizations()->attach($organization->id, [
                    'role' => 'owner',
                    'is_active' => true,
                    'joined_at' => now(),
                ]);

                // Setze aktuelle Organisation im Session-Kontext
                try {
                    $user->setCurrentOrganization($organization);
                } catch (\Throwable $e) {
                    // Ignoriere Fehler hier, Middleware fängt fehlenden Kontext ab
                }
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
