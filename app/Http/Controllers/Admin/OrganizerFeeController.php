<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizerFeeController extends Controller
{
    /**
     * Show organization fee settings
     */
    public function edit(Organization $organization)
    {
        $customFee = $organization->custom_platform_fee ?? [];
        $globalSettings = [
            'fee_percentage' => config('monetization.platform_fee_percentage', 5.0),
            'fee_type' => config('monetization.platform_fee_type', 'percentage'),
            'fee_fixed_amount' => config('monetization.platform_fee_fixed_amount', 0),
            'minimum_fee' => config('monetization.platform_fee_minimum', 1.00),
        ];

        return view('admin.organizer-fees.edit', compact('organization', 'customFee', 'globalSettings'));
    }

    /**
     * Update organization fee settings
     */
    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'use_custom_fee' => 'boolean',
            'custom_fee_type' => 'nullable|in:percentage,fixed',
            'custom_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'custom_fee_fixed_amount' => 'nullable|numeric|min:0',
            'custom_minimum_fee' => 'nullable|numeric|min:0',
            'custom_fee_notes' => 'nullable|string|max:500',
        ]);

        $customFee = null;

        if ($request->boolean('use_custom_fee')) {
            $customFee = [
                'enabled' => true,
                'fee_type' => $validated['custom_fee_type'] ?? 'percentage',
                'fee_percentage' => $validated['custom_fee_percentage'] ?? 0,
                'fee_fixed_amount' => $validated['custom_fee_fixed_amount'] ?? 0,
                'minimum_fee' => $validated['custom_minimum_fee'] ?? config('monetization.platform_fee_minimum', 1.00),
                'notes' => $validated['custom_fee_notes'] ?? '',
                'updated_by' => auth()->id(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        $organization->update([
            'custom_platform_fee' => $customFee
        ]);

        return redirect()
            ->route('admin.organizer-fees.edit', $organization)
            ->with('success', 'Individuelle Gebühren-Einstellungen wurden gespeichert.');
    }

    /**
     * Remove custom fee settings
     */
    public function destroy(Organization $organization)
    {
        $organization->update([
            'custom_platform_fee' => null
        ]);

        return redirect()
            ->route('admin.organizer-fees.edit', $organization)
            ->with('success', 'Individuelle Gebühren wurden entfernt. Es gelten nun die Standard-Einstellungen.');
    }
}

