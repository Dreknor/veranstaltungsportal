<!-- SEO & Social Media Meta Tags Component -->
@props(['event' => null, 'title' => null, 'description' => null, 'image' => null, 'breadcrumbs' => null, 'canonical' => null])

@php
    // Default values
    $pageTitle = $title ?? ($event ? $event->title . ' - ' . config('app.name') : config('app.name') . ' - Fort- und Weiterbildungen für Bildungseinrichtungen');
    $pageDescription = $description ?? ($event ? \Illuminate\Support\Str::limit(strip_tags($event->description ?? ''), 160) : 'Entdecken Sie hochwertige Fort- und Weiterbildungen für evangelische Schulen und Bildungseinrichtungen. Schwerpunkt: Aktion Hauptfach Mensch und pädagogische Exzellenz.');
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
@php
    $keywords = ['Fortbildung', 'Weiterbildung', 'Bildung', 'Event', 'Veranstaltung', 'Pädagogik', 'Lehrkräfte', 'evangelische Schulen', 'Hauptfach Mensch'];
    if ($event) {
        if ($event->category) {
            $keywords[] = $event->category->name;
        }
        if ($event->venue_city) {
            $keywords[] = $event->venue_city;
        }
        $keywords[] = $event->event_type === 'online' ? 'Online-Event' : 'Präsenz-Event';
    }
    $keywordString = implode(', ', array_unique($keywords));
@endphp
<meta name="keywords" content="{{ $keywordString }}">
<meta name="author" content="{{ config('app.name') }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#4F46E5">
<meta name="language" content="de">
<meta name="geo.region" content="DE">
@if($event && $event->venue_city)
<meta name="geo.placename" content="{{ $event->venue_city }}">
@endif
@if($event && $event->venue_latitude && $event->venue_longitude)
<meta name="geo.position" content="{{ $event->venue_latitude }};{{ $event->venue_longitude }}">
<meta name="ICBM" content="{{ $event->venue_latitude }}, {{ $event->venue_longitude }}">
@endif

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

    // Determine event status
    $eventStatus = 'https://schema.org/EventScheduled';
    if ($event->is_cancelled ?? false) {
        $eventStatus = 'https://schema.org/EventCancelled';
    } elseif ($event->end_date && $event->end_date->isPast()) {
        $eventStatus = 'https://schema.org/EventScheduled'; // Keep as scheduled even if past
    }

    $schemaData = [
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => $event->title ?? 'Event',
        'description' => $pageDescription,
        'startDate' => $event->start_date ? $event->start_date->toIso8601String() : now()->toIso8601String(),
        'endDate' => $event->end_date ? $event->end_date->toIso8601String() : now()->addHours(2)->toIso8601String(),
        'eventStatus' => $eventStatus,
        'eventAttendanceMode' => ($event->event_type === 'online') ? 'https://schema.org/OnlineEventAttendanceMode' : 'https://schema.org/OfflineEventAttendanceMode',
        'image' => [$pageImage],
        'organizer' => [
            '@type' => 'Organization',
            'name' => $organizationName,
            'url' => $event->organization?->website ?? url('/'),
        ],
    ];

    // Location handling
    if ($event->event_type === 'online') {
        $schemaData['location'] = [
            '@type' => 'VirtualLocation',
            'url' => $event->meeting_link ?? url('/'),
        ];
    } else {
        $schemaData['location'] = [
            '@type' => 'Place',
            'name' => $event->venue_name ?? 'Veranstaltungsort',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $event->venue_address ?? '',
                'addressLocality' => $event->venue_city ?? '',
                'postalCode' => $event->venue_postal_code ?? '',
                'addressCountry' => $event->venue_country ?? 'DE',
            ],
        ];

        // Add geo coordinates if available
        if ($event->venue_latitude && $event->venue_longitude) {
            $schemaData['location']['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $event->venue_latitude,
                'longitude' => $event->venue_longitude,
            ];
        }
    }

    // Add performer
    $schemaData['performer'] = [
        '@type' => 'Organization',
        'name' => $organizationName,
    ];

    // Add offers/tickets
    $eventMinPrice = $event->getMinimumPrice();
    if ($eventMinPrice !== null && $eventMinPrice > 0) {
        $schemaData['offers'] = [
            '@type' => 'Offer',
            'url' => route('events.show', $event->slug ?? 'event'),
            'price' => $eventMinPrice,
            'priceCurrency' => 'EUR',
            'availability' => ($event->isFullyBooked() ?? false) ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
            'validFrom' => now()->toIso8601String(),
        ];
    } elseif ($eventMinPrice === 0) {
        $schemaData['offers'] = [
            '@type' => 'Offer',
            'url' => route('events.show', $event->slug ?? 'event'),
            'price' => 0,
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock',
        ];
        $schemaData['isAccessibleForFree'] = true;
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

<!-- Organization Schema -->
@php
    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => config('app.name'),
        'url' => url('/'),
        'logo' => asset('images/logo.png'),
        'description' => 'Bildungsportal für Fort- und Weiterbildungen an evangelischen Schulen und Bildungseinrichtungen',
        'sameAs' => [
            // Social Media Links können hier hinzugefügt werden
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($organizationSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<!-- Breadcrumb Schema -->
@if($breadcrumbs)
@php
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => []
    ];

    foreach ($breadcrumbs as $index => $breadcrumb) {
        $breadcrumbSchema['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $breadcrumb['name'],
            'item' => $breadcrumb['url'] ?? null,
        ];
    }
@endphp
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif

<!-- Canonical URL -->
@php $canonicalUrl = $canonical ?? $pageUrl; @endphp
<link rel="canonical" href="{{ $canonicalUrl }}">

<!-- Hreflang für Deutsch -->
<link rel="alternate" hreflang="de" href="{{ $canonicalUrl }}" />
<link rel="alternate" hreflang="x-default" href="{{ $canonicalUrl }}" />

<!-- Robots -->
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">
<meta name="bingbot" content="index, follow">
