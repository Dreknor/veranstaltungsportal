<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display settings by group
     */
    public function index(Request $request)
    {
        $group = $request->get('group', 'general');

        $settings = Setting::where('group', $group)
            ->orderBy('order')
            ->get();

        $groups = Setting::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return view('admin.settings.index', compact('settings', 'groups', 'group'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $setting->setTypedValue($value);
                $setting->save();
            }
        }

        return redirect()->back()->with('success', 'Einstellungen erfolgreich aktualisiert.');
    }

    /**
     * Create new setting
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'nullable',
            'type' => 'required|in:string,boolean,integer,json',
            'group' => 'required|string',
            'label' => 'required|string',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_public' => 'boolean',
        ]);

        $validated['is_public'] = $request->has('is_public');

        Setting::create($validated);

        return redirect()->back()->with('success', 'Einstellung erfolgreich erstellt.');
    }

    /**
     * Delete setting
     */
    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->back()->with('success', 'Einstellung erfolgreich gelöscht.');
    }

    /**
     * Initialize default settings
     */
    public function initializeDefaults()
    {
        $defaults = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Bildungsportal',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Website Name',
                'description' => 'Name der Website',
                'order' => 1,
                'is_public' => true,
            ],
            [
                'key' => 'site_description',
                'value' => 'Plattform für Fort- und Weiterbildungen',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Website Beschreibung',
                'description' => 'Kurze Beschreibung der Website',
                'order' => 2,
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@bildungsportal.de',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Kontakt E-Mail',
                'description' => 'Haupt-Kontakt E-Mail-Adresse',
                'order' => 3,
                'is_public' => true,
            ],
            [
                'key' => 'support_email',
                'value' => 'support@bildungsportal.de',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Support E-Mail',
                'description' => 'E-Mail für Support-Anfragen',
                'order' => 4,
                'is_public' => true,
            ],

            // Email Settings
            [
                'key' => 'email_from_name',
                'value' => 'Bildungsportal',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Absender Name',
                'description' => 'Name des E-Mail-Absenders',
                'order' => 1,
                'is_public' => false,
            ],
            [
                'key' => 'email_from_address',
                'value' => 'noreply@bildungsportal.de',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Absender E-Mail',
                'description' => 'E-Mail-Adresse des Absenders',
                'order' => 2,
                'is_public' => false,
            ],

            // Booking Settings
            [
                'key' => 'booking_cancellation_hours',
                'value' => '24',
                'type' => 'integer',
                'group' => 'booking',
                'label' => 'Stornierungsfrist (Stunden)',
                'description' => 'Stunden vor Event, bis zu denen storniert werden kann',
                'order' => 1,
                'is_public' => true,
            ],
            [
                'key' => 'enable_guest_booking',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'booking',
                'label' => 'Gast-Buchungen erlauben',
                'description' => 'Erlaubt Buchungen ohne Registrierung',
                'order' => 2,
                'is_public' => true,
            ],

            // Platform Settings
            [
                'key' => 'platform_fee_percentage',
                'value' => '5',
                'type' => 'integer',
                'group' => 'platform',
                'label' => 'Plattform-Gebühr (%)',
                'description' => 'Prozentsatz der Plattform-Gebühr',
                'order' => 1,
                'is_public' => false,
            ],
            [
                'key' => 'min_payout_amount',
                'value' => '50',
                'type' => 'integer',
                'group' => 'platform',
                'label' => 'Minimale Auszahlung (€)',
                'description' => 'Mindestbetrag für Auszahlungen',
                'order' => 2,
                'is_public' => false,
            ],

            // Appearance Settings
            [
                'key' => 'enable_dark_mode',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'appearance',
                'label' => 'Dark Mode aktivieren',
                'description' => 'Erlaubt Nutzern Dark Mode zu verwenden',
                'order' => 1,
                'is_public' => true,
            ],
            [
                'key' => 'primary_color',
                'value' => '#3B82F6',
                'type' => 'string',
                'group' => 'appearance',
                'label' => 'Primärfarbe',
                'description' => 'Hauptfarbe der Website',
                'order' => 2,
                'is_public' => true,
            ],
        ];

        foreach ($defaults as $default) {
            Setting::firstOrCreate(
                ['key' => $default['key']],
                $default
            );
        }

        return redirect()->back()->with('success', 'Standard-Einstellungen initialisiert.');
    }
}

