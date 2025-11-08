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

        // Checkboxen senden nur Werte wenn aktiviert, daher müssen wir alle explizit setzen
        $preferences = [
            'email_booking_confirmed' => $request->has('email_booking_confirmed'),
            'email_booking_cancelled' => $request->has('email_booking_cancelled'),
            'email_event_reminder' => $request->has('email_event_reminder'),
            'email_event_updated' => $request->has('email_event_updated'),
            'email_new_review' => $request->has('email_new_review'),
            'email_marketing' => $request->has('email_marketing'),
            'push_booking_confirmed' => $request->has('push_booking_confirmed'),
            'push_event_reminder' => $request->has('push_event_reminder'),
            'push_event_updated' => $request->has('push_event_updated'),
            // Für den BookingController
            'booking_notifications' => $request->has('email_booking_confirmed'),
            'event_updates' => $request->has('email_event_updated'),
            'reminder_notifications' => $request->has('email_event_reminder'),
        ];

        // Update user preferences
        $user->update([
            'notification_preferences' => $preferences,
        ]);

        return back()->with('success', 'Benachrichtigungseinstellungen wurden aktualisiert.');
    }
}

