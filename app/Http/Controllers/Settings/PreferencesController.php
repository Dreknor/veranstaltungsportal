<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferencesController extends Controller
{
    /**
     * Show preferences form
     */
    public function edit()
    {
        $user = Auth::user();

        return view('settings.preferences', compact('user'));
    }

    /**
     * Update user preferences
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'language' => 'nullable|string|in:de,en',
            'timezone' => 'nullable|string',
            'date_format' => 'nullable|string|in:d.m.Y,Y-m-d,m/d/Y',
            'time_format' => 'nullable|string|in:H:i,h:i A',
            'items_per_page' => 'nullable|integer|min:10|max:100',
            'auto_add_to_calendar' => 'boolean',
            'show_past_events' => 'boolean',
        ]);

        // Store in user preferences or settings table
        // For now, we'll use the notification_preferences field as a general preferences field
        $preferences = $user->notification_preferences ?? [];

        foreach ($validated as $key => $value) {
            $preferences[$key] = $value;
        }

        $user->update([
            'notification_preferences' => $preferences,
        ]);

        return back()->with('success', 'Einstellungen wurden gespeichert.');
    }
}

