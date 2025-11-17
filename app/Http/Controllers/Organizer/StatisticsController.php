<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $orgId = $organization->id;

        $totalEvents = Event::where('organization_id', $orgId)->count();
        $publishedEvents = Event::where('organization_id', $orgId)->where('is_published', true)->count();
        $upcomingEvents = Event::where('organization_id', $orgId)->where('start_date', '>', now())->count();
        $pastEvents = Event::where('organization_id', $orgId)->where('end_date', '<', now())->count();

        $totalBookings = Booking::whereHas('event', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->count();

        $confirmedBookings = Booking::whereHas('event', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->where('status', 'confirmed')->count();

        $pendingBookings = Booking::whereHas('event', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->where('status', 'pending')->count();

        $cancelledBookings = Booking::whereHas('event', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->where('status', 'cancelled')->count();

        $totalRevenue = Booking::whereHas('event', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->where('status', '!=', 'cancelled')->sum('total_amount');

        $confirmedRevenue = Booking::whereHas('event', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->where('status', 'confirmed')->sum('total_amount');

        $pendingRevenue = Booking::whereHas('event', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })->where('status', 'pending')->sum('total_amount');

        $ticketsSold = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->where('events.organization_id', $orgId)
            ->where('bookings.status', '!=', 'cancelled')
            ->sum('booking_items.quantity');

        $monthlyBookings = Booking::whereHas('event', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
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

        $topEventsByRevenue = Event::where('organization_id', $orgId)
            ->withSum(['bookings' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }], 'total_amount')
            ->withCount(['bookings' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }])
            ->orderBy('bookings_sum_total_amount', 'desc')
            ->limit(10)
            ->get();

        $topEventsByAttendees = Event::where('organization_id', $orgId)
            ->withCount(['bookings' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }])
            ->orderBy('bookings_count', 'desc')
            ->limit(10)
            ->get();

        $categoryStats = Event::where('organization_id', $orgId)
            ->select('event_category_id', DB::raw('COUNT(*) as count'))
            ->with('category')
            ->groupBy('event_category_id')
            ->get();

        $recentBookings = Booking::whereHas('event', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })
        ->with(['event', 'items.ticketType'])
        ->latest()
        ->limit(10)
        ->get();

        $paymentStats = Booking::whereHas('event', function($q) use ($orgId) {
            $q->where('organization_id', $orgId);
        })
        ->select('payment_status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
        ->groupBy('payment_status')
        ->get();

        $avgTicketPrice = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->where('events.organization_id', $orgId)
            ->where('bookings.status', '!=', 'cancelled')
            ->avg('booking_items.price');

        $conversionRate = $totalBookings > 0
            ? round(($confirmedBookings / $totalBookings) * 100, 2)
            : 0;

        return view('organizer.statistics.index', compact(
            'totalEvents','publishedEvents','upcomingEvents','pastEvents','totalBookings','confirmedBookings','pendingBookings','cancelledBookings','totalRevenue','confirmedRevenue','pendingRevenue','ticketsSold','monthlyBookings','topEventsByRevenue','topEventsByAttendees','categoryStats','recentBookings','paymentStats','avgTicketPrice','conversionRate','startDate','endDate','organization'
        ));
    }

    public function eventStatistics(Event $event)
    {
        $this->authorize('view', $event);

        $totalBookings = $event->bookings()->where('status', '!=', 'cancelled')->count();
        $confirmedBookings = $event->bookings()->where('status', 'confirmed')->count();
        $pendingBookings = $event->bookings()->where('status', 'pending')->count();
        $cancelledBookings = $event->bookings()->where('status', 'cancelled')->count();

        $totalRevenue = $event->bookings()->where('status', '!=', 'cancelled')->sum('total_amount');
        $confirmedRevenue = $event->bookings()->where('status', 'confirmed')->sum('total_amount');
        $pendingRevenue = $event->bookings()->where('status', 'pending')->sum('total_amount');

        $ticketsSold = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->where('bookings.event_id', $event->id)
            ->where('bookings.status', '!=', 'cancelled')
            ->sum('booking_items.quantity');

        $ticketsAvailable = $event->max_attendees ? $event->max_attendees - $ticketsSold : null;
        $capacityPercentage = $event->max_attendees ? round(($ticketsSold / $event->max_attendees) * 100, 2) : null;

        return view('organizer.statistics.event', compact(
            'event','totalBookings','confirmedBookings','pendingBookings','cancelledBookings','totalRevenue','confirmedRevenue','pendingRevenue','ticketsSold','ticketsAvailable','capacityPercentage'
        ));
    }
}

