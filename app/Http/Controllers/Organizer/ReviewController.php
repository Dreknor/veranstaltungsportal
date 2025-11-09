<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display reviews for organizer's events
     */
    public function index(Request $request)
    {
        $query = EventReview::whereHas('event', function ($q) {
            $q->where('user_id', auth()->id());
        })->with(['event', 'user'])
          ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating !== '') {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(20);

        // Get organizer's events for filter
        $events = auth()->user()->events()->orderBy('title')->get();

        // Statistics
        $stats = [
            'total' => EventReview::whereHas('event', function ($q) {
                $q->where('user_id', auth()->id());
            })->count(),
            'pending' => EventReview::whereHas('event', function ($q) {
                $q->where('user_id', auth()->id());
            })->where('is_approved', false)->count(),
            'approved' => EventReview::whereHas('event', function ($q) {
                $q->where('user_id', auth()->id());
            })->where('is_approved', true)->count(),
            'average_rating' => round(EventReview::whereHas('event', function ($q) {
                $q->where('user_id', auth()->id());
            })->where('is_approved', true)->avg('rating'), 1),
        ];

        return view('organizer.reviews.index', compact('reviews', 'events', 'stats'));
    }

    /**
     * Show single review for moderation
     */
    public function moderate(EventReview $review)
    {
        // Check if review belongs to organizer's event
        if ($review->event->user_id !== auth()->id()) {
            abort(403);
        }

        return view('organizer.reviews.moderate', compact('review'));
    }

    /**
     * Approve a review
     */
    public function approve(EventReview $review)
    {
        // Check if review belongs to organizer's event
        if ($review->event->user_id !== auth()->id()) {
            abort(403);
        }

        $review->update(['is_approved' => true]);

        // Update event average rating
        $this->updateEventAverageRating($review->event);

        return back()->with('success', 'Bewertung wurde freigegeben.');
    }

    /**
     * Reject a review
     */
    public function reject(EventReview $review)
    {
        // Check if review belongs to organizer's event
        if ($review->event->user_id !== auth()->id()) {
            abort(403);
        }

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
        // Check if review belongs to organizer's event
        if ($review->event->user_id !== auth()->id()) {
            abort(403);
        }

        $event = $review->event;
        $review->delete();

        // Update event average rating
        $this->updateEventAverageRating($event);

        return redirect()->route('organizer.reviews.index')
            ->with('success', 'Bewertung wurde gelöscht.');
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

