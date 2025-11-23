<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $organization = $user->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        // Basic Stats
        $stats = [
            'total_events' => $organization->events()->count(),
            'published_events' => $organization->events()->where('is_published', true)->count(),
            'upcoming_events' => $organization->events()->upcoming()->count(),
            'past_events' => $organization->events()->where('end_date', '<', now())->count(),
            'total_bookings' => $organization->bookings()->count(),
            'confirmed_bookings' => $organization->bookings()->where('status', 'confirmed')->count(),
            'pending_bookings' => $organization->bookings()->where('status', 'pending')->count(),
            'total_revenue' => $organization->bookings()
                ->whereIn('payment_status', ['paid'])
                ->sum('total'),
            'pending_revenue' => $organization->bookings()
                ->where('payment_status', 'pending')
                ->sum('total'),
            'total_attendees' => $organization->bookings()
                ->where('status', '!=', 'cancelled')
                ->join('booking_items', 'bookings.id', '=', 'booking_items.booking_id')
                ->sum('booking_items.quantity'),
        ];

        // Organization Info
        $organizationInfo = [
            'name' => $organization->name,
            'member_count' => $organization->users()->wherePivot('is_active', true)->count(),
            'has_complete_billing' => $organization->hasCompleteBillingData(),
            'is_verified' => $organization->is_verified,
        ];

        // Revenue Trend (last 12 months)
        $driver = \DB::connection()->getDriverName();
        $dateFormat = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $revenueTrend = $organization->bookings()
            ->where('created_at', '>=', now()->subMonths(12))
            ->whereIn('payment_status', ['paid'])
            ->selectRaw("{$dateFormat} as month, SUM(total) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top Events by Revenue
        $topEvents = $organization->events()
            ->withSum(['bookings' => function($q) {
                $q->whereIn('payment_status', ['paid']);
            }], 'total')
            ->orderBy('bookings_sum_total', 'desc')
            ->limit(5)
            ->get();

        // Upcoming Events
        $upcomingEvents = $organization->events()
            ->upcoming()
            ->published()
            ->with(['category', 'bookings'])
            ->withCount(['bookings' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }])
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Recent Bookings
        $recentBookings = $organization->bookings()
            ->with(['event', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Team Members
        $teamMembers = $organization->users()
            ->wherePivot('is_active', true)
            ->withPivot(['role', 'joined_at'])
            ->take(5)
            ->get();

        return view('organizer.dashboard', compact(
            'stats',
            'organizationInfo',
            'revenueTrend',
            'topEvents',
            'upcomingEvents',
            'recentBookings',
            'teamMembers',
            'organization'
        ));
    }
}

