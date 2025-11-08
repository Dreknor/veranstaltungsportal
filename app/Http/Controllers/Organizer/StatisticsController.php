<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'organizer']);
    }

    public function index(Request $request)
    {
        $userId = auth()->id();

        // Date range filter (default: last 30 days)
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Overview Statistics
        $totalEvents = Event::where('user_id', $userId)->count();
        $publishedEvents = Event::where('user_id', $userId)->where('is_published', true)->count();
        $upcomingEvents = Event::where('user_id', $userId)
            ->where('start_date', '>', now())
            ->count();
        $pastEvents = Event::where('user_id', $userId)
            ->where('end_date', '<', now())
            ->count();

        // Booking Statistics
        $totalBookings = Booking::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        $confirmedBookings = Booking::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'confirmed')->count();

        $pendingBookings = Booking::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'pending')->count();

        $cancelledBookings = Booking::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'cancelled')->count();

        // Revenue Statistics
        $totalRevenue = Booking::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', '!=', 'cancelled')->sum('total_amount');

        $confirmedRevenue = Booking::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'confirmed')->sum('total_amount');

        $pendingRevenue = Booking::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'pending')->sum('total_amount');

        // Tickets Sold
        $ticketsSold = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->where('events.user_id', $userId)
            ->where('bookings.status', '!=', 'cancelled')
            ->sum('booking_items.quantity');

        // Monthly bookings chart data
        $monthlyBookings = Booking::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->where('status', '!=', 'cancelled')
        ->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as revenue')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Top Events by Revenue
        $topEventsByRevenue = Event::where('user_id', $userId)
            ->withSum(['bookings' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }], 'total_amount')
            ->withCount(['bookings' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }])
            ->orderBy('bookings_sum_total_amount', 'desc')
            ->limit(10)
            ->get();

        // Top Events by Attendees
        $topEventsByAttendees = Event::where('user_id', $userId)
            ->withCount(['bookings' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }])
            ->orderBy('bookings_count', 'desc')
            ->limit(10)
            ->get();

        // Category Distribution
        $categoryStats = Event::where('user_id', $userId)
            ->select('event_category_id', DB::raw('COUNT(*) as count'))
            ->with('category')
            ->groupBy('event_category_id')
            ->get();

        // Recent Bookings (Last 10)
        $recentBookings = Booking::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->with(['event', 'items.ticketType'])
        ->latest()
        ->limit(10)
        ->get();

        // Payment Status Distribution
        $paymentStats = Booking::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->select('payment_status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
        ->groupBy('payment_status')
        ->get();

        // Average ticket price
        $avgTicketPrice = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->where('events.user_id', $userId)
            ->where('bookings.status', '!=', 'cancelled')
            ->avg('booking_items.price');

        // Conversion Rate (bookings / event views) - would need view tracking
        // For now, we'll calculate confirmed bookings vs total bookings
        $conversionRate = $totalBookings > 0
            ? round(($confirmedBookings / $totalBookings) * 100, 2)
            : 0;

        return view('organizer.statistics.index', compact(
            'totalEvents',
            'publishedEvents',
            'upcomingEvents',
            'pastEvents',
            'totalBookings',
            'confirmedBookings',
            'pendingBookings',
            'cancelledBookings',
            'totalRevenue',
            'confirmedRevenue',
            'pendingRevenue',
            'ticketsSold',
            'monthlyBookings',
            'topEventsByRevenue',
            'topEventsByAttendees',
            'categoryStats',
            'recentBookings',
            'paymentStats',
            'avgTicketPrice',
            'conversionRate',
            'startDate',
            'endDate'
        ));
    }

    public function eventStatistics(Event $event)
    {
        $this->authorize('view', $event);

        // Event Overview
        $totalBookings = $event->bookings()->where('status', '!=', 'cancelled')->count();
        $confirmedBookings = $event->bookings()->where('status', 'confirmed')->count();
        $pendingBookings = $event->bookings()->where('status', 'pending')->count();
        $cancelledBookings = $event->bookings()->where('status', 'cancelled')->count();

        // Revenue
        $totalRevenue = $event->bookings()->where('status', '!=', 'cancelled')->sum('total_amount');
        $confirmedRevenue = $event->bookings()->where('status', 'confirmed')->sum('total_amount');
        $pendingRevenue = $event->bookings()->where('status', 'pending')->sum('total_amount');

        // Tickets
        $ticketsSold = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->where('bookings.event_id', $event->id)
            ->where('bookings.status', '!=', 'cancelled')
            ->sum('booking_items.quantity');

        $ticketsAvailable = $event->max_attendees ? $event->max_attendees - $ticketsSold : null;
        $capacityPercentage = $event->max_attendees
            ? round(($ticketsSold / $event->max_attendees) * 100, 2)
            : null;

        // Ticket Type Distribution
        $ticketTypeStats = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->join('ticket_types', 'booking_items.ticket_type_id', '=', 'ticket_types.id')
            ->where('bookings.event_id', $event->id)
            ->where('bookings.status', '!=', 'cancelled')
            ->select(
                'ticket_types.name',
                DB::raw('SUM(booking_items.quantity) as quantity_sold'),
                DB::raw('SUM(booking_items.price * booking_items.quantity) as revenue')
            )
            ->groupBy('ticket_types.id', 'ticket_types.name')
            ->get();

        // Daily Booking Trend
        $dailyBookings = $event->bookings()
            ->where('status', '!=', 'cancelled')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Discount Code Usage
        $discountCodeStats = $event->bookings()
            ->whereNotNull('discount_code_id')
            ->where('status', '!=', 'cancelled')
            ->with('discountCode')
            ->select('discount_code_id', DB::raw('COUNT(*) as usage_count'), DB::raw('SUM(discount) as total_discount'))
            ->groupBy('discount_code_id')
            ->get();

        // Check-in Statistics
        $checkedInCount = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->where('bookings.event_id', $event->id)
            ->where('bookings.status', '!=', 'cancelled')
            ->whereNotNull('booking_items.checked_in_at')
            ->sum('booking_items.quantity');

        $checkInRate = $ticketsSold > 0
            ? round(($checkedInCount / $ticketsSold) * 100, 2)
            : 0;

        return view('organizer.statistics.event', compact(
            'event',
            'totalBookings',
            'confirmedBookings',
            'pendingBookings',
            'cancelledBookings',
            'totalRevenue',
            'confirmedRevenue',
            'pendingRevenue',
            'ticketsSold',
            'ticketsAvailable',
            'capacityPercentage',
            'ticketTypeStats',
            'dailyBookings',
            'discountCodeStats',
            'checkedInCount',
            'checkInRate'
        ));
    }
}

