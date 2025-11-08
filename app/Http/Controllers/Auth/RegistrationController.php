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
            'user_type' => ['required', 'in:participant,organizer'],
            'organization_name' => ['required_if:user_type,organizer', 'nullable', 'string', 'max:255'],
            'organization_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Assign appropriate role based on user_type
        if ($user->user_type === 'organizer') {
            $user->assignRole('organizer');
        } else {
            $user->assignRole('user');
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
