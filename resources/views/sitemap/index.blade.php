<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    <!-- Homepage -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Events Index -->
    <url>
        <loc>{{ route('events.index') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Events Calendar -->
    <url>
        <loc>{{ route('events.calendar') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Categories -->
    @foreach($categories as $category)
    <url>
        <loc>{{ route('events.index', ['category' => $category->id]) }}</loc>
        <lastmod>{{ $category->updated_at->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    <!-- Events -->
    @foreach($events as $event)
    <url>
        <loc>{{ route('events.show', $event) }}</loc>
        <lastmod>{{ $event->updated_at->toIso8601String() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        @if($event->featured_image)
        <image:image>
            <image:loc>{{ asset('storage/' . $event->featured_image) }}</image:loc>
            <image:title>{{ $event->title }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach

    <!-- Static Pages -->
    <url>
        <loc>{{ url('/about') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    <url>
        <loc>{{ url('/contact') }}</loc>
        <lastmod>{{ now()->toIso8601String() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

</urlset>

