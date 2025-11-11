<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml
     */
    public function index()
    {
        $events = Event::published()
            ->where('start_date', '>', now()->subMonths(3))
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = EventCategory::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $content = view('sitemap.index', [
            'events' => $events,
            'categories' => $categories,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Generate robots.txt
     */
    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /organizer/\n";
        $content .= "Disallow: /settings/\n";
        $content .= "Disallow: /dashboard\n";
        $content .= "\n";
        $content .= "Sitemap: " . route('sitemap') . "\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}

