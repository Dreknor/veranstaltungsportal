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
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $query = EventReview::whereHas('event', function ($q) use ($organization) {
            $q->where('organization_id', $organization->id);
        })->with(['event', 'user'])
          ->orderBy('created_at', 'desc');

        // Filter by moderation status
        match ($request->get('status')) {
            'pending' => $query->pending(),
            'approved' => $query->approved(),
            'rejected' => $query->rejected(),
            default => null,
        };

        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating !== '') {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(20)->withQueryString();

        // Get organizer's events for filter
        $events = $organization->events()->orderBy('title')->get();

        // Statistics
        $ofOrganization = fn () => EventReview::whereHas('event', fn ($q) => $q->where('organization_id', $organization->id));
        $stats = [
            'total' => $ofOrganization()->count(),
            'pending' => $ofOrganization()->pending()->count(),
            'approved' => $ofOrganization()->approved()->count(),
            'rejected' => $ofOrganization()->rejected()->count(),
        ];
        $summary = $organization->reviewSummary();

        return view('organizer.reviews.index', compact('reviews', 'events', 'stats', 'summary', 'organization'));
    }

    /**
     * Show single review for moderation
     */
    public function moderate(EventReview $review)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization || $review->event->organization_id !== $organization->id) {
            abort(403);
        }

        return view('organizer.reviews.moderate', compact('review', 'organization'));
    }

    /**
     * Approve a review
     */
    public function approve(EventReview $review)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization || $review->event->organization_id !== $organization->id) {
            abort(403);
        }

        $review->approve();

        return back()->with('success', 'Bewertung wurde freigegeben.');
    }

    /**
     * Reject a review
     */
    public function reject(EventReview $review)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization || $review->event->organization_id !== $organization->id) {
            abort(403);
        }

        $review->reject();

        return back()->with('success', 'Bewertung wurde abgelehnt.');
    }

    /**
     * Delete a review
     */
    public function destroy(EventReview $review)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization || $review->event->organization_id !== $organization->id) {
            abort(403);
        }

        $review->delete();

        return redirect()->route('organizer.reviews.index')
            ->with('success', 'Bewertung wurde gelöscht.');
    }

    /**
     * Öffentliche Antwort des Veranstalters speichern (leer = Antwort entfernen).
     */
    public function reply(Request $request, EventReview $review)
    {
        $organization = auth()->user()->currentOrganization();
        if (! $organization || $review->event->organization_id !== $organization->id) {
            abort(403);
        }

        $validated = $request->validate([
            'organizer_reply' => 'nullable|string|max:1000',
        ]);

        $reply = trim((string) ($validated['organizer_reply'] ?? ''));

        $review->update([
            'organizer_reply' => $reply !== '' ? $reply : null,
            'organizer_replied_at' => $reply !== '' ? now() : null,
        ]);

        return back()->with('success', $reply !== '' ? 'Ihre Antwort wurde gespeichert.' : 'Ihre Antwort wurde entfernt.');
    }
}
