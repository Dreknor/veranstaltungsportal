<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrganizationManagementController extends Controller
{
    /**
     * Display a listing of all organizations
     */
    public function index(Request $request)
    {
        $query = Organization::with(['owner', 'events']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Custom fees filter
        if ($request->filled('custom_fees')) {
            if ($request->custom_fees === 'yes') {
                $query->whereNotNull('custom_platform_fee');
            } elseif ($request->custom_fees === 'no') {
                $query->whereNull('custom_platform_fee');
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');

        if ($sortBy === 'events_count') {
            $query->withCount('events')->orderBy('events_count', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $organizations = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => Organization::count(),
            'active' => Organization::where('is_active', true)->count(),
            'inactive' => Organization::where('is_active', false)->count(),
            'with_custom_fees' => Organization::whereNotNull('custom_platform_fee')->count(),
        ];

        return view('admin.organizations.index', compact('organizations', 'stats'));
    }

    /**
     * Show the details of a specific organization
     */
    public function show(Organization $organization)
    {
        $organization->load(['owner', 'events', 'members']);

        $stats = [
            'total_events' => $organization->events()->count(),
            'published_events' => $organization->events()->where('is_published', true)->count(),
            'upcoming_events' => $organization->events()->where('start_date', '>=', now())->count(),
            'total_bookings' => DB::table('bookings')
                ->join('events', 'bookings.event_id', '=', 'events.id')
                ->where('events.organization_id', $organization->id)
                ->where('bookings.status', 'confirmed')
                ->count(),
        ];

        return view('admin.organizations.show', compact('organization', 'stats'));
    }

    /**
     * Toggle organization active status
     */
    public function toggleActive(Organization $organization)
    {
        $organization->update([
            'is_active' => !$organization->is_active
        ]);

        $status = $organization->is_active ? 'aktiviert' : 'deaktiviert';

        return redirect()
            ->back()
            ->with('success', "Organisation wurde {$status}.");
    }

    /**
     * Delete an organization
     */
    public function destroy(Organization $organization)
    {
        // Check if organization has events
        if ($organization->events()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Organisation kann nicht gelöscht werden, da sie noch Veranstaltungen hat.');
        }

        $name = $organization->name;
        $organization->delete();

        return redirect()
            ->route('admin.organizations.index')
            ->with('success', "Organisation \"{$name}\" wurde gelöscht.");
    }
}
