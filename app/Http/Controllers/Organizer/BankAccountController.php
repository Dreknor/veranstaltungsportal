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
        $organization = Auth::user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $bankAccount = $organization->bank_account ?? [
            'account_holder' => '',
            'bank_name' => '',
            'iban' => '',
            'bic' => '',
        ];

        return view('organizer.bank-account.index', compact('bankAccount', 'organization'));
    }

    /**
     * Update bank account settings
     */
    public function update(Request $request)
    {
        $organization = Auth::user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $validated = $request->validate([
            'account_holder' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
            'bic' => 'required|string|max:11',
        ]);

        $organization->update([
            'bank_account' => $validated
        ]);

        return redirect()->route('organizer.bank-account.index')
            ->with('success', 'Kontoverbindung wurde erfolgreich aktualisiert.');
    }

    /**
     * Show billing data settings for organizer
     */
    public function billingData()
    {
        $organization = Auth::user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $billingData = $organization->billing_data ?? [
            'company_name' => $organization->name ?? '',
            'company_address' => '',
            'company_postal_code' => '',
            'company_city' => '',
            'company_country' => 'Deutschland',
            'tax_id' => $organization->tax_id ?? '',
            'vat_id' => '',
            'company_email' => $organization->email,
            'company_phone' => $organization->phone ?? '',
        ];

        return view('organizer.bank-account.billing-data', compact('billingData', 'organization'));
    }

    /**
     * Update billing data
     */
    public function updateBillingData(Request $request)
    {
        $organization = Auth::user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

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
            'invoice_number_format' => 'required|string|max:100',
        ]);

        $organization->update([
            'billing_data' => $validated,
            'name' => $validated['company_name'],
            'tax_id' => $validated['tax_id'],
            'email' => $validated['company_email'],
            'phone' => $validated['company_phone'],
        ]);

        return redirect()->route('organizer.bank-account.billing-data')
            ->with('success', 'Rechnungsdaten wurden erfolgreich aktualisiert.');
    }
}
