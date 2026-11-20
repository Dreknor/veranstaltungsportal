<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventReview;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewManagementController extends Controller
{
    /**
     * Ab diesem Anteil abgelehnter Bewertungen wird ein Veranstalter hervorgehoben.
     * Abgelehnt werden darf nur bei Regelverstoß – eine hohe Quote deutet auf „Schönmoderieren“ hin.
     */
    public const REJECTION_ALERT_PERCENT = 20;

    /**
     * Display a listing of reviews
     */
    public function index(Request $request)
    {
        $query = EventReview::with(['event.organization', 'user', 'booking'])
            ->orderBy('created_at', 'desc');

        // Filter by moderation status
        match ($request->get('status')) {
            'pending' => $query->pending(),
            'approved' => $query->approved(),
            'rejected' => $query->rejected(),
            default => null,
        };

        // Filter by rating
        if ($request->has('rating') && $request->rating !== '') {
            $query->where('rating', $request->rating);
        }

        // Filter by organization
        if ($request->filled('organization_id')) {
            $query->whereHas('event', fn ($q) => $q->where('organization_id', $request->organization_id));
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('event', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => EventReview::count(),
            'pending' => EventReview::pending()->count(),
            'approved' => EventReview::approved()->count(),
            'rejected' => EventReview::rejected()->count(),
            'average_rating' => round((float) EventReview::approved()->avg('rating'), 1),
        ];

        $moderation = $this->moderationByOrganization();

        return view('admin.reviews.index', compact('reviews', 'stats', 'moderation'));
    }

    /**
     * Ablehnungsquote je Veranstalter – nur Veranstalter mit abgelehnten Bewertungen.
     *
     * @return \Illuminate\Support\Collection<int, object{organization: Organization|null, organization_id: int, decided: int, rejected: int, percent: int}>
     */
    private function moderationByOrganization()
    {
        $rows = DB::table('event_reviews')
            ->join('events', 'events.id', '=', 'event_reviews.event_id')
            ->whereNotNull('events.organization_id')
            ->groupBy('events.organization_id')
            ->selectRaw('events.organization_id,
                sum(case when event_reviews.is_approved = 1 then 1 else 0 end) as approved,
                sum(case when event_reviews.is_approved = 0 and event_reviews.rejected_at is not null then 1 else 0 end) as rejected')
            ->havingRaw('sum(case when event_reviews.is_approved = 0 and event_reviews.rejected_at is not null then 1 else 0 end) > 0')
            ->get();

        $organizations = Organization::withTrashed()->whereIn('id', $rows->pluck('organization_id'))->get()->keyBy('id');

        return $rows->map(function ($row) use ($organizations) {
            $decided = (int) $row->approved + (int) $row->rejected;

            return (object) [
                'organization' => $organizations->get($row->organization_id),
                'organization_id' => (int) $row->organization_id,
                'decided' => $decided,
                'rejected' => (int) $row->rejected,
                'percent' => $decided > 0 ? (int) round($row->rejected / $decided * 100) : 0,
            ];
        })->sortByDesc('percent')->values();
    }

    /**
     * Approve a review
     */
    public function approve(EventReview $review)
    {
        $review->approve();

        return back()->with('success', 'Bewertung wurde freigegeben.');
    }

    /**
     * Reject/Unapprove a review
     */
    public function reject(EventReview $review)
    {
        $review->reject();

        return back()->with('success', 'Bewertung wurde abgelehnt.');
    }

    /**
     * Delete a review
     */
    public function destroy(EventReview $review)
    {
        $review->delete();

        return back()->with('success', 'Bewertung wurde gelöscht.');
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:event_reviews,id',
        ]);

        EventReview::whereIn('id', $request->review_ids)->get()->each->approve();

        return back()->with('success', count($request->review_ids).' Bewertungen wurden freigegeben.');
    }

    /**
     * Bulk reject reviews
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:event_reviews,id',
        ]);

        EventReview::whereIn('id', $request->review_ids)->get()->each->reject();

        return back()->with('success', count($request->review_ids).' Bewertungen wurden abgelehnt.');
    }
}
