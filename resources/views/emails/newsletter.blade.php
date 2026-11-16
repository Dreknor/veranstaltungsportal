@extends('emails.layout')

@php
    $h2 =
        'margin: 32px 0 0 0; padding: 0 0 8px 0; border-bottom: 1px solid #DCE3E2; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;';
    $sections = [
        [
            'title' => 'Highlights der Woche',
            'events' => $featuredEvents,
            'link' => 'Jetzt anmelden',
            'description' => true,
        ],
        [
            'title' => 'Passend zu Ihren Interessen',
            'events' => $recommendations,
            'link' => 'Mehr erfahren',
            'description' => false,
        ],
        [
            'title' => 'Kommende Veranstaltungen',
            'events' => $upcomingEvents->take(5),
            'link' => 'Details ansehen',
            'description' => false,
        ],
    ];
@endphp

@section('title', 'Newsletter')

@section('heading')
    Ihr {{ $type === 'weekly' ? 'wöchentlicher' : 'monatlicher' }} Newsletter
@endsection

@section('content')
    <p style="margin: 0 0 16px 0;">
        Hallo {{ $subscriber->fullName() }},
    </p>

    <p style="margin: 0;">
        wir haben wieder spannende Bildungsangebote für Sie zusammengestellt!
    </p>

    @foreach ($sections as $section)
        @if ($section['events']->count() > 0)
            <h2 style="{{ $h2 }}">{{ $section['title'] }}</h2>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                @foreach ($section['events'] as $event)
                    <tr>
                        <td style="padding: 16px 0; border-bottom: 1px solid #DCE3E2;">
                            @if ($event->category)
                                <p style="margin: 0 0 4px 0; font-size: 13px; line-height: 1.3; color: #55676A;">
                                    {{ $event->category->name }}</p>
                            @endif
                            <p
                                style="margin: 0 0 4px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;">
                                {{ $event->title }}</p>
                            <p
                                style="margin: 0; font-size: 14px; line-height: 1.5; color: #55676A; font-variant-numeric: tabular-nums;">
                                @if ($event->start_date->isSameDay($event->end_date))
                                    {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                                @else
                                    {{ $event->start_date->format('d.m.Y') }} – {{ $event->end_date->format('d.m.Y') }}
                                @endif
                                · {{ $event->isOnline() ? 'Online-Veranstaltung' : $event->venue_name }}
                            </p>
                            @if ($section['description'])
                                <p style="margin: 8px 0 0 0; font-size: 15px; line-height: 1.5; color: #1E3135;">
                                    {{ Str::limit(strip_tags($event->description), 120) }}
                                </p>
                            @endif
                            <p style="margin: 8px 0 0 0; font-size: 15px; font-weight: 600;">
                                <a href="{{ route('events.show', $event) }}"
                                    style="color: #1E3135; text-decoration: underline;">{{ $section['link'] }}</a>
                            </p>
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endforeach

    <p style="margin: 32px 0 0 0;">
        Viel Freude beim Entdecken und Lernen!<br>
        Ihr {{ config('app.name') }}-Team
    </p>
@endsection

@section('footer_note')
    Sie erhalten diese E-Mail, weil Sie unseren Newsletter abonniert haben.<br>
    <a href="{{ route('settings.interests.edit') }}" style="color: #55676A; text-decoration: underline;">Interessen
        verwalten</a> ·
    <a href="{{ route('newsletter.unsubscribe') }}" style="color: #55676A; text-decoration: underline;">Abmelden</a>
@endsection
