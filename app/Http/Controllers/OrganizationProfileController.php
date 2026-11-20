<?php

namespace App\Http\Controllers;

use App\Models\EventReview;
use App\Models\Organization;
use App\Support\ReviewSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Öffentliches Veranstalter-Profil: aktuelle und vergangene Veranstaltungen sowie Bewertungen.
 * Hier werden Bewertungen vergangener Veranstaltungen sichtbar, die selbst nicht mehr gelistet sind.
 */
class OrganizationProfileController extends Controller
{
    public const VIEWS = ['aktuell', 'vergangen', 'bewertungen'];

    public function show(Request $request, Organization $organization): View
    {
        abort_unless($organization->hasPublicProfile(), 404);

        $view = in_array($request->query('ansicht'), self::VIEWS, true) ? $request->query('ansicht') : 'aktuell';

        $publicEvents = fn () => $organization->events()
            ->where('is_published', true)
            ->where('is_private', false);

        $counts = [
            'aktuell' => $publicEvents()->where('end_date', '>=', now())->count(),
            'vergangen' => $publicEvents()->where('end_date', '<', now())->count(),
            'bewertungen' => $organization->reviews()->where('event_reviews.is_approved', true)->count(),
        ];

        $upcoming = $past = $reviews = null;

        if ($view === 'aktuell') {
            $upcoming = $publicEvents()
                ->where('end_date', '>=', now())
                ->with(['category', 'ticketTypes'])
                ->orderBy('start_date')
                ->paginate(20, pageName: 'seite')
                ->withQueryString();
        } elseif ($view === 'vergangen') {
            $past = $publicEvents()
                ->where('end_date', '<', now())
                ->with(['category'])
                ->withCount(['reviews as approved_reviews_count' => fn ($q) => $q->where('is_approved', true)])
                ->withAvg(['reviews as approved_reviews_avg' => fn ($q) => $q->where('is_approved', true)], 'rating')
                ->orderByDesc('start_date')
                ->paginate(20, pageName: 'seite')
                ->withQueryString();
        } else {
            $reviews = EventReview::query()
                ->approved()
                ->whereHas('event', fn ($q) => $q->where('organization_id', $organization->id))
                ->with(['event', 'user', 'booking'])
                ->latest()
                ->paginate(10, pageName: 'seite')
                ->withQueryString();
        }

        // Für die Übersicht neben den Reitern: die neuesten Bewertungen (nur auf „aktuell“)
        $latestReviews = $view === 'aktuell'
            ? EventReview::query()
                ->approved()
                ->whereHas('event', fn ($q) => $q->where('organization_id', $organization->id))
                ->with(['event', 'user', 'booking'])
                ->latest()
                ->limit(3)
                ->get()
            : collect();

        return view('organizations.show', [
            'organization' => $organization,
            'summary' => $organization->reviewSummary(),
            'view' => $view,
            'counts' => $counts,
            'upcoming' => $upcoming,
            'past' => $past,
            'reviews' => $reviews,
            'latestReviews' => $latestReviews,
            'months' => ReviewSummary::ORGANIZATION_MONTHS,
        ]);
    }
}
