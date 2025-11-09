<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
            'organization_name' => ['required_if:account_type,organizer', 'nullable', 'string', 'max:255'],
            'organization_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $accountType = $validated['account_type'];
        unset($validated['account_type']);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Assign appropriate role based on account_type
        if ($accountType === 'organizer') {
            $user->assignRole('organizer');
        } else {
            $user->assignRole('user');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
