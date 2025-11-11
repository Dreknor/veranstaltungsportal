<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrivacyController extends Controller
{
    /**
     * Display the privacy settings form.
     */
    public function edit(Request $request): View
    {
        return view('settings.privacy', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's privacy settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'show_email_to_connections' => 'nullable|boolean',
            'show_phone_to_connections' => 'nullable|boolean',
            // Konsolidierte DSGVO-Einstellungen
            'allow_networking' => 'nullable|boolean',
            'show_profile_public' => 'nullable|boolean',
            'allow_data_analytics' => 'nullable|boolean',
        ]);

        $user = $request->user();

        $user->update([
            'show_email_to_connections' => $request->boolean('show_email_to_connections'),
            'show_phone_to_connections' => $request->boolean('show_phone_to_connections'),
            // Konsolidierte DSGVO-Einstellungen
            'allow_networking' => $request->boolean('allow_networking'),
            'show_profile_public' => $request->boolean('show_profile_public'),
            'allow_data_analytics' => $request->boolean('allow_data_analytics'),
        ]);

        // Log the privacy settings update
        AuditLog::log(
            'privacy_settings_updated',
            $user,
            null,
            null,
            'Benutzer hat Datenschutzeinstellungen aktualisiert'
        );

        return redirect()->route('settings.privacy.edit')
            ->with('status', 'Datenschutz-Einstellungen wurden erfolgreich aktualisiert.');
    }
}

