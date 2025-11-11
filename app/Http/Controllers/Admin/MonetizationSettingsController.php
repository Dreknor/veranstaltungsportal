<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MonetizationSettingsController extends Controller
{
    /**
     * Display monetization settings
     */
    public function index()
    {
        $settings = [
            'platform_fee_percentage' => config('monetization.platform_fee_percentage', 5.0),
            'platform_fee_type' => config('monetization.platform_fee_type', 'percentage'), // percentage or fixed
            'platform_fee_fixed_amount' => config('monetization.platform_fee_fixed_amount', 0),
            'platform_fee_minimum' => config('monetization.platform_fee_minimum', 1.00),
            'auto_invoice' => config('monetization.auto_invoice', true),
            'invoice_cc_email' => config('monetization.invoice_cc_email', ''),
            'payment_deadline_days' => config('monetization.payment_deadline_days', 14),
            // Featured Event Settings
            'featured_event_enabled' => config('monetization.featured_event_enabled', true),
            'featured_event_daily_rate' => config('monetization.featured_event_rates.daily', 5.00),
            'featured_event_weekly_rate' => config('monetization.featured_event_rates.weekly', 25.00),
            'featured_event_monthly_rate' => config('monetization.featured_event_rates.monthly', 80.00),
            'featured_event_max_duration_days' => config('monetization.featured_event_max_duration_days', 90),
            'featured_event_auto_disable_on_expiry' => config('monetization.featured_event_auto_disable_on_expiry', true),
        ];

        // Get organizers with custom fees
        $organizersWithCustomFees = \App\Models\User::role('organizer')
            ->whereNotNull('custom_platform_fee')
            ->orderBy('name')
            ->get()
            ->filter(function($user) {
                return !empty($user->custom_platform_fee) && ($user->custom_platform_fee['enabled'] ?? false);
            });

        // Get Featured Event Statistics
        $featuredStats = [
            'active_featured_events' => \App\Models\Event::where('is_featured', true)->count(),
            'total_featured_fees' => \App\Models\FeaturedEventFee::where('payment_status', 'paid')->count(),
            'pending_payments' => \App\Models\FeaturedEventFee::where('payment_status', 'pending')->count(),
            'total_revenue' => \App\Models\FeaturedEventFee::where('payment_status', 'paid')->sum('fee_amount'),
            'this_month_revenue' => \App\Models\FeaturedEventFee::where('payment_status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('fee_amount'),
        ];

        return view('admin.monetization.index', compact('settings', 'organizersWithCustomFees', 'featuredStats'));
    }

    /**
     * Update monetization settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'platform_fee_percentage' => 'required|numeric|min:0|max:100',
            'platform_fee_type' => 'required|in:percentage,fixed',
            'platform_fee_fixed_amount' => 'nullable|numeric|min:0',
            'platform_fee_minimum' => 'required|numeric|min:0',
            'auto_invoice' => 'boolean',
            'invoice_cc_email' => 'nullable|email',
            'payment_deadline_days' => 'required|integer|min:1|max:90',
            // Featured Event Validation
            'featured_event_enabled' => 'boolean',
            'featured_event_daily_rate' => 'required|numeric|min:0',
            'featured_event_weekly_rate' => 'required|numeric|min:0',
            'featured_event_monthly_rate' => 'required|numeric|min:0',
            'featured_event_max_duration_days' => 'required|integer|min:1|max:365',
            'featured_event_auto_disable_on_expiry' => 'boolean',
        ]);

        try {
            $this->updateEnvFile([
                'PLATFORM_FEE_PERCENTAGE' => $validated['platform_fee_percentage'],
                'PLATFORM_FEE_TYPE' => $validated['platform_fee_type'],
                'PLATFORM_FEE_FIXED_AMOUNT' => $validated['platform_fee_fixed_amount'] ?? 0,
                'PLATFORM_FEE_MINIMUM' => $validated['platform_fee_minimum'],
                'AUTO_INVOICE' => $validated['auto_invoice'] ?? false,
                'INVOICE_CC_EMAIL' => $validated['invoice_cc_email'] ?? '',
                'PAYMENT_DEADLINE_DAYS' => $validated['payment_deadline_days'],
                // Featured Event Settings
                'FEATURED_EVENT_ENABLED' => $validated['featured_event_enabled'] ?? false,
                'FEATURED_EVENT_DAILY_RATE' => $validated['featured_event_daily_rate'],
                'FEATURED_EVENT_WEEKLY_RATE' => $validated['featured_event_weekly_rate'],
                'FEATURED_EVENT_MONTHLY_RATE' => $validated['featured_event_monthly_rate'],
                'FEATURED_EVENT_MAX_DURATION_DAYS' => $validated['featured_event_max_duration_days'],
                'FEATURED_EVENT_AUTO_DISABLE_ON_EXPIRY' => $validated['featured_event_auto_disable_on_expiry'] ?? false,
            ]);

            Cache::forget('monetization_settings');

            return redirect()
                ->route('admin.monetization.index')
                ->with('success', 'Monetarisierungseinstellungen wurden erfolgreich aktualisiert.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.monetization.index')
                ->with('error', 'Fehler beim Speichern: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update .env file
     */
    private function updateEnvFile(array $data)
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            throw new \Exception('.env Datei nicht gefunden.');
        }

        if (!is_writable($envPath)) {
            throw new \Exception('.env Datei ist nicht beschreibbar. Bitte Berechtigungen prüfen.');
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            // Escape special characters in value
            $value = $this->escapeEnvValue($value);

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                // Add new key
                $envContent .= "\n{$key}={$value}";
            }
        }

        $result = file_put_contents($envPath, $envContent);

        if ($result === false) {
            throw new \Exception('Fehler beim Schreiben der .env Datei.');
        }
    }

    /**
     * Escape value for .env file
     */
    private function escapeEnvValue($value)
    {
        // Convert boolean to string
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        // If value contains spaces or special characters, wrap in quotes
        if (preg_match('/\s/', $value) || empty($value)) {
            return '"' . str_replace('"', '\\"', $value) . '"';
        }

        return $value;
    }

    /**
     * Show billing data settings for platform
     */
    public function billingData()
    {
        $billingData = [
            'company_name' => config('monetization.platform_company_name', ''),
            'company_address' => config('monetization.platform_company_address', ''),
            'company_postal_code' => config('monetization.platform_company_postal_code', ''),
            'company_city' => config('monetization.platform_company_city', ''),
            'company_country' => config('monetization.platform_company_country', 'Deutschland'),
            'tax_id' => config('monetization.platform_tax_id', ''),
            'vat_id' => config('monetization.platform_vat_id', ''),
            'company_email' => config('monetization.platform_company_email', ''),
            'company_phone' => config('monetization.platform_company_phone', ''),
            'bank_name' => config('monetization.platform_bank_name', ''),
            'bank_iban' => config('monetization.platform_bank_iban', ''),
            'bank_bic' => config('monetization.platform_bank_bic', ''),
        ];

        return view('admin.monetization.billing-data', compact('billingData'));
    }

    /**
     * Update billing data
     */
    public function updateBillingData(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'company_postal_code' => 'required|string|max:10',
            'company_city' => 'required|string|max:100',
            'company_country' => 'required|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'vat_id' => 'nullable|string|max:50',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            'bank_iban' => 'required|string|max:34',
            'bank_bic' => 'required|string|max:11',
        ]);

        try {
            $this->updateEnvFile([
                'PLATFORM_COMPANY_NAME' => $validated['company_name'],
                'PLATFORM_COMPANY_ADDRESS' => $validated['company_address'],
                'PLATFORM_COMPANY_POSTAL_CODE' => $validated['company_postal_code'],
                'PLATFORM_COMPANY_CITY' => $validated['company_city'],
                'PLATFORM_COMPANY_COUNTRY' => $validated['company_country'],
                'PLATFORM_TAX_ID' => $validated['tax_id'] ?? '',
                'PLATFORM_VAT_ID' => $validated['vat_id'] ?? '',
                'PLATFORM_COMPANY_EMAIL' => $validated['company_email'],
                'PLATFORM_COMPANY_PHONE' => $validated['company_phone'],
                'PLATFORM_BANK_NAME' => $validated['bank_name'],
                'PLATFORM_BANK_IBAN' => $validated['bank_iban'],
                'PLATFORM_BANK_BIC' => $validated['bank_bic'],
            ]);

            Cache::forget('platform_billing_data');

            return redirect()
                ->route('admin.monetization.billing-data')
                ->with('success', 'Rechnungsdaten wurden erfolgreich aktualisiert.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.monetization.billing-data')
                ->with('error', 'Fehler beim Speichern: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show Featured Events overview
     */
    public function featuredEvents()
    {
        $featuredFees = \App\Models\FeaturedEventFee::with(['event', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_revenue' => \App\Models\FeaturedEventFee::where('payment_status', 'paid')->sum('fee_amount'),
            'pending_revenue' => \App\Models\FeaturedEventFee::where('payment_status', 'pending')->sum('fee_amount'),
            'active_featured' => \App\Models\Event::where('is_featured', true)->count(),
            'pending_payments' => \App\Models\FeaturedEventFee::where('payment_status', 'pending')->count(),
            'this_month' => \App\Models\FeaturedEventFee::where('payment_status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('fee_amount'),
            'last_month' => \App\Models\FeaturedEventFee::where('payment_status', 'paid')
                ->whereMonth('paid_at', now()->subMonth()->month)
                ->whereYear('paid_at', now()->subMonth()->year)
                ->sum('fee_amount'),
        ];

        return view('admin.monetization.featured-events', compact('featuredFees', 'stats'));
    }
}

