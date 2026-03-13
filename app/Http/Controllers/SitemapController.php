<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Http\Response;
use Carbon\Carbon;

class SitemapController extends Controller
{
    private function xmlResponse(string $xml): Response
    {
        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Generate the main sitemap index
     */
    public function index(): Response
    {
        $sitemaps = [
            ['loc' => route('sitemap.static'),     'lastmod' => now()->toW3cString()],
            ['loc' => route('sitemap.events'),     'lastmod' => Carbon::parse(Event::where('is_published', true)->max('updated_at') ?? now())->toW3cString()],
            ['loc' => route('sitemap.categories'), 'lastmod' => Carbon::parse(EventCategory::max('updated_at') ?? now())->toW3cString()],
            ['loc' => route('sitemap.organizers'), 'lastmod' => Carbon::parse(User::where('is_organizer', true)->max('updated_at') ?? now())->toW3cString()],
        ];

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($sitemaps as $sitemap) {
            $xml .= "  <sitemap>\n";
            $xml .= '    <loc>' . e($sitemap['loc']) . "</loc>\n";
            $xml .= '    <lastmod>' . e($sitemap['lastmod']) . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }
        $xml .= '</sitemapindex>';

        return $this->xmlResponse($xml);
    }

    /**
     * Generate sitemap for static pages
     */
    public function static(): Response
    {
        $urls = [
            ['loc' => route('home'),               'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('events.index'),        'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => route('events.calendar'),     'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => route('help.index'),          'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => route('badges.index'),        'priority' => '0.6', 'changefreq' => 'weekly'],
            ['loc' => route('badges.leaderboard'),  'priority' => '0.6', 'changefreq' => 'daily'],
        ];

        return $this->xmlResponse($this->buildUrlset($urls));
    }

    /**
     * Generate sitemap for events
     */
    public function events(): Response
    {
        $events = Event::where('is_published', true)
            ->where('start_date', '>=', now()->subMonths(3))
            ->orderBy('start_date', 'desc')
            ->limit(5000)
            ->get();

        $urls = $events->map(function ($event) {
            $isFuture   = $event->start_date->isFuture();
            $isFeatured = $event->is_featured ?? false;

            return [
                'loc'        => route('events.show', $event->slug),
                'lastmod'    => $event->updated_at->toW3cString(),
                'priority'   => $isFeatured ? '0.9' : ($isFuture ? '0.8' : '0.7'),
                'changefreq' => 'daily',
            ];
        })->toArray();

        return $this->xmlResponse($this->buildUrlset($urls));
    }

    /**
     * Generate sitemap for categories
     */
    public function categories(): Response
    {
        $categories = EventCategory::where('is_active', true)->get();

        $urls = $categories->map(function ($category) {
            return [
                'loc'        => route('events.index', ['category' => $category->slug]),
                'lastmod'    => $category->updated_at->toW3cString(),
                'priority'   => '0.7',
                'changefreq' => 'weekly',
            ];
        })->toArray();

        return $this->xmlResponse($this->buildUrlset($urls));
    }

    /**
     * Generate sitemap for organizers
     */
    public function organizers(): Response
    {
        $organizers = User::where('is_organizer', true)
            ->whereHas('organizations', function ($query) {
                $query->whereHas('events', function ($q) {
                    $q->where('is_published', true);
                });
            })
            ->get();

        $urls = $organizers->map(function ($organizer) {
            return [
                'loc'        => route('users.show', $organizer->id),
                'lastmod'    => $organizer->updated_at->toW3cString(),
                'priority'   => '0.6',
                'changefreq' => 'weekly',
            ];
        })->toArray();

        return $this->xmlResponse($this->buildUrlset($urls));
    }

    /**
     * Generate robots.txt
     */
    public function robots(): Response
    {
        $content = view('sitemap.robots')->render();

        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Build a <urlset> XML string from an array of URL entries.
     */
    private function buildUrlset(array $urls): string
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . e($url['loc']) . "</loc>\n";
            if (!empty($url['lastmod']))    $xml .= '    <lastmod>'   . e($url['lastmod'])    . "</lastmod>\n";
            if (!empty($url['changefreq'])) $xml .= '    <changefreq>'. e($url['changefreq']) . "</changefreq>\n";
            if (!empty($url['priority']))   $xml .= '    <priority>'  . e($url['priority'])   . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return $xml;
    }
}
