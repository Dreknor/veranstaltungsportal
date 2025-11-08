<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    /**
     * Display the user dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Get user's bookings with event and items
        $bookings = $user->bookings()
            ->with(['event', 'items.ticketType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get upcoming events
        $upcomingBookings = $user->bookings()
            ->with(['event'])
            ->whereHas('event', function ($query) {
                $query->where('start_date', '>', now());
            })
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get past events
        $pastBookings = $user->bookings()
            ->with(['event'])
            ->whereHas('event', function ($query) {
                $query->where('end_date', '<', now());
            })
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent notifications
        $notifications = $user->notifications()
            ->latest()
            ->limit(5)
            ->get();

        $unreadNotificationsCount = $user->unreadNotifications()->count();

        // Calculate statistics
        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'upcoming_events' => $upcomingBookings->count(),
            'past_events' => $pastBookings->count(),
        ];

        return view('user.dashboard', compact('user', 'bookings', 'upcomingBookings', 'pastBookings', 'stats', 'notifications', 'unreadNotificationsCount'));
    }

    /**
     * Display user's booking history
     */
    public function bookingHistory()
    {
        $user = Auth::user();

        $bookings = $user->bookings()
            ->with(['event', 'items.ticketType'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.booking-history', compact('bookings'));
    }

    /**
     * Display user's upcoming events
     */
    public function upcomingEvents()
    {
        $user = Auth::user();

        $bookings = $user->bookings()
            ->with(['event', 'items.ticketType'])
            ->whereHas('event', function ($query) {
                $query->where('start_date', '>', now());
            })
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.upcoming-events', compact('bookings'));
    }

    /**
     * Display user's past events
     */
    public function pastEvents()
    {
        $user = Auth::user();

        $bookings = $user->bookings()
            ->with(['event', 'items.ticketType'])
            ->whereHas('event', function ($query) {
                $query->where('end_date', '<', now());
            })
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.past-events', compact('bookings'));
    }

    /**
     * Display user statistics
     */
    public function statistics()
    {
        $user = Auth::user();

        // Calculate comprehensive statistics
        $stats = [
            // Booking stats
            'total_bookings' => $user->bookings()->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'cancelled_bookings' => $user->bookings()->where('status', 'cancelled')->count(),

            // Event stats
            'total_events_attended' => $user->bookings()
                ->where('status', 'confirmed')
                ->whereHas('event', function ($query) {
                    $query->where('end_date', '<', now());
                })
                ->count(),

            'upcoming_events' => $user->bookings()
                ->where('status', 'confirmed')
                ->whereHas('event', function ($query) {
                    $query->where('start_date', '>', now());
                })
                ->count(),

            // Financial stats
            'total_spent' => $user->bookings()
                ->where('payment_status', 'paid')
                ->sum('total'),

            'average_booking_value' => $user->bookings()
                ->where('payment_status', 'paid')
                ->avg('total'),

            // Category breakdown
            'events_by_category' => $user->bookings()
                ->where('status', 'confirmed')
                ->with('event.category')
                ->get()
                ->groupBy('event.category.name')
                ->map(function ($items) {
                    return $items->count();
                })
                ->sortDesc(),

            // Time stats
            'total_hours' => $this->calculateTotalHours($user),

            // This year stats
            'bookings_this_year' => $user->bookings()
                ->whereYear('created_at', now()->year)
                ->count(),

            'events_this_year' => $user->bookings()
                ->where('status', 'confirmed')
                ->whereHas('event', function ($query) {
                    $query->whereYear('start_date', now()->year);
                })
                ->count(),

            // Monthly breakdown for chart
            'bookings_by_month' => $user->bookings()
                ->whereYear('created_at', now()->year)
                ->get()
                ->groupBy(function ($booking) {
                    return $booking->created_at->format('m');
                })
                ->map(function ($items) {
                    return $items->count();
                }),

            // Reviews given
            'reviews_count' => \App\Models\EventReview::where('user_id', $user->id)->count(),
            'average_rating_given' => \App\Models\EventReview::where('user_id', $user->id)->avg('rating'),
        ];

        return view('user.statistics', compact('stats'));
    }

    /**
     * Calculate total hours from attended events
     */
    private function calculateTotalHours($user)
    {
        $bookings = $user->bookings()
            ->where('status', 'confirmed')
            ->whereHas('event', function ($query) {
                $query->where('end_date', '<', now());
            })
            ->with('event')
            ->get();

        $totalMinutes = 0;
        foreach ($bookings as $booking) {
            $totalMinutes += $booking->event->start_date->diffInMinutes($booking->event->end_date);
        }

        return round($totalMinutes / 60, 1);
    }
}

