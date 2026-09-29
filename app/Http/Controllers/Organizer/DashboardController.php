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
            'pending_invoice_count' => $organization->hasExternalInvoicing()
                ? $organization->bookings()
                    ->where('total', '>', 0)
                    ->where('externally_invoiced', false)
                    ->where('status', '!=', 'cancelled')
                    ->count()
                : 0,
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

        $todo = $this->todo($organization);

        return view('organizer.dashboard', compact(
            'todo',
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

    /**
     * "Heute zu tun": offene Aufgaben über alle kommenden Veranstaltungen der Organisation.
     */
    /**
     * @return array{tasks: array<int, array{count: int, label: string, tone: string, url: string}>, soon: \Illuminate\Support\Collection<int, array<string, mixed>>, almostFull: \Illuminate\Support\Collection<int, \App\Models\Event>, waiting: int}
     */
    protected function todo(\App\Models\Organization $organization): array
    {
        $upcoming = fn ($q) => $q->where('organization_id', $organization->id)->where('end_date', '>=', now());
        $bookings = fn () => \App\Models\Booking::whereHas('event', $upcoming);

        $tasks = [];

        $pendingApproval = $bookings()->where('status', 'pending_approval')->count();
        if ($pendingApproval) {
            $tasks[] = ['count' => $pendingApproval, 'label' => 'Anmeldung(en) warten auf Ihre Freigabe', 'tone' => 'amber',
                'url' => route('organizer.bookings.index', ['status' => 'pending_approval'])];
        }

        $unpaid = $bookings()->where('status', 'pending')->where('payment_status', 'pending')->where('total', '>', 0)->count();
        if ($unpaid) {
            $tasks[] = ['count' => $unpaid, 'label' => 'Buchung(en) mit offener Zahlung', 'tone' => 'yellow',
                'url' => route('organizer.bookings.index', ['status' => 'pending', 'payment_status' => 'pending'])];
        }

        if ($organization->hasExternalInvoicing()) {
            $toInvoice = \App\Models\Booking::whereHas('event', fn ($q) => $q->where('organization_id', $organization->id))
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->where('total', '>', 0)
                ->where('externally_invoiced', false)
                ->count();
            if ($toInvoice) {
                $tasks[] = ['count' => $toInvoice, 'label' => 'Buchung(en) noch nicht fakturiert', 'tone' => 'blue',
                    'url' => route('organizer.billing-data.index', ['filter' => 'pending'])];
            }
        }

        $pendingReviews = \App\Models\EventReview::whereHas('event', fn ($q) => $q->where('organization_id', $organization->id))
            ->where('is_approved', false)->count();
        if ($pendingReviews) {
            $tasks[] = ['count' => $pendingReviews, 'label' => 'Bewertung(en) warten auf Moderation', 'tone' => 'purple',
                'url' => route('organizer.reviews.index', ['status' => 'pending'])];
        }

        $waiting = (int) \App\Models\EventWaitlist::whereHas('event', $upcoming)->where('status', 'waiting')->sum('quantity');

        // Veranstaltungen der nächsten 7 Tage mit Auslastung und direktem Check-in
        $soon = $organization->events()
            ->where('is_cancelled', false)
            ->whereBetween('start_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->orderBy('start_date')
            ->get()
            ->map(function (\App\Models\Event $event) {
                $booked = \App\Models\BookingItem::whereHas('booking', fn (\Illuminate\Database\Eloquent\Builder $q) => $q->where('event_id', $event->id)->readyForParticipation())->count();

                return [
                    'event' => $event,
                    'booked' => $booked,
                    'capacity' => $event->max_attendees,
                    'open' => $event->bookings()->whereIn('status', ['pending', 'pending_approval'])->count(),
                ];
            });

        // Fast ausgebucht (≥ 90 %) – Warteliste im Blick behalten
        $almostFull = $organization->events()
            ->where('is_cancelled', false)
            ->where('start_date', '>', now())
            ->whereNotNull('max_attendees')
            ->get()
            ->filter(fn ($e) => $e->max_attendees > 0 && ($e->max_attendees - $e->availableTickets()) / $e->max_attendees >= 0.9)
            ->values();

        return compact('tasks', 'soon', 'almostFull', 'waiting');
    }
}
