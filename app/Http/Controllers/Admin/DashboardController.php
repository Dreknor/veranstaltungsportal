<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Booking;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_organizers' => User::role('organizer')->count(),
            'total_events' => Event::count(),
            'published_events' => Event::where('is_published', true)->count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::whereIn('payment_status', ['paid'])->sum('total'),
            'pending_events' => Event::where('is_published', false)->count(),
            'recent_bookings_count' => Booking::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $recentUsers = User::latest()->limit(10)->get();
        $recentEvents = Event::with(['organization.users', 'category'])->latest()->limit(10)->get();
        $recentBookings = Booking::with(['event', 'user'])->latest()->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentEvents', 'recentBookings'));
    }
}

