<!-- SEO & Social Media Meta Tags Component -->
@props(['event' => null, 'title' => null, 'description' => null, 'image' => null])

@php
    // Default values
    $pageTitle = $title ?? ($event ? $event->title : config('app.name'));
    $pageDescription = $description ?? ($event ? \Illuminate\Support\Str::limit(strip_tags($event->description ?? ''), 160) : 'Bildungsportal für Fort- und Weiterbildungen');
    $pageImage = $image ?? ($event && $event->featured_image ? \Illuminate\Support\Facades\Storage::url($event->featured_image) : asset('images/og-default.jpg'));
    $pageUrl = url()->current();

    // Event specific data
    if ($event) {
        $eventDate = $event->start_date ? $event->start_date->format('Y-m-d') : date('Y-m-d');
        $eventLocation = ($event->venue_name ?? '') . ', ' . ($event->venue_city ?? '');
    }
@endphp

<!-- Primary Meta Tags -->
<meta name="title" content="{{ $pageTitle }}">
<meta name="description" content="{{ $pageDescription }}">
<meta name="keywords" content="Fortbildung, Weiterbildung, Bildung, Event, Veranstaltung, Pädagogik, Lehrkräfte">
<meta name="author" content="{{ config('app.name') }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $event ? 'event' : 'website' }}">
<meta property="og:url" content="{{ $pageUrl }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:image" content="{{ $pageImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:locale" content="de_DE">

@if($event)
    <!-- Event specific OG tags -->
    @if($event->start_date)
    <meta property="event:start_time" content="{{ $event->start_date->toIso8601String() }}">
    @endif
    @if($event->end_date)
    <meta property="event:end_time" content="{{ $event->end_date->toIso8601String() }}">
    @endif
    @if(isset($eventLocation))
    <meta property="event:location" content="{{ $eventLocation }}">
    @endif

    @php
        $eventMinPrice = $event->getMinimumPrice();
    @endphp
    @if($eventMinPrice)
        <meta property="event:price" content="{{ $eventMinPrice }}">
        <meta property="event:price_currency" content="EUR">
    @endif
@endif

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $pageUrl }}">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $pageImage }}">
<meta name="twitter:site" content="@{{ config('app.name') }}">
<meta name="twitter:creator" content="@{{ config('app.name') }}">

<!-- Schema.org for Google -->
@if($event)
@php
    $eventUser = $event->user ?? null;
    $organizationName = $eventUser ? ($eventUser->organization_name ?? $eventUser->fullName()) : config('app.name');

    $schemaData = [
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => $event->title ?? 'Event',
        'description' => $pageDescription,
        'startDate' => $event->start_date ? $event->start_date->toIso8601String() : now()->toIso8601String(),
        'endDate' => $event->end_date ? $event->end_date->toIso8601String() : now()->addHours(2)->toIso8601String(),
        'eventStatus' => 'https://schema.org/EventScheduled',
        'eventAttendanceMode' => ($event->livestream_url ?? false) ? 'https://schema.org/OnlineEventAttendanceMode' : 'https://schema.org/OfflineEventAttendanceMode',
        'location' => [
            '@type' => 'Place',
            'name' => $event->venue_name ?? 'Veranstaltungsort',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $event->venue_address ?? '',
                'addressLocality' => $event->venue_city ?? '',
                'postalCode' => $event->venue_postal_code ?? '',
                'addressCountry' => $event->venue_country ?? 'Deutschland',
            ],
        ],
        'image' => $pageImage,
        'organizer' => [
            '@type' => 'Organization',
            'name' => $organizationName,
            'url' => $event->organization?->website ?? url('/'),
        ],
        'performer' => [
            '@type' => 'Organization',
            'name' => $organizationName,
        ],
    ];

    $eventMinPrice = $event->getMinimumPrice();
    if ($eventMinPrice) {
        $schemaData['offers'] = [
            '@type' => 'Offer',
            'url' => route('events.show', $event->slug ?? 'event'),
            'price' => $eventMinPrice,
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock',
            'validFrom' => now()->toIso8601String(),
        ];
    }
@endphp
<script type="application/ld+json">
{!! json_encode($schemaData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
@else
@php
    $schemaData = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => config('app.name'),
        'url' => url('/'),
        'description' => $pageDescription,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => url('/events') . '?search={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($schemaData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
@endif

<!-- Canonical URL -->
<link rel="canonical" href="{{ $pageUrl }}">

<!-- Robots -->
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">
<meta name="bingbot" content="index, follow">
