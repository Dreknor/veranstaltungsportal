<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total_events' => Event::where('user_id', $user->id)->count(),
            'published_events' => Event::where('user_id', $user->id)->where('is_published', true)->count(),
            'upcoming_events' => Event::where('user_id', $user->id)->upcoming()->count(),
            'total_bookings' => $user->events()->withCount('bookings')->get()->sum('bookings_count'),
            'total_revenue' => $user->events()
                ->join('bookings', 'events.id', '=', 'bookings.event_id')
                ->whereIn('bookings.payment_status', ['paid'])
                ->sum('bookings.total'),
        ];

        $upcomingEvents = Event::where('user_id', $user->id)
            ->upcoming()
            ->published()
            ->with(['category', 'bookings'])
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        $recentBookings = \App\Models\Booking::whereHas('event', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['event', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('organizer.dashboard', compact('stats', 'upcomingEvents', 'recentBookings'));
    }
}

