<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Support\PushNotifications;
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

        // Gespeicherte Einstellungen, fehlende Schlüssel mit Standardwerten
        $preferences = array_merge([
            'email_booking_confirmed' => true,
            'email_booking_cancelled' => true,
            'email_event_reminder' => true,
            'email_event_updated' => true,
            'email_new_review' => false,
            'email_marketing' => false,
        ], $user->notification_preferences);

        $push = [
            'configured' => PushNotifications::configured(),
            'publicKey' => PushNotifications::publicKey(),
            'enabled' => PushNotifications::enabledFor($user),
            'devices' => $user->pushSubscriptions()->count(),
            // Damit der Browser erkennt, ob sein Abo noch gilt – ohne die Endpunkte selbst auszugeben
            'knownEndpoints' => $user->pushSubscriptions()->pluck('endpoint')
                ->map(fn (string $endpoint) => hash('sha256', $endpoint))->values()->all(),
        ];

        return view('settings.notifications', compact('preferences', 'push'));
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
            // Für den BookingController
            'booking_notifications' => $request->has('email_booking_confirmed'),
            'event_updates' => $request->has('email_event_updated'),
            'reminder_notifications' => $request->has('email_event_reminder'),
        ];

        // Update user preferences
        // Übrige Einstellungen (z. B. Push, Abzeichen, Kontakte) bleiben erhalten
        $user->update([
            'notification_preferences' => array_merge($user->notification_preferences, $preferences),
        ]);

        return back()->with('success', 'Benachrichtigungseinstellungen wurden aktualisiert.');
    }
}
