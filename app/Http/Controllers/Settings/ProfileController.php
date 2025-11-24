<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('settings.profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9\s\-\+\(\)]+$/'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'notification_preferences' => ['nullable', 'array'],
            'notification_preferences.*' => ['boolean'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo) {
                Storage::disk('local')->delete($user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'local');
            $validated['profile_photo'] = $path;
        }

        // Handle notification preferences separately
        $notificationPreferences = null;
        if (isset($validated['notification_preferences'])) {
            $currentPreferences = $user->notification_preferences ?? [];
            $newPreferences = array_map(function($value) {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }, $validated['notification_preferences']);

            // Merge new preferences with existing ones
            $notificationPreferences = array_merge($currentPreferences, $newPreferences);

            // Remove from validated data to handle separately
            unset($validated['notification_preferences']);
        }

        $user->fill($validated);

        // Set notification preferences after fill using setAttribute to force dirty tracking
        if ($notificationPreferences !== null) {
            $user->setAttribute('notification_preferences', $notificationPreferences);
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return to_route('settings.profile.edit')->with('status', __('Profile updated successfully'));
    }

    /**
     * Delete user's profile photo
     */
    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('local')->delete($user->profile_photo);
            $user->profile_photo = null;
            $user->save();
        }

        return to_route('settings.profile.edit')->with('status', __('Profile photo deleted successfully'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }
}
