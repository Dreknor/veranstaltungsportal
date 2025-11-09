<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventReview;
use Illuminate\Http\Request;

class ReviewManagementController extends Controller
{
    /**
     * Display a listing of reviews
     */
    public function index(Request $request)
    {
        $query = EventReview::with(['event', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter by approval status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating !== '') {
            $query->where('rating', $request->rating);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('event', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $reviews = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => EventReview::count(),
            'pending' => EventReview::where('is_approved', false)->count(),
            'approved' => EventReview::where('is_approved', true)->count(),
            'average_rating' => round(EventReview::where('is_approved', true)->avg('rating'), 1),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Approve a review
     */
    public function approve(EventReview $review)
    {
        $review->update(['is_approved' => true]);

        // Update event average rating
        $this->updateEventAverageRating($review->event);

        return back()->with('success', 'Bewertung wurde freigegeben.');
    }

    /**
     * Reject/Unapprove a review
     */
    public function reject(EventReview $review)
    {
        $review->update(['is_approved' => false]);

        // Update event average rating
        $this->updateEventAverageRating($review->event);

        return back()->with('success', 'Bewertung wurde abgelehnt.');
    }

    /**
     * Delete a review
     */
    public function destroy(EventReview $review)
    {
        $event = $review->event;
        $review->delete();

        // Update event average rating
        $this->updateEventAverageRating($event);

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

        $reviews = EventReview::whereIn('id', $request->review_ids)->get();

        foreach ($reviews as $review) {
            $review->update(['is_approved' => true]);
            $this->updateEventAverageRating($review->event);
        }

        return back()->with('success', count($request->review_ids) . ' Bewertungen wurden freigegeben.');
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

        EventReview::whereIn('id', $request->review_ids)->update(['is_approved' => false]);

        return back()->with('success', count($request->review_ids) . ' Bewertungen wurden abgelehnt.');
    }

    /**
     * Update event average rating
     */
    private function updateEventAverageRating($event)
    {
        $averageRating = $event->reviews()->where('is_approved', true)->avg('rating');
        $event->update(['average_rating' => $averageRating]);
    }
}

