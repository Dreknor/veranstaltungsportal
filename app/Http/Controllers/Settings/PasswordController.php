<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        // Redirect SSO users - they cannot change password
        if ($request->user()->sso_provider) {
            return redirect()->route('settings.profile.edit')
                ->with('error', 'SSO-Benutzer können ihr Passwort nicht über diese Anwendung ändern. Bitte verwenden Sie Ihren SSO-Provider (' . ucfirst($request->user()->sso_provider) . ').');
        }

        return view('settings.password', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        // Prevent SSO users from changing password
        if ($request->user()->sso_provider) {
            return back()->with('error', 'SSO-Benutzer können ihr Passwort nicht über diese Anwendung ändern.');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Rules\Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
