<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Zeige Profil-Einstellungen
     */
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Aktualisiere Profil-Daten
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'organization_name' => 'nullable|string|max:255',
            'organization_website' => 'nullable|url|max:255',
            'organization_description' => 'nullable|string',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil erfolgreich aktualisiert!');
    }

    /**
     * Zeige Benachrichtigungseinstellungen
     */
    public function notifications()
    {
        $user = auth()->user();
        $notificationPreferences = $user->notification_preferences ?? [
            'booking_notifications' => true,
            'event_updates' => true,
            'reminder_notifications' => true,
        ];

        return view('profile.notifications', compact('user', 'notificationPreferences'));
    }

    /**
     * Aktualisiere Benachrichtigungseinstellungen
     */
    public function updateNotifications(Request $request)
    {
        $user = auth()->user();

        $preferences = [
            'booking_notifications' => $request->has('booking_notifications'),
            'event_updates' => $request->has('event_updates'),
            'reminder_notifications' => $request->has('reminder_notifications'),
            'newsletter' => $request->has('newsletter'),
            'marketing' => $request->has('marketing'),
        ];

        $user->update([
            'notification_preferences' => $preferences
        ]);

        return back()->with('success', 'Benachrichtigungseinstellungen erfolgreich aktualisiert!');
    }

    /**
     * Passwort ändern
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Das aktuelle Passwort ist falsch.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Passwort erfolgreich geändert!');
    }
}
