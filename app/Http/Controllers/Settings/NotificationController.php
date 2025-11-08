<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Show notification settings form
     */
    public function edit()
    {
        $user = Auth::user();

        // Get current notification preferences from user meta or defaults
        $preferences = $user->notification_preferences ?? [
            'email_booking_confirmed' => true,
            'email_booking_cancelled' => true,
            'email_event_reminder' => true,
            'email_event_updated' => true,
            'email_new_review' => false,
            'email_marketing' => false,
            'push_booking_confirmed' => false,
            'push_event_reminder' => false,
            'push_event_updated' => false,
        ];

        return view('settings.notifications', compact('preferences'));
    }

    /**
     * Update notification settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email_booking_confirmed' => 'boolean',
            'email_booking_cancelled' => 'boolean',
            'email_event_reminder' => 'boolean',
            'email_event_updated' => 'boolean',
            'email_new_review' => 'boolean',
            'email_marketing' => 'boolean',
            'push_booking_confirmed' => 'boolean',
            'push_event_reminder' => 'boolean',
            'push_event_updated' => 'boolean',
        ]);

        // Convert to booleans
        foreach ($validated as $key => $value) {
            $validated[$key] = (bool) $value;
        }

        // Update user preferences
        $user->update([
            'notification_preferences' => $validated,
        ]);

        return back()->with('success', 'Benachrichtigungseinstellungen wurden aktualisiert.');
    }
}

