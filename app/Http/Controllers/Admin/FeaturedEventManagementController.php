<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedEventFee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeaturedEventManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']);
    }

    /**
     * Display all featured event fees
     */
    public function index(Request $request)
    {
        $query = FeaturedEventFee::with(['event', 'event.organizer'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('payment_status', $request->status);
        }

        // Filter by duration
        if ($request->has('duration') && $request->duration !== '') {
            $query->where('duration_type', $request->duration);
        }

        // Search by event name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('event', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $fees = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => FeaturedEventFee::count(),
            'active' => FeaturedEventFee::where('payment_status', 'paid')
                ->where('featured_end_date', '>', now())
                ->count(),
            'pending' => FeaturedEventFee::where('payment_status', 'pending')->count(),
            'revenue' => FeaturedEventFee::where('payment_status', 'paid')
                ->sum('fee_amount'),
            'pending_revenue' => FeaturedEventFee::where('payment_status', 'pending')
                ->sum('fee_amount'),
        ];

        return view('admin.featured-events.index', compact('fees', 'stats'));
    }

    /**
     * Show details of a specific featured event fee
     */
    public function show(FeaturedEventFee $fee)
    {
        $fee->load(['event', 'event.organizer']);

        return view('admin.featured-events.show', compact('fee'));
    }

    /**
     * Update payment status
     */
    public function updateStatus(Request $request, FeaturedEventFee $fee)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $oldStatus = $fee->payment_status;
        $fee->payment_status = $request->payment_status;

        // If marking as paid, set paid_at timestamp
        if ($request->payment_status === 'paid' && $oldStatus !== 'paid') {
            $fee->paid_at = now();
        }

        $fee->save();

        // Update event featured status
        if ($request->payment_status === 'paid' && \Carbon\Carbon::parse($fee->featured_end_date)->isFuture()) {
            $fee->event->update(['featured' => true]);
        } elseif ($request->payment_status !== 'paid') {
            $fee->event->update(['featured' => false]);
        }

        return redirect()
            ->route('admin.featured-events.show', $fee)
            ->with('success', 'Zahlungsstatus erfolgreich aktualisiert.');
    }

    /**
     * Cancel a featured event fee
     */
    public function cancel(FeaturedEventFee $fee)
    {
        if ($fee->payment_status === 'paid') {
            return back()->with('error', 'Bezahlte Featured-Gebühren können nicht storniert werden. Bitte erstellen Sie eine Rückerstattung.');
        }

        $fee->payment_status = 'failed';
        $fee->save();

        // Remove featured status from event
        $fee->event->update(['featured' => false]);

        return redirect()
            ->route('admin.featured-events.index')
            ->with('success', 'Featured Event Gebühr wurde storniert.');
    }

    /**
     * Extend a featured event period
     */
    public function extend(Request $request, FeaturedEventFee $fee)
    {
        $request->validate([
            'duration_type' => 'required|in:daily,weekly,monthly',
            'duration_count' => 'required|integer|min:1|max:365',
        ]);

        $currentEndDate = \Carbon\Carbon::parse($fee->featured_end_date);

        $newExpiry = match ($request->duration_type) {
            'daily' => $currentEndDate->addDays($request->duration_count),
            'weekly' => $currentEndDate->addWeeks($request->duration_count),
            'monthly' => $currentEndDate->addMonths($request->duration_count),
        };

        $fee->featured_end_date = $newExpiry->format('Y-m-d');
        $fee->save();

        return redirect()
            ->route('admin.featured-events.show', $fee)
            ->with('success', 'Featured Event Zeitraum wurde verlängert bis ' . $newExpiry->format('d.m.Y H:i'));
    }

    /**
     * Show statistics page
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays((int) $period);

        // Revenue over time
        $revenueByDay = FeaturedEventFee::where('payment_status', 'paid')
            ->where('paid_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(fee_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // By duration type
        $byDuration = FeaturedEventFee::where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->select('duration_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(fee_amount) as total'))
            ->groupBy('duration_type')
            ->get();

        // Top organizers
        $topOrganizers = FeaturedEventFee::where('payment_status', 'paid')
            ->with('event.organizer')
            ->where('created_at', '>=', $startDate)
            ->select('event_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(fee_amount) as total'))
            ->groupBy('event_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return view('admin.featured-events.statistics', compact(
            'revenueByDay',
            'byDuration',
            'topOrganizers',
            'period'
        ));
    }

    /**
     * Send payment reminder
     */
    public function sendReminder(FeaturedEventFee $fee)
    {
        if ($fee->payment_status !== 'pending') {
            return back()->with('error', 'Zahlungserinnerungen können nur für ausstehende Zahlungen versendet werden.');
        }

        // Send reminder email
        $fee->event->organizer->notify(new \App\Notifications\FeaturedEventPaymentReminderNotification($fee));

        return back()->with('success', 'Zahlungserinnerung wurde versendet.');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_paid,mark_failed,send_reminder',
            'fee_ids' => 'required|array',
            'fee_ids.*' => 'exists:featured_event_fees,id',
        ]);

        $fees = FeaturedEventFee::whereIn('id', $request->fee_ids)->get();

        foreach ($fees as $fee) {
            switch ($request->action) {
                case 'mark_paid':
                    if ($fee->payment_status === 'pending') {
                        $fee->update([
                            'payment_status' => 'paid',
                            'paid_at' => now(),
                        ]);
                        if ($fee->expires_at->isFuture()) {
                            $fee->event->update(['featured' => true]);
                        }
                    }
                    break;

                case 'mark_failed':
                    if ($fee->payment_status === 'pending') {
                        $fee->update(['payment_status' => 'failed']);
                        $fee->event->update(['featured' => false]);
                    }
                    break;

                case 'send_reminder':
                    if ($fee->payment_status === 'pending') {
                        $fee->event->organizer->notify(new \App\Notifications\FeaturedEventPaymentReminderNotification($fee));
                    }
                    break;
            }
        }

        return back()->with('success', 'Bulk-Aktion erfolgreich ausgeführt.');
    }
}

