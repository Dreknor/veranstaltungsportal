<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

        // Calculate statistics
        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'upcoming_events' => $upcomingBookings->count(),
            'past_events' => $pastBookings->count(),
            'total_spent' => $user->bookings()
                ->where('payment_status', 'paid')
                ->sum('total'),
        ];

        return view('user.dashboard', compact('user', 'bookings', 'upcomingBookings', 'pastBookings', 'stats'));
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
}

