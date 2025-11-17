<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    /**
     * Show bank account settings
     */
    public function index()
    {
        $user = Auth::user();

        $bankAccount = $user->bank_account ?? [
            'account_holder' => '',
            'bank_name' => '',
            'iban' => '',
            'bic' => '',
        ];

        return view('organizer.bank-account.index', compact('bankAccount'));
    }

    /**
     * Update bank account settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'account_holder' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
            'bic' => 'required|string|max:11',
        ]);

        $user = Auth::user();

        $user->update([
            'bank_account' => $validated
        ]);

        return redirect()
            ->route('organizer.bank-account.index')
            ->with('success', 'Kontoverbindung wurde erfolgreich aktualisiert.');
    }

    /**
     * Show billing data settings for organizer
     */
    public function billingData()
    {
        $user = Auth::user();

        $billingData = $user->organizer_billing_data ?? [
            'company_name' => $user->organization_name ?? '',
            'company_address' => '',
            'company_postal_code' => '',
            'company_city' => '',
            'company_country' => 'Deutschland',
            'tax_id' => $user->tax_id ?? '',
            'vat_id' => '',
            'company_email' => $user->email,
            'company_phone' => $user->phone ?? '',
        ];

        return view('organizer.bank-account.billing-data', compact('billingData'));
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
            'tax_id' => 'required|string|max:50',
            'vat_id' => 'nullable|string|max:50',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'required|string|max:50',
        ]);

        $user = Auth::user();

        $user->update([
            'organizer_billing_data' => $validated,
            'organization_name' => $validated['company_name'],
            'tax_id' => $validated['tax_id'],
        ]);

        return redirect()
            ->route('organizer.bank-account.billing-data')
            ->with('success', 'Rechnungsdaten wurden erfolgreich aktualisiert.');
    }
}

