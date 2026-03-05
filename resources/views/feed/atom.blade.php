<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:georss="http://www.georss.org/georss">

    {{-- ── Feed-Metadaten ──────────────────────────────────────────────── --}}
    <id>{{ $feedId }}</id>
    <title type="text">{{ $feedTitle }}</title>
    <subtitle type="text">Anstehende Veranstaltungen auf {{ config('app.name') }}</subtitle>
    <updated>{{ $lastModified->toAtomString() }}</updated>
    <generator uri="{{ $siteUrl }}" version="1.0">{{ config('app.name') }}</generator>
    <rights>© {{ now()->year }} {{ config('app.name') }}</rights>

    <link rel="self" type="application/atom+xml" href="{{ $feedUrl }}" />
    <link rel="alternate" type="text/html" href="{{ route('events.index') }}" />

    <author>
        <name>{{ config('app.name') }}</name>
        <uri>{{ $siteUrl }}</uri>
    </author>

    {{-- ── Einträge ─────────────────────────────────────────────────────── --}}
    @foreach($events as $event)
    <entry>

        {{-- RFC 4287 Pflichtfelder --}}
        <id>{{ route('events.show', $event->slug) }}</id>
        <title type="text">{{ e($event->title) }}</title>
        <updated>{{ $event->updated_at->toAtomString() }}</updated>

        {{-- Empfohlene Felder --}}
        <published>{{ $event->created_at->toAtomString() }}</published>

        <link rel="alternate" type="text/html"
              href="{{ route('events.show', $event->slug) }}" />

        @if($event->featured_image)
        <link rel="enclosure"
              type="image/jpeg"
              href="{{ \Illuminate\Support\Facades\Storage::url($event->featured_image) }}" />
        @endif

        <author>
            <name>{{ e($event->organization?->name ?? config('app.name')) }}</name>
            @if($event->organization?->website)
            <uri>{{ e($event->organization->website) }}</uri>
            @endif
        </author>

        @if($event->category)
        <category term="{{ e($event->category->slug) }}"
                  label="{{ e($event->category->name) }}"
                  scheme="{{ route('feed.atom.category', $event->category->slug) }}" />
        @endif

        <summary type="text">{{ e(\Illuminate\Support\Str::limit(strip_tags($event->description ?? ''), 500)) }}</summary>

        <content type="html"><![CDATA[
            <p><strong>Datum:</strong>
                {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                @if($event->end_date)
                    bis {{ $event->end_date->format('d.m.Y H:i') }} Uhr
                @endif
            </p>

            @if($event->event_type !== 'online')
            <p><strong>Ort:</strong>
                {{ e($event->venue_name) }}{{ $event->venue_name ? ', ' : '' }}{{ e($event->venue_address) }}{{ $event->venue_address ? ', ' : '' }}{{ e($event->venue_postal_code) }} {{ e($event->venue_city) }}{{ $event->venue_city ? ', ' : '' }}{{ e($event->venue_country) }}
            </p>
            @endif

            <p><strong>Typ:</strong>
                @switch($event->event_type)
                    @case('physical') Präsenzveranstaltung @break
                    @case('online') Online-Veranstaltung @break
                    @case('hybrid') Hybrid (Präsenz + Online) @break
                    @default {{ e($event->event_type) }}
                @endswitch
            </p>

            @if($event->price_from !== null)
            <p><strong>Preis:</strong>
                @if((float)$event->price_from === 0.0)
                    Kostenlos
                @else
                    Ab {{ number_format((float)$event->price_from, 2, ',', '.') }}&nbsp;€
                @endif
            </p>
            @endif

            @if($event->is_featured)
            <p><em>&#11088; Empfohlene Veranstaltung</em></p>
            @endif
        ]]></content>

        {{-- Geo-Koordinaten: nur für physische/hybride Events mit Koordinaten --}}
        @if(in_array($event->event_type, ['physical', 'hybrid']) && $event->venue_latitude && $event->venue_longitude)
        <georss:point>{{ $event->venue_latitude }} {{ $event->venue_longitude }}</georss:point>
        @endif

    </entry>
    @endforeach

</feed>

