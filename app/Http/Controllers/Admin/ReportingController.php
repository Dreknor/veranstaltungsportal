<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Models\EventCategory;
use App\Models\Badge;
use App\Models\FeaturedEventFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $period = $request->get('period', '30days');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // Key Metrics
        $metrics = [
            'total_users' => User::count(),
            'new_users' => User::where('created_at', '>=', $startDate)->count(),
            'total_events' => Event::count(),
            'new_events' => Event::where('created_at', '>=', $startDate)->count(),
            'total_bookings' => Booking::count(),
            'new_bookings' => Booking::where('created_at', '>=', $startDate)->count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total'),
            'period_revenue' => Booking::where('payment_status', 'paid')
                ->where('created_at', '>=', $startDate)
                ->sum('total'),
        ];

        // User Growth Chart
        $userGrowth = $this->getUserGrowthData($startDate, $endDate);

        // Revenue Chart
        $revenueData = $this->getRevenueData($startDate, $endDate);

        // Category Performance
        $categoryPerformance = $this->getCategoryPerformance($startDate, $endDate);

        // Top Events
        $topEvents = $this->getTopEvents($startDate, $endDate);

        // Top Organizers
        $topOrganizers = $this->getTopOrganizers($startDate, $endDate);

        // Conversion Funnel
        $conversionFunnel = $this->getConversionFunnel($startDate, $endDate);

        return view('admin.reporting.index', compact(
            'metrics',
            'userGrowth',
            'revenueData',
            'categoryPerformance',
            'topEvents',
            'topOrganizers',
            'conversionFunnel',
            'period'
        ));
    }

    public function users(Request $request)
    {
        $period = $request->get('period', '30days');
        $startDate = $this->getStartDate($period);

        $data = [
            'user_growth' => $this->getUserGrowthData($startDate, now()),
            'user_roles' => User::select('is_organizer', DB::raw('count(*) as count'))
                ->groupBy('is_organizer')
                ->get(),
            'user_activity' => $this->getUserActivityData($startDate),
            'top_users' => User::withCount(['bookings'])
                ->orderBy('bookings_count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('admin.reporting.users', compact('data', 'period'));
    }

    public function events(Request $request)
    {
        $period = $request->get('period', '30days');
        $startDate = $this->getStartDate($period);

        $data = [
            'event_growth' => Event::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'events_by_category' => $this->getCategoryPerformance($startDate, now()),
            'events_by_type' => Event::select('event_type', DB::raw('count(*) as count'))
                ->groupBy('event_type')
                ->get(),
            'average_capacity' => Event::avg('max_attendees'),
            'published_vs_draft' => Event::select('is_published', DB::raw('count(*) as count'))
                ->groupBy('is_published')
                ->get(),
        ];

        return view('admin.reporting.events', compact('data', 'period'));
    }

    public function revenue(Request $request)
    {
        $period = $request->get('period', '30days');
        $startDate = $this->getStartDate($period);

        $data = [
            'revenue_chart' => $this->getRevenueData($startDate, now()),
            'revenue_by_category' => EventCategory::with(['events' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                    ->with(['bookings' => function ($q) use ($startDate) {
                        $q->where('payment_status', 'paid')
                            ->where('created_at', '>=', $startDate);
                    }]);
            }])
                ->get()
                ->map(function ($category) {
                    $category->bookings_sum_total = $category->events->sum(function ($event) {
                        return $event->bookings->sum('total');
                    });
                    return $category;
                })
                ->sortByDesc('bookings_sum_total')
                ->values(),
            'payment_methods' => Booking::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total) as total'))
                ->where('created_at', '>=', $startDate)
                ->where('payment_status', 'paid')
                ->groupBy('payment_method')
                ->get(),
            'payment_status_breakdown' => Booking::select('payment_status', DB::raw('count(*) as count'), DB::raw('sum(total) as total'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('payment_status')
                ->get(),
            'platform_fees' => FeaturedEventFee::where('created_at', '>=', $startDate)
                ->sum('fee_amount'),
        ];

        return view('admin.reporting.revenue', compact('data', 'period'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'all');
        $period = $request->get('period', '30days');
        $format = $request->get('format', 'csv');

        $startDate = $this->getStartDate($period);
        $endDate = now();

        if ($format === 'csv') {
            return $this->exportCSV($type, $startDate, $endDate);
        }

        return back()->with('error', 'Ungültiges Exportformat');
    }

    // Helper Methods

    private function getStartDate($period)
    {
        return match ($period) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            '365days' => now()->subDays(365),
            'ytd' => now()->startOfYear(),
            'all' => Carbon::parse('2000-01-01'),
            default => now()->subDays(30),
        };
    }

    private function getUserGrowthData($startDate, $endDate)
    {
        return User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRevenueData($startDate, $endDate)
    {
        return Booking::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('sum(total) as total')
        )
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getCategoryPerformance($startDate, $endDate)
    {
        return EventCategory::withCount(['events' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->with(['events' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->with(['bookings' => function ($q) use ($startDate, $endDate) {
                        $q->where('payment_status', 'paid')
                            ->whereBetween('created_at', [$startDate, $endDate]);
                    }]);
            }])
            ->orderBy('events_count', 'desc')
            ->get()
            ->map(function ($category) {
                $category->bookings_sum_total = $category->events->sum(function ($event) {
                    return $event->bookings->sum('total');
                });
                return $category;
            });
    }

    private function getTopEvents($startDate, $endDate)
    {
        return Event::withCount(['bookings' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->withSum(['bookings' => function ($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'paid')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total')
            ->orderBy('bookings_sum_total', 'desc')
            ->limit(10)
            ->get();
    }

    private function getTopOrganizers($startDate, $endDate)
    {
        return User::where('is_organizer', true)
            ->withCount(['events' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->with(['events' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->with(['bookings' => function ($q) use ($startDate, $endDate) {
                        $q->where('payment_status', 'paid')
                            ->whereBetween('created_at', [$startDate, $endDate]);
                    }]);
            }])
            ->get()
            ->map(function ($organizer) {
                $organizer->events_bookings_sum_total = $organizer->events->sum(function ($event) {
                    return $event->bookings->sum('total');
                });
                return $organizer;
            })
            ->sortByDesc('events_bookings_sum_total')
            ->take(10)
            ->values();
    }

    private function getConversionFunnel($startDate, $endDate)
    {
        $eventViews = Event::whereBetween('created_at', [$startDate, $endDate])->count();
        $bookingsStarted = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $bookingsCompleted = Booking::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            'event_views' => $eventViews,
            'bookings_started' => $bookingsStarted,
            'bookings_completed' => $bookingsCompleted,
            'start_rate' => $eventViews > 0 ? ($bookingsStarted / $eventViews) * 100 : 0,
            'completion_rate' => $bookingsStarted > 0 ? ($bookingsCompleted / $bookingsStarted) * 100 : 0,
        ];
    }

    private function getUserActivityData($startDate)
    {
        return [
            'active_users' => User::whereHas('bookings', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count(),
            'inactive_users' => User::whereDoesntHave('bookings', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count(),
        ];
    }

    private function exportCSV($type, $startDate, $endDate)
    {
        $filename = "report_{$type}_" . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            if ($type === 'users') {
                fputcsv($file, ['ID', 'Name', 'Email', 'Rolle', 'Registriert am', 'Buchungen', 'Umsatz']);

                User::with(['bookings' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])->chunk(100, function ($users) use ($file) {
                    foreach ($users as $user) {
                        fputcsv($file, [
                            $user->id,
                            $user->name,
                            $user->email,
                            $user->is_organizer ? 'Veranstalter' : 'Teilnehmer',
                            $user->created_at->format('d.m.Y H:i'),
                            $user->bookings->count(),
                            $user->bookings->where('payment_status', 'paid')->sum('total'),
                        ]);
                    }
                });
            } elseif ($type === 'events') {
                fputcsv($file, ['ID', 'Titel', 'Kategorie', 'Veranstalter', 'Datum', 'Typ', 'Buchungen', 'Umsatz']);

                Event::with(['category', 'organization', 'bookings' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])->chunk(100, function ($events) use ($file) {
                    foreach ($events as $event) {
                        fputcsv($file, [
                            $event->id,
                            $event->title,
                            $event->category->name ?? '',
                            $event->organization->name ?? '',
                            $event->start_date->format('d.m.Y H:i'),
                            $event->event_type,
                            $event->bookings->count(),
                            $event->bookings->where('payment_status', 'paid')->sum('total'),
                        ]);
                    }
                });
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

