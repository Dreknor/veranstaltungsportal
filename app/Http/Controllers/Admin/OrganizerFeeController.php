<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizerFeeController extends Controller
{
    /**
     * Show organizer fee settings
     */
    public function edit(User $user)
    {
        if (!$user->hasRole('organizer')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Dieser Benutzer ist kein Organisator.');
        }

        $customFee = $user->custom_platform_fee ?? [];
        $globalSettings = [
            'fee_percentage' => config('monetization.platform_fee_percentage', 5.0),
            'fee_type' => config('monetization.platform_fee_type', 'percentage'),
            'fee_fixed_amount' => config('monetization.platform_fee_fixed_amount', 0),
            'minimum_fee' => config('monetization.platform_fee_minimum', 1.00),
        ];

        return view('admin.organizer-fees.edit', compact('user', 'customFee', 'globalSettings'));
    }

    /**
     * Update organizer fee settings
     */
    public function update(Request $request, User $user)
    {
        if (!$user->hasRole('organizer')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Dieser Benutzer ist kein Organisator.');
        }

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

        $user->update([
            'custom_platform_fee' => $customFee
        ]);

        return redirect()
            ->route('admin.organizer-fees.edit', $user)
            ->with('success', 'Individuelle Gebühren-Einstellungen wurden gespeichert.');
    }

    /**
     * Remove custom fee settings
     */
    public function destroy(User $user)
    {
        $user->update([
            'custom_platform_fee' => null
        ]);

        return redirect()
            ->route('admin.organizer-fees.edit', $user)
            ->with('success', 'Individuelle Gebühren wurden entfernt. Es gelten nun die Standard-Einstellungen.');
    }
}

