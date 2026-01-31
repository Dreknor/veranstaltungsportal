<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Http\Response;
use Carbon\Carbon;

class SitemapController extends Controller
{
    /**
     * Generate the main sitemap index
     */
    public function index(): Response
    {
        $sitemaps = [
            ['loc' => route('sitemap.static'), 'lastmod' => now()->toW3cString()],
            ['loc' => route('sitemap.events'), 'lastmod' => Event::where('published', true)->max('updated_at')?->toW3cString() ?? now()->toW3cString()],
            ['loc' => route('sitemap.categories'), 'lastmod' => EventCategory::max('updated_at')?->toW3cString() ?? now()->toW3cString()],
            ['loc' => route('sitemap.organizers'), 'lastmod' => User::where('is_organizer', true)->max('updated_at')?->toW3cString() ?? now()->toW3cString()],
        ];

        return response()
            ->view('sitemap.index', ['sitemaps' => $sitemaps])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for static pages
     */
    public function static(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('events.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => route('events.calendar'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => route('help.index'), 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => route('badges.index'), 'priority' => '0.6', 'changefreq' => 'weekly'],
            ['loc' => route('badges.leaderboard'), 'priority' => '0.6', 'changefreq' => 'daily'],
        ];

        return response()
            ->view('sitemap.urlset', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for events
     */
    public function events(): Response
    {
        // Include upcoming events and recent past events (last 3 months)
        $events = Event::where('published', true)
            ->where('start_date', '>=', now()->subMonths(3))
            ->orderBy('start_date', 'desc')
            ->limit(5000) // Google sitemap limit
            ->get();

        $urls = $events->map(function ($event) {
            $isFuture = $event->start_date->isFuture();
            $isFeatured = $event->is_featured ?? false;

            return [
                'loc' => route('events.show', $event->slug),
                'lastmod' => $event->updated_at->toW3cString(),
                'priority' => $isFeatured ? '0.9' : ($isFuture ? '0.8' : '0.7'),
                'changefreq' => 'daily' ,
            ];
        })->toArray();

        return response()
            ->view('sitemap.urlset', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for categories
     */
    public function categories(): Response
    {
        $categories = EventCategory::where('is_active', true)->get();

        $urls = $categories->map(function ($category) {
            return [
                'loc' => route('events.index', ['category' => $category->slug]),
                'lastmod' => $category->updated_at->toW3cString(),
                'priority' => '0.7',
                'changefreq' => 'weekly',
            ];
        })->toArray();

        return response()
            ->view('sitemap.urlset', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap for organizers
     */
    public function organizers(): Response
    {
        $organizers = User::where('is_organizer', true)
            ->whereHas('organizedEvents', function ($query) {
                $query->where('published', true);
            })
            ->get();

        $urls = $organizers->map(function ($organizer) {
            return [
                'loc' => route('user.profile', $organizer->id),
                'lastmod' => $organizer->updated_at->toW3cString(),
                'priority' => '0.6',
                'changefreq' => 'weekly',
            ];
        })->toArray();

        return response()
            ->view('sitemap.urlset', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate robots.txt
     */
    public function robots(): Response
    {
        $content = view('sitemap.robots')->render();

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}

